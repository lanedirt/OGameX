<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use OGame\Factories\PlanetServiceFactory;
use OGame\Models\Planet\Coordinate;
use OGame\Services\BuildingQueueService;
use OGame\Services\DarkMatterService;
use OGame\Services\FleetMissionService;
use OGame\Services\PlanetMoveService;
use OGame\Services\PlayerService;
use OGame\Services\ResearchQueueService;
use OGame\Services\SettingsService;
use OGame\Services\UnitQueueService;

class PlanetMoveController extends OGameController
{
    /**
     * Shows the notes popup page
     *
     * @return View
     */
    public function overlay(): View
    {
        // TODO: add correct template for this page.
        return view('ingame.notes.overlay');
    }

    /**
     * Schedule a planet relocation (24-hour countdown).
     */
    public function move(Request $request, PlayerService $player, PlanetServiceFactory $planetServiceFactory, DarkMatterService $darkMatterService, SettingsService $settingsService, BuildingQueueService $buildingQueueService, ResearchQueueService $researchQueueService, UnitQueueService $unitQueueService, FleetMissionService $fleetMissionService, PlanetMoveService $planetMoveService): JsonResponse
    {
        $galaxy = (int) $request->input('galaxy');
        $system = (int) $request->input('system');
        $position = (int) $request->input('position');

        $targetCoordinate = new Coordinate($galaxy, $system, $position);

        // Validate the target position is empty.
        $existingPlanet = $planetServiceFactory->makePlanetForCoordinate($targetCoordinate, false);
        if ($existingPlanet !== null) {
            return response()->json(['error' => 'The target position is not empty.']);
        }

        $planet = $player->planets->current();
        $user = $planet->getPlayer()->getUser();

        // Validate the player doesn't already have an active move for this planet.
        $activeMove = $planetMoveService->getActiveMoveForPlanet($planet);
        if ($activeMove !== null) {
            return response()->json(['error' => 'A planet relocation is already in progress.']);
        }

        // Validate the player can afford the relocation cost (check only, don't deduct yet).
        $cost = (int) $settingsService->get('planet_relocation_cost', 240000);
        if (!$darkMatterService->canAfford($user, $cost)) {
            return response()->json(['error' => 'Insufficient Dark Matter. You need ' . number_format($cost) . ' DM.']);
        }

        // Validate no active building queue on the current planet.
        $buildingQueue = $buildingQueueService->retrieveQueueItems($planet);
        if ($buildingQueue->isNotEmpty()) {
            return response()->json(['error' => 'Cannot relocate while buildings are being constructed.']);
        }

        // Validate no active research queue for this player.
        $researchQueue = $researchQueueService->retrieveQueue($planet);
        if (count($researchQueue->queue) > 0) {
            return response()->json(['error' => 'Cannot relocate while research is in progress.']);
        }

        // Validate no active unit queue on the current planet.
        $unitQueue = $unitQueueService->retrieveQueue($planet);
        if (count($unitQueue->queue) > 0) {
            return response()->json(['error' => 'Cannot relocate while units are being built.']);
        }

        // Validate no active fleet missions from/to the current planet.
        $activeMissions = $fleetMissionService->getActiveMissionsByPlanetIds([$planet->getPlanetId()]);
        if ($activeMissions->isNotEmpty()) {
            return response()->json(['error' => 'Cannot relocate while fleet missions are active.']);
        }

        // Schedule the move (DM will be deducted when the countdown expires).
        $planetMoveService->scheduleMoveForPlanet($planet, $targetCoordinate);

        return response()->json(['error' => '']);
    }

    /**
     * Cancel a pending planet relocation.
     */
    public function cancel(PlayerService $player, PlanetMoveService $planetMoveService): JsonResponse
    {
        $planet = $player->planets->current();
        $activeMove = $planetMoveService->getActiveMoveForPlanet($planet);

        if ($activeMove === null) {
            return response()->json(['error' => 'No active planet relocation found.']);
        }

        $planetMoveService->cancelMove($activeMove);

        return response()->json(['error' => '']);
    }
}
