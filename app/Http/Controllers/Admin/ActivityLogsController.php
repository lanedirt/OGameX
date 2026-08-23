<?php

namespace OGame\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\View\View;
use OGame\Factories\GameMissionFactory;
use OGame\Http\Controllers\OGameController;
use OGame\Models\BuildingQueue;
use OGame\Models\FleetMission;
use OGame\Models\Planet;
use OGame\Models\ResearchQueue;
use OGame\Models\UnitQueue;
use OGame\Models\User;
use OGame\Services\ObjectService;

class ActivityLogsController extends OGameController
{
    private const PER_PAGE = 50;

    /**
     * Shows recent game activity for admin monitoring.
     */
    public function index(Request $request): View
    {
        $tab = $request->input('tab', 'fleets');
        if (!in_array($tab, ['fleets', 'buildings', 'units', 'research'], true)) {
            $tab = 'fleets';
        }

        $fleets = null;
        $buildings = null;
        $units = null;
        $research = null;
        $users = collect();
        $planets = collect();

        if ($tab === 'fleets') {
            $fleets = FleetMission::query()
                ->orderByDesc('id')
                ->paginate(self::PER_PAGE)
                ->withQueryString();

            $userIds = $fleets->pluck('user_id')->unique();
            $users = User::whereIn('id', $userIds)->pluck('username', 'id');
        } elseif ($tab === 'buildings') {
            $buildings = BuildingQueue::query()
                ->orderByDesc('id')
                ->paginate(self::PER_PAGE)
                ->withQueryString();

            $planetIds = $buildings->pluck('planet_id')->unique();
            $planets = Planet::whereIn('id', $planetIds)->get()->keyBy('id');
            $users = User::whereIn('id', $planets->pluck('user_id')->unique())->pluck('username', 'id');
        } elseif ($tab === 'units') {
            $units = UnitQueue::query()
                ->orderByDesc('id')
                ->paginate(self::PER_PAGE)
                ->withQueryString();

            $planetIds = $units->pluck('planet_id')->unique();
            $planets = Planet::whereIn('id', $planetIds)->get()->keyBy('id');
            $users = User::whereIn('id', $planets->pluck('user_id')->unique())->pluck('username', 'id');
        } else {
            $research = ResearchQueue::query()
                ->orderByDesc('id')
                ->paginate(self::PER_PAGE)
                ->withQueryString();

            $planetIds = $research->pluck('planet_id')->unique();
            $planets = Planet::whereIn('id', $planetIds)->get()->keyBy('id');
            $users = User::whereIn('id', $planets->pluck('user_id')->unique())->pluck('username', 'id');
        }

        $objectNames = [];
        foreach (ObjectService::getBuildingObjects() as $object) {
            $objectNames[$object->id] = $object->title;
        }
        foreach (ObjectService::getStationObjects() as $object) {
            $objectNames[$object->id] = $object->title;
        }
        foreach (ObjectService::getResearchObjects() as $object) {
            $objectNames[$object->id] = $object->title;
        }
        foreach (ObjectService::getShipObjects() as $object) {
            $objectNames[$object->id] = $object->title;
        }
        foreach (ObjectService::getDefenseObjects() as $object) {
            $objectNames[$object->id] = $object->title;
        }

        return view('ingame.admin.activitylogs', [
            'tab' => $tab,
            'fleets' => $fleets,
            'buildings' => $buildings,
            'units' => $units,
            'research' => $research,
            'users' => $users,
            'planets' => $planets,
            'objectNames' => $objectNames,
            'missionTypeLabels' => collect(GameMissionFactory::getAllMissions())
                ->mapWithKeys(fn ($mission, $id) => [$id => $mission::getName()]),
        ]);
    }
}
