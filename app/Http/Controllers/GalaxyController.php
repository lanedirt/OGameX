<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use OGame\Factories\PlanetServiceFactory;
use OGame\Http\Traits\IngameTrait;
use OGame\Models\Planet;
use OGame\Services\PlayerService;

class GalaxyController extends Controller
{
    use IngameTrait;

    /**
     * Shows the galaxy index page
     *
     * @param int $id
     * @return Response
     */
    public function index(Request $request, PlayerService $player)
    {
        $this->body_id = 'galaxy';

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
            'body_id' => $this->body_id,
            'current_galaxy' => $galaxy,
            'current_system' => $system,
            'espionage_probe_count' => 0,
            'recycler_count' => 0,
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
     * @param $galaxy
     * @param $system
     */
    public function getTable($galaxy, $system)
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

        $view_response = $view = View::make('ingame.galaxy.table', [
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
     * @param int $id
     * @return Response
     */
    public function ajax(Request $request, PlayerService $player)
    {
        $galaxy = $request->input('galaxy');
        $system = $request->input('system');

        return response()->json(['galaxy' => $this->getTable($galaxy, $system)]);
    }
}
