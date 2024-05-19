<?php

namespace OGame\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use OGame\Http\Controllers\OGameController;
use OGame\Services\ObjectService;
use OGame\Services\PlayerService;
use OGame\Services\SettingsService;

class DeveloperShortcutsController extends OGameController
{
    /**
     * Shows the server settings page.
     *
     * @param PlayerService $player
     * @param SettingsService $settingsService
     * @param ObjectService $objectService
     * @return View
     */
    public function index(PlayerService $player, SettingsService $settingsService, ObjectService $objectService): View
    {
        // Get all unit objects
        $units = $objectService->getUnitObjects();


        return view('ingame.admin.developershortcuts')->with([
            'units' => $units,
        ]);
    }

    /**
     * Updates the server settings.
     *
     * @param \Illuminate\Http\Request $request
     * @param PlayerService $playerService
     * @param ObjectService $objectService
     * @return RedirectResponse
     * @throws \Exception
     */
    public function update(\Illuminate\Http\Request $request, PlayerService $playerService, ObjectService $objectService): RedirectResponse
    {
        if ($request->has('set_mines')) {
            // Handle "Set all mines to level 30"
            $playerService->planets->current()->setObjectLevel($objectService->getObjectByMachineName('metal_mine')->id, 30);
            $playerService->planets->current()->setObjectLevel($objectService->getObjectByMachineName('crystal_mine')->id, 30);
            $playerService->planets->current()->setObjectLevel($objectService->getObjectByMachineName('deuterium_synthesizer')->id, 30);
        } elseif ($request->has('set_storages')) {
            // Handle "Set all storages to level 30"
            $playerService->planets->current()->setObjectLevel($objectService->getObjectByMachineName('metal_store')->id, 15);
            $playerService->planets->current()->setObjectLevel($objectService->getObjectByMachineName('crystal_store')->id, 15);
            $playerService->planets->current()->setObjectLevel($objectService->getObjectByMachineName('deuterium_store')->id, 15);
        } elseif ($request->has('set_shipyard')) {
            // Handle "Set all shipyard facilities to level 12"
            $playerService->planets->current()->setObjectLevel($objectService->getObjectByMachineName('shipyard')->id, 12);
            $playerService->planets->current()->setObjectLevel($objectService->getObjectByMachineName('robot_factory')->id, 12);
            $playerService->planets->current()->setObjectLevel($objectService->getObjectByMachineName('nano_factory')->id, 12);

        } elseif ($request->has('set_research')) {
            // Handle "Set all research to level 10"
            $playerService->planets->current()->setObjectLevel($objectService->getObjectByMachineName('research_lab')->id, 12);
            foreach ($objectService->getResearchObjects() as $research) {
                $playerService->setResearchLevel($research->machine_name, 10);
            }
        } elseif ($request->has('reset_buildings')) {
            // Handle "Reset all buildings"
            foreach ($objectService->getBuildingObjects() as $building) {
                $playerService->planets->current()->setObjectLevel($building->id, 0);
            }
            foreach ($objectService->getStationObjects() as $building) {
                $playerService->planets->current()->setObjectLevel($building->id, 0);
            }
        } elseif ($request->has('reset_research')) {
            // Handle "Reset all research"
            foreach ($objectService->getResearchObjects() as $research) {
                $playerService->setResearchLevel($research->machine_name, 0);
            }
        } elseif ($request->has('reset_units')) {
            // Handle "Reset all units"
            foreach ($objectService->getUnitObjects() as $unit) {
                $playerService->planets->current()->removeUnit($unit->machine_name, $playerService->planets->current()->getObjectAmount($unit->machine_name));
            }
        } else {
            // Handle unit submission
            foreach ($objectService->getUnitObjects() as $unit) {
                if ($request->has('unit_' . $unit->id)) {
                    // Handle adding the specific unit
                    $playerService->planets->current()->addUnit($unit->machine_name, $request->input('amount_of_units'));
                }
            }
        }


        return redirect()->route('admin.developershortcuts.index')->with('success', __('Changes saved!'));
    }
}
