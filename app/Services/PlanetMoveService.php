<?php

namespace OGame\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Date;
use OGame\Enums\DarkMatterTransactionType;
use OGame\Factories\PlanetServiceFactory;
use OGame\GameMessages\PlanetRelocationSuccess;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\FleetMission;
use OGame\Models\Planet;
use OGame\Models\Planet\Coordinate;
use OGame\Models\PlanetMove;

class PlanetMoveService
{
    /**
     * Get the cooldown seconds remaining before a planet can be relocated again.
     * A 24-hour cooldown applies after a move is processed or cancelled.
     */
    public function getCooldownSecondsForPlanet(PlanetService $planet): int
    {
        $lastMove = PlanetMove::where('planet_id', $planet->getPlanetId())
            ->where(function ($query) {
                $query->where('canceled', true)->orWhere('processed', true);
            })
            ->orderByDesc('updated_at')
            ->first();

        if ($lastMove === null) {
            return 0;
        }

        return (int) max(0, ((int) $lastMove->updated_at->timestamp + 86400) - (int) Date::now()->timestamp);
    }

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

        // Re-validate no active research queue on this planet.
        $researchQueue = $researchQueueService->retrieveQueueForPlanet($planet);
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

        // Save old coordinates before updating.
        $planetModel = Planet::find($move->planet_id);
        $oldCoordinate = new Coordinate($planetModel->galaxy, $planetModel->system, $planetModel->planet);

        // Move planet coordinates and recalculate temperature.
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

            // Deactivate the jump gate on the relocated moon for 24 hours.
            if ($moon->getObjectLevel('jump_gate') > 0) {
                $moon->setJumpGateCooldown((int) Date::now()->timestamp + 86400);
            }

            // Collect flyable ships from moon and create deployment fleet to transfer them.
            $allMoonShipUnits = $moon->getShipUnits();
            $moonShipUnits = new UnitCollection();
            foreach ($allMoonShipUnits->units as $unit) {
                if (!in_array($unit->unitObject->machine_name, ['solar_satellite', 'crawler'], true)) {
                    $moonShipUnits->addUnit($unit->unitObject, $unit->amount);
                }
            }
            if ($moonShipUnits->getAmount() > 0) {
                $this->createShipTransferMission($moon, $oldCoordinate, $targetCoordinate, $moonShipUnits, $fleetMissionService, $settingsService);
            }
        }

        // Collect flyable ships from planet and create deployment fleet to transfer them.
        // Filter out non-flyable units (solar satellites, crawlers) that cannot be part of fleet missions.
        $allShipUnits = $planet->getShipUnits();
        $shipUnits = new UnitCollection();
        foreach ($allShipUnits->units as $unit) {
            if (!in_array($unit->unitObject->machine_name, ['solar_satellite', 'crawler'], true)) {
                $shipUnits->addUnit($unit->unitObject, $unit->amount);
            }
        }
        if ($shipUnits->getAmount() > 0) {
            $this->createShipTransferMission($planet, $oldCoordinate, $targetCoordinate, $shipUnits, $fleetMissionService, $settingsService);
        }

        // Detach foreign fleet missions targeting the old coordinates.
        // These missions will find planet_id_to = null on arrival and return home.
        $planetIds = [$move->planet_id];
        if ($planet->hasMoon()) {
            $planetIds[] = $planet->moon()->getPlanetId();
        }
        FleetMission::whereIn('planet_id_to', $planetIds)
            ->where('user_id', '!=', $planet->getPlayer()->getId())
            ->where('processed', 0)
            ->update(['planet_id_to' => null]);

        $move->processed = true;
        $move->save();

        // Send success message to the player.
        $messageService = resolve(MessageService::class);
        $messageService->sendSystemMessageToPlayer($planet->getPlayer(), PlanetRelocationSuccess::class, [
            'planet_name' => $planet->getPlanetName(),
            'old_coordinates' => $oldCoordinate->asString(),
            'new_coordinates' => $targetCoordinate->asString(),
        ]);

        return true;
    }

    /**
     * Create a deployment fleet mission to transfer ships from old to new coordinates.
     */
    private function createShipTransferMission(
        PlanetService $planet,
        Coordinate $oldCoordinate,
        Coordinate $newCoordinate,
        UnitCollection $shipUnits,
        FleetMissionService $fleetMissionService,
        SettingsService $settingsService,
    ): void {
        // Calculate flight duration using the distance formula and slowest ship speed.
        $distance = $fleetMissionService->calculateDistance($oldCoordinate, $newCoordinate);
        $slowestSpeed = $shipUnits->getSlowestUnitSpeed($planet->getPlayer());
        $fleetSpeed = $settingsService->fleetSpeedPeaceful();
        $duration = (int) max(round((35000 / 10 * sqrt($distance * 10 / $slowestSpeed) + 10) / $fleetSpeed), 1);

        $now = (int) Date::now()->timestamp;

        // Create the fleet mission record directly (bypasses GameMission::start()).
        $mission = new FleetMission();
        $mission->user_id = $planet->getPlayer()->getId();
        $mission->planet_id_from = $planet->getPlanetId();
        $mission->planet_id_to = $planet->getPlanetId();
        $mission->galaxy_from = $oldCoordinate->galaxy;
        $mission->system_from = $oldCoordinate->system;
        $mission->position_from = $oldCoordinate->position;
        $mission->type_from = $planet->getPlanetType()->value;
        $mission->galaxy_to = $newCoordinate->galaxy;
        $mission->system_to = $newCoordinate->system;
        $mission->position_to = $newCoordinate->position;
        $mission->type_to = $planet->getPlanetType()->value;
        $mission->mission_type = 4; // Deployment
        $mission->time_departure = $now;
        $mission->time_arrival = $now + $duration;
        $mission->metal = 0;
        $mission->crystal = 0;
        $mission->deuterium = 0;
        $mission->deuterium_consumption = 0;
        $mission->processed = 0;
        $mission->canceled = 0;

        // Populate ship columns from the unit collection.
        foreach ($shipUnits->units as $unit) {
            $mission->{$unit->unitObject->machine_name} = $unit->amount;
        }

        $mission->save();

        // Remove ships from the planet.
        $planet->removeUnits($shipUnits, true);
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

        $researchQueue = $researchQueueService->retrieveQueueForPlanet($planet);
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
