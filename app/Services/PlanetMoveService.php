<?php

namespace OGame\Services;

use Illuminate\Database\Eloquent\Collection;
use OGame\Enums\DarkMatterTransactionType;
use OGame\Factories\PlanetServiceFactory;
use OGame\Models\Planet;
use OGame\Models\Planet\Coordinate;
use OGame\Models\PlanetMove;

class PlanetMoveService
{
    /**
     * Get the active (pending) move for a planet, if any.
     */
    public function getActiveMoveForPlanet(PlanetService $planet): PlanetMove|null
    {
        return PlanetMove::where('planet_id', $planet->getPlanetId())
            ->where('canceled', false)
            ->where('processed', false)
            ->first();
    }

    /**
     * Schedule a planet move with a 24-hour countdown.
     */
    public function scheduleMoveForPlanet(PlanetService $planet, Coordinate $target): PlanetMove
    {
        return PlanetMove::create([
            'planet_id' => $planet->getPlanetId(),
            'target_galaxy' => $target->galaxy,
            'target_system' => $target->system,
            'target_position' => $target->position,
            'time_start' => time(),
            'time_arrive' => time() + 86400,
            'canceled' => false,
            'processed' => false,
        ]);
    }

    /**
     * Cancel an active planet move.
     */
    public function cancelMove(PlanetMove $move): void
    {
        $move->canceled = true;
        $move->save();
    }

    /**
     * Process a due move: re-validate conditions, deduct DM, move planet.
     * Returns true if the move was executed, false if canceled.
     */
    public function processMove(
        PlanetMove $move,
        PlanetServiceFactory $planetServiceFactory,
        DarkMatterService $darkMatterService,
        SettingsService $settingsService,
        BuildingQueueService $buildingQueueService,
        ResearchQueueService $researchQueueService,
        UnitQueueService $unitQueueService,
        FleetMissionService $fleetMissionService,
    ): bool {
        $targetCoordinate = new Coordinate($move->target_galaxy, $move->target_system, $move->target_position);

        // Re-validate target position is still empty.
        $existingPlanet = $planetServiceFactory->makePlanetForCoordinate($targetCoordinate, false);
        if ($existingPlanet !== null) {
            $this->cancelMove($move);
            return false;
        }

        // Load the planet being moved.
        $planet = $planetServiceFactory->make($move->planet_id);

        // Re-validate no active building queue.
        $buildingQueue = $buildingQueueService->retrieveQueueItems($planet);
        if ($buildingQueue->isNotEmpty()) {
            $this->cancelMove($move);
            return false;
        }

        // Re-validate no active research queue.
        $researchQueue = $researchQueueService->retrieveQueue($planet);
        if (count($researchQueue->queue) > 0) {
            $this->cancelMove($move);
            return false;
        }

        // Re-validate no active unit queue.
        $unitQueue = $unitQueueService->retrieveQueue($planet);
        if (count($unitQueue->queue) > 0) {
            $this->cancelMove($move);
            return false;
        }

        // Re-validate no active fleet missions.
        $activeMissions = $fleetMissionService->getActiveMissionsByPlanetIds([$move->planet_id]);
        if ($activeMissions->isNotEmpty()) {
            $this->cancelMove($move);
            return false;
        }

        // Re-validate DM affordability.
        $user = $planet->getPlayer()->getUser();
        $cost = (int) $settingsService->get('planet_relocation_cost', 240000);
        if (!$darkMatterService->canAfford($user, $cost)) {
            $this->cancelMove($move);
            return false;
        }

        // Deduct DM.
        $darkMatterService->debit($user, $cost, DarkMatterTransactionType::PLANET_RELOCATION->value, 'Planet relocation to ' . $targetCoordinate->asString());

        // Move planet coordinates and recalculate temperature.
        $planetModel = Planet::find($move->planet_id);
        $planetModel->galaxy = $move->target_galaxy;
        $planetModel->system = $move->target_system;
        $planetModel->planet = $move->target_position;

        $planetData = $planetServiceFactory->planetData($move->target_position, false);
        $planetModel->temp_max = rand($planetData['temperature'][0], $planetData['temperature'][1]);
        $planetModel->temp_min = $planetModel->temp_max - 40;
        $planetModel->save();

        // If the planet has a moon, move it too.
        if ($planet->hasMoon()) {
            $moon = $planet->moon();
            $moonModel = Planet::find($moon->getPlanetId());
            $moonModel->galaxy = $move->target_galaxy;
            $moonModel->system = $move->target_system;
            $moonModel->planet = $move->target_position;

            $avgTemp = (int) (($planetData['temperature'][0] + $planetData['temperature'][1]) / 2);
            $moonModel->temp_max = $avgTemp;
            $moonModel->temp_min = $avgTemp - 40;
            $moonModel->save();
        }

        $move->processed = true;
        $move->save();

        return true;
    }

    /**
     * Get a list of reasons that would currently block a pending move from executing.
     * Returns an empty array if nothing is blocking.
     *
     * @return string[]
     */
    public function getBlockingReasons(
        PlanetService $planet,
        BuildingQueueService $buildingQueueService,
        ResearchQueueService $researchQueueService,
        UnitQueueService $unitQueueService,
        FleetMissionService $fleetMissionService,
    ): array {
        $reasons = [];

        $buildingQueue = $buildingQueueService->retrieveQueueItems($planet);
        if ($buildingQueue->isNotEmpty()) {
            $reasons[] = 'Buildings are being constructed on this planet';
        }

        $researchQueue = $researchQueueService->retrieveQueue($planet);
        if (count($researchQueue->queue) > 0) {
            $reasons[] = 'Research is still taking place on this planet';
        }

        $unitQueue = $unitQueueService->retrieveQueue($planet);
        if (count($unitQueue->queue) > 0) {
            $reasons[] = 'Units are being built on this planet';
        }

        $activeMissions = $fleetMissionService->getActiveMissionsByPlanetIds([$planet->getPlanetId()]);
        if ($activeMissions->isNotEmpty()) {
            $reasons[] = 'Fleet missions are still active';
        }

        return $reasons;
    }

    /**
     * Get all active moves targeting positions in a given galaxy+system.
     *
     * @return Collection<int, PlanetMove>
     */
    public function getActiveMovesForSystem(int $galaxy, int $system): Collection
    {
        return PlanetMove::where('target_galaxy', $galaxy)
            ->where('target_system', $system)
            ->where('canceled', false)
            ->where('processed', false)
            ->get();
    }

    /**
     * Process all due moves (time_arrive <= now, not canceled, not processed).
     */
    public function processDueMoves(
        PlanetServiceFactory $planetServiceFactory,
        DarkMatterService $darkMatterService,
        SettingsService $settingsService,
        BuildingQueueService $buildingQueueService,
        ResearchQueueService $researchQueueService,
        UnitQueueService $unitQueueService,
        FleetMissionService $fleetMissionService,
    ): void {
        $dueMoves = PlanetMove::where('time_arrive', '<=', time())
            ->where('canceled', false)
            ->where('processed', false)
            ->get();

        foreach ($dueMoves as $move) {
            $this->processMove(
                $move,
                $planetServiceFactory,
                $darkMatterService,
                $settingsService,
                $buildingQueueService,
                $researchQueueService,
                $unitQueueService,
                $fleetMissionService,
            );
        }
    }
}
