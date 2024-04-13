<?php

namespace OGame\Http\Controllers;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use OGame\Factories\PlanetServiceFactory;
use OGame\Models\Planet;
use OGame\Services\PlayerService;

class GalaxyController extends OGameController
{
    /**
     * Shows the galaxy index page
     *
     * @param Request $request
     * @param PlayerService $player
     * @return View
     * @throws BindingResolutionException
     */
    public function index(Request $request, PlayerService $player) : View
    {
        $this->setBodyId('galaxy');

        // Get current galaxy and system from current planet.
        $planet = $player->planets->current();
        $coordinates = $planet->getPlanetCoordinates();
        $galaxy = $coordinates['galaxy'];
        $system = $coordinates['system'];

        // Get galaxy and system querystring params if set instead.
        $galaxy_qs = $request->input('galaxy', '0');
        $system_qs = $request->input('system', '0');
        if (!empty($galaxy_qs) && !empty($system_qs)) {
            $galaxy = intval($galaxy_qs);
            $system = intval($system_qs);
        }

        return view('ingame.galaxy.index')->with([
            'current_galaxy' => $galaxy,
            'current_system' => $system,
            'espionage_probe_count' => 0,
            'recycler_count' => 0,
            'interplanetary_missiles_count' => 0,
            'used_slots' => 0,
            'max_slots' => 1,
            'galaxy_table_html' => $this->getTable($galaxy, $system),
        ]);
    }

    /**
     * Get galaxy table (used for both static and AJAX requests)
     *
     * @param int $galaxy
     * @param int $system
     * @return string
     * @throws BindingResolutionException
     */
    public function getTable(int $galaxy, int $system) : string
    {
        // Retrieve all planets from this galaxy and system.
        $planet_list = Planet::where(['galaxy' => $galaxy, 'system' => $system])->get();
        $planets = [];
        foreach ($planet_list as $record) {
            $planetServiceFactory =  app()->make(PlanetServiceFactory::class);
            $planetService = $planetServiceFactory->make($record->id);
            $planets[$record->planet] = $planetService;
        }

        // Render galaxy rows
        $galaxy_rows = [];
        for ($i = 1; $i <= 15; $i++) {
            $planet = false;

            // Check if planet exists, if so, pass information.
            if (!empty($planets[$i])) {
                // Planet exists.
                $planet = $planets[$i];
            }

            $galaxy_rows[$i] = [
                'planet' => $planet,
            ];
        }

        $view = \Illuminate\Support\Facades\View::make('ingame.galaxy.table', [
            'current_galaxy' => $galaxy,
            'current_system' => $system,
            'espionage_probe_count' => 0,
            'recycler_count' => 0,
            'interplanetary_missiles_count' => 0,
            'used_slots' => 0,
            'max_slots' => 1,
            'galaxy_rows' => $galaxy_rows,
        ]);
        $view_html = $view->render();

        return $view_html;
    }

    /**
     * Shows the galaxy index page
     *
     * @param Request $request
     * @return JsonResponse
     * @throws BindingResolutionException
     */
    public function ajax(Request $request) : JsonResponse
    {
        $galaxy = $request->input('galaxy');
        $system = $request->input('system');

        return response()->json(['galaxy' => $this->getTable($galaxy, $system)]);
    }
}
