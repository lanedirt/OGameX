<?php

namespace OGame\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use OGame\Http\Controllers\OGameController;
use OGame\Models\Enums\ResourceType;
use OGame\Services\ObjectService;
use OGame\Services\PlayerService;
use OGame\Models\Planet\Coordinate;
use OGame\Factories\PlanetServiceFactory;
use OGame\Models\Enums\PlanetType;

class DeveloperShortcutsController extends OGameController
{
    /**
     * Shows the developer shortcuts page.
     *
     * @return View
     */
    public function index(PlayerService $playerService): View
    {
        // Get all unit objects
        $units = ObjectService::getUnitObjects();

        return view('ingame.admin.developershortcuts')->with([
            'units' => $units,
            'currentPlanet' => $playerService->planets->current(),
        ]);
    }

    /**
     * Updates the server settings.
     *
     * @param \Illuminate\Http\Request $request
     * @param PlayerService $playerService
     * @return RedirectResponse
     * @throws \Exception
     */
    public function update(\Illuminate\Http\Request $request, PlayerService $playerService): RedirectResponse
    {
        if ($request->has('set_mines')) {
            // Handle "Set all mines to level 30"
            $playerService->planets->current()->setObjectLevel(ObjectService::getObjectByMachineName('metal_mine')->id, 30);
            $playerService->planets->current()->setObjectLevel(ObjectService::getObjectByMachineName('crystal_mine')->id, 30);
            $playerService->planets->current()->setObjectLevel(ObjectService::getObjectByMachineName('deuterium_synthesizer')->id, 30);
            $playerService->planets->current()->setObjectLevel(ObjectService::getObjectByMachineName('solar_plant')->id, 30);
        } elseif ($request->has('set_storages')) {
            // Handle "Set all storages to level 30"
            $playerService->planets->current()->setObjectLevel(ObjectService::getObjectByMachineName('metal_store')->id, 15);
            $playerService->planets->current()->setObjectLevel(ObjectService::getObjectByMachineName('crystal_store')->id, 15);
            $playerService->planets->current()->setObjectLevel(ObjectService::getObjectByMachineName('deuterium_store')->id, 15);
        } elseif ($request->has('set_shipyard')) {
            // Handle "Set all shipyard facilities to level 12"
            $playerService->planets->current()->setObjectLevel(ObjectService::getObjectByMachineName('shipyard')->id, 12);
            $playerService->planets->current()->setObjectLevel(ObjectService::getObjectByMachineName('robot_factory')->id, 12);
            $playerService->planets->current()->setObjectLevel(ObjectService::getObjectByMachineName('nano_factory')->id, 12);

        } elseif ($request->has('set_research')) {
            // Handle "Set all research to level 10"
            $playerService->planets->current()->setObjectLevel(ObjectService::getObjectByMachineName('research_lab')->id, 12);
            foreach (ObjectService::getResearchObjects() as $research) {
                $playerService->setResearchLevel($research->machine_name, 10);
            }
        } elseif ($request->has('reset_buildings')) {
            // Handle "Reset all buildings"
            foreach (ObjectService::getBuildingObjects() as $building) {
                $playerService->planets->current()->setObjectLevel($building->id, 0);
            }
            foreach (ObjectService::getStationObjects() as $building) {
                $playerService->planets->current()->setObjectLevel($building->id, 0);
            }
        } elseif ($request->has('reset_research')) {
            // Handle "Reset all research"
            foreach (ObjectService::getResearchObjects() as $research) {
                $playerService->setResearchLevel($research->machine_name, 0);
            }
        } elseif ($request->has('reset_units')) {
            // Handle "Reset all units"
            foreach (ObjectService::getUnitObjects() as $unit) {
                $playerService->planets->current()->removeUnit($unit->machine_name, $playerService->planets->current()->getObjectAmount($unit->machine_name));
            }
        } else {
            // Handle unit submission
            foreach (ObjectService::getUnitObjects() as $unit) {
                if ($request->has('unit_' . $unit->id)) {
                    // Handle adding the specific unit
                    $playerService->planets->current()->addUnit($unit->machine_name, $request->input('amount_of_units'));
                }
            }
        }

        return redirect()->route('admin.developershortcuts.index')->with('success', __('Changes saved!'));
    }

    public function updateResources(\Illuminate\Http\Request $request, PlayerService $playerService): RedirectResponse
    {
        // Handle resource addition / subtraction
        foreach (ResourceType::cases() as $resourceType) {
            if ($request->has('resource_' . $resourceType->value)) {
                if (isset($request->amount_of_resources)) {
                    $playerService->planets->current()->addResource($resourceType, $request->amount_of_resources);
                }
            }
        }
        return redirect()->route('admin.developershortcuts.index')->with('success', __('Changes saved!'));
    }

    /**
     * Creates a planet or moon at the specified coordinates.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createAtCoords(\Illuminate\Http\Request $request, PlanetServiceFactory $planetServiceFactory, PlayerService $player): RedirectResponse
    {
        // Validate coordinates
        $validated = $request->validate([
            'galaxy' => 'required|integer|min:1|max:9',
            'system' => 'required|integer|min:1|max:499',
            'position' => 'required|integer|min:1|max:15',
        ]);

        $coordinate = new Coordinate(
            $validated['galaxy'],
            $validated['system'],
            $validated['position']
        );

        try {
            if ($request->has('delete_moon')) {
                // Check if there's a moon at these coordinates.
                $moon = $player->planets->getMoonByCoordinates($coordinate);

                if (!$moon) {
                    return redirect()->back()->with('error', 'No moon exists at ' . $coordinate->asString());
                }

                // Delete the moon.
                $moon->abandonPlanet();
                return redirect()->back()->with('success', 'Moon deleted successfully at ' . $coordinate->asString());
            }

            if ($request->has('create_planet')) {
                // Create planet for current admin user
                $planetServiceFactory->createAdditionalPlanetForPlayer($player, $coordinate);
                return redirect()->back()->with('success', 'Planet created successfully at ' . $coordinate->asString());
            }

            if ($request->has('create_moon')) {
                // First check if there's a planet at these coordinates.
                $existingPlanet = $planetServiceFactory->makeForCoordinate($coordinate);
                if (!$existingPlanet) {
                    return redirect()->back()->with('error', 'Cannot create moon - no planet exists at ' . $coordinate->asString());
                }

                // Create moon for the planet's owner.
                $planetOwner = $existingPlanet->getPlayer();
                $planetServiceFactory->createMoonForPlayer(planet: $existingPlanet);

                return redirect()->back()->with('success', 'Moon created successfully at ' . $coordinate->asString());
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to create planet/moon: ' . $e->getMessage());
        }

        return redirect()->back()->with('error', 'Invalid action specified');
    }
}
