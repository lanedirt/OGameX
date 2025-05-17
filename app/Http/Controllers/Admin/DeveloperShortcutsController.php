<?php

namespace OGame\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
            'buildings' => [...ObjectService::getBuildingObjects(), ...ObjectService::getStationObjects()],
            'research' => ObjectService::getResearchObjects(),
            'currentPlanet' => $playerService->planets->current(),
        ]);
    }

    /**
     * Updates the planet objects and units.
     *
     * @param Request $request
     * @param PlayerService $playerService
     * @return RedirectResponse
     * @throws \Exception
     */
    public function update(Request $request, PlayerService $playerService): RedirectResponse
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
        }

        // Handle unit submission
        $amountOfUnits = max(1, $request->input('amount_of_units', 1));
        foreach (ObjectService::getUnitObjects() as $unit) {
            if ($request->has('unit_' . $unit->id)) {
                // Handle adding the specific unit
                $playerService->planets->current()->addUnit($unit->machine_name, AppUtil::parseResourceValue($amountOfUnits));
            }
        }

        // Handle building level setting
        foreach ($request->all() as $key => $value) {
            if (str_starts_with($key, 'building_')) {
                $buildingId = (int)substr($key, 9); // Remove 'building_' prefix
                $level = (int)$request->input('building_level', 1);

                // Find the building object
                $building = null;
                foreach ([...ObjectService::getBuildingObjects(), ...ObjectService::getStationObjects()] as $obj) {
                    if ($obj->id === $buildingId) {
                        $building = $obj;
                        break;
                    }
                }

                if ($building) {
                    $playerService->planets->current()->setObjectLevel($building->id, $level);
                }
            }
        }

        // Handle research level setting
        foreach ($request->all() as $key => $value) {
            if (str_starts_with($key, 'research_')) {
                $researchId = (int)substr($key, 9); // Remove 'research_' prefix
                $level = (int)$request->input('research_level', 1);

                // Find the research object
                $research = null;
                foreach (ObjectService::getResearchObjects() as $obj) {
                    if ($obj->id === $researchId) {
                        $research = $obj;
                        break;
                    }
                }

                if ($research) {
                    $playerService->setResearchLevel($research->machine_name, $level);
                }
            }
        }

        return redirect()->route('admin.developershortcuts.index')->with('success', __('Changes saved!'));
    }

    /**
     * Updates the resources of the specified planet.
     *
     * @param Request $request
     * @param PlayerService $playerService
     * @return RedirectResponse
     */
    public function updateResources(Request $request, PlayerService $playerService): RedirectResponse
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

        $planetFactory = app(PlanetServiceFactory::class);
        if ($request->has('update_resources_planet')) {
            $planet = $planetFactory->makePlanetForCoordinate($coordinate);
        } elseif ($request->has('update_resources_moon')) {
            $planet = $planetFactory->makeMoonForCoordinate($coordinate);
        } else {
            return redirect()->back()->with('error', 'Invalid action specified');
        }

        // Parse each resource value, handling k/m/b suffixes and negative values
        $metal = AppUtil::parseResourceValue($request->input('metal', 0));
        $crystal = AppUtil::parseResourceValue($request->input('crystal', 0));
        $deuterium = AppUtil::parseResourceValue($request->input('deuterium', 0));

        // Split resources into positive and negative values
        $resourcesToAdd = new Resources(
            metal: max(0, $metal),
            crystal: max(0, $crystal),
            deuterium: max(0, $deuterium),
            energy: 0
        );

        $resourcesToDeduct = new Resources(
            metal: abs(min(0, $metal)),
            crystal: abs(min(0, $crystal)),
            deuterium: abs(min(0, $deuterium)),
            energy: 0
        );

        // First deduct negative values, then add positive values
        if ($resourcesToDeduct->sum() > 0) {
            $planet->deductResources($resourcesToDeduct);
        }

        if ($resourcesToAdd->sum() > 0) {
            $planet->addResources($resourcesToAdd);
        }

        return redirect()->back()->with('success', 'Resources updated successfully');
    }

    /**
     * Creates a planet or moon at the specified coordinates.
     *
     * @param Request $request
     * @param PlanetServiceFactory $planetServiceFactory
     * @param PlayerService $player
     * @return RedirectResponse
     */
    public function createAtCoords(Request $request, PlanetServiceFactory $planetServiceFactory, PlayerService $player): RedirectResponse
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
                $moon = $planetServiceFactory->makeMoonForCoordinate($coordinate);

                if (!$moon) {
                    return redirect()->back()->with('error', 'No moon exists at ' . $coordinate->asString());
                }

                // Delete the moon.
                $moon->abandonPlanet();
                return redirect()->back()->with('success', 'Moon deleted successfully at ' . $coordinate->asString());
            }

            if ($request->has('delete_planet')) {
                // Check if there's a moon at these coordinates.
                $planet = $planetServiceFactory->makePlanetForCoordinate($coordinate);

                if (!$planet) {
                    return redirect()->back()->with('error', 'No planet exists at ' . $coordinate->asString());
                }

                // Delete the planet.
                $planet->abandonPlanet();
                return redirect()->back()->with('success', 'Planet deleted successfully at ' . $coordinate->asString());
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

                // Create moon for the specified planet.
                $planetServiceFactory->createMoonForPlanet(planet: $existingPlanet);

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
     * @param Request $request
     * @return RedirectResponse
     */
    public function createDebris(Request $request)
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
