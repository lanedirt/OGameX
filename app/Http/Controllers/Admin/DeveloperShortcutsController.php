<?php

namespace OGame\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use OGame\Http\Controllers\OGameController;
use OGame\Services\ObjectService;
use OGame\Services\PlayerService;
use OGame\Models\Planet\Coordinate;
use OGame\Factories\PlanetServiceFactory;
use OGame\Services\DebrisFieldService;
use OGame\Models\Resources;
use OGame\Facades\AppUtil;

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
     * Updates the planet objects and units.
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
        } elseif ($request->has('reset_resources')) {
            // Set all resources to 0 by deducting the current amount.
            $playerService->planets->current()->deductResources($playerService->planets->current()->getResources());

            return redirect()->back()->with('success', 'All resources have been set to 0');
        } else {
            // Handle unit submission
            foreach (ObjectService::getUnitObjects() as $unit) {
                if ($request->has('unit_' . $unit->id)) {
                    // Handle adding the specific unit
                    $playerService->planets->current()->addUnit($unit->machine_name, (int)AppUtil::parseResourceValue($request->input('amount_of_units')));
                }
            }
        }

        return redirect()->route('admin.developershortcuts.index')->with('success', __('Changes saved!'));
    }

    /**
     * Updates the resources of the specified planet.
     *
     * @param \Illuminate\Http\Request $request
     * @param PlayerService $playerService
     * @return RedirectResponse
     */
    public function updateResources(\Illuminate\Http\Request $request, PlayerService $playerService): RedirectResponse
    {
        $resources = [];
        foreach (['metal', 'crystal', 'deuterium'] as $resource) {
            $resources[$resource] = AppUtil::parseResourceValue($request->input("resource_{$resource}", 0));
        }

        $resourcesObj = new Resources(
            metal: $resources['metal'],
            crystal: $resources['crystal'],
            deuterium: $resources['deuterium'],
            energy: 0
        );

        $planet = $playerService->planets->current();

        // Add the resources (negative values will subtract)
        $planet->addResources($resourcesObj);
        $planet->save();

        return redirect()->route('admin.developershortcuts.index')
            ->with('success', __('Resources updated successfully!'));
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

    /**
     * Creates a debris field at the specified coordinates.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createDebris(\Illuminate\Http\Request $request)
    {
        $coordinate = new Coordinate(
            galaxy: (int)$request->input('galaxy'),
            system: (int)$request->input('system'),
            position: (int)$request->input('position')
        );

        $debrisField = app(DebrisFieldService::class);

        if ($request->has('delete_debris')) {
            // Load and delete if exists
            if ($debrisField->loadForCoordinates($coordinate)) {
                $debrisField->delete();
                return redirect()->back()->with('success', 'Debris field deleted successfully at ' . $coordinate->asString());
            }
            return redirect()->back()->with('error', 'No debris field exists at ' . $coordinate->asString());
        }

        // Create/append debris field
        $debrisField->loadOrCreateForCoordinates($coordinate);

        // Add the resources
        $resources = new Resources(
            metal: (int)AppUtil::parseResourceValue($request->input('metal', 0)),
            crystal: (int)AppUtil::parseResourceValue($request->input('crystal', 0)),
            deuterium: (int)AppUtil::parseResourceValue($request->input('deuterium', 0)),
            energy: 0,
        );

        $debrisField->appendResources($resources);
        $debrisField->save();

        return redirect()->back()->with('success', 'Debris field created/updated successfully at ' . $coordinate->asString());
    }
}
