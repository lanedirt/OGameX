@php use OGame\Models\Enums\PlanetType; @endphp
@php /** @var OGame\Services\PlayerService $player */ @endphp
@php /** @var OGame\Services\PlanetService $planet */ @endphp
@php /** @var OGame\Services\SettingsService $settings */ @endphp

@extends('ingame.layouts.main')

@section('content')

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <div id="eventboxContent" style="display: none">
        <img height="16" width="16" src="/img/icons/3f9884806436537bdec305aa26fc60.gif">
    </div>
    <div id="fleetdispatchcomponent" class="maincontent">
        <script type="text/javascript">
            var checkTargetUrl = "{{ route('fleet.dispatch.checktarget') }}"
            var sendFleetUrl = "{{ route('fleet.dispatch.sendfleet') }}"
            var saveSettingsUrl = ""

            var fleetBoxOrder = {"fleetboxdestination": 0, "fleetboxmission": 1, "fleetboxbriefingandresources": 2}

            var FLEET_DEUTERIUM_SAVE_FACTOR = 0.5;
            var maxNumberOfPlanets = 2;

            // TODO: make ships data dynamic based on GameObject data for proper
            // fuel consumption and cargo capacity value calculations.
            var shipsData = {
                "204": {
                    "id": 204,
                    "name": "Light Fighter",
                    "baseFuelCapacity": 50,
                    "baseCargoCapacity": 50,
                    "fuelConsumption": 7,
                    "speed": 31250
                },
                "205": {
                    "id": 205,
                    "name": "Heavy Fighter",
                    "baseFuelCapacity": 100,
                    "baseCargoCapacity": 100,
                    "fuelConsumption": 27,
                    "speed": 26000
                },
                "206": {
                    "id": 206,
                    "name": "Cruiser",
                    "baseFuelCapacity": 800,
                    "baseCargoCapacity": 800,
                    "fuelConsumption": 112,
                    "speed": 39000
                },
                "207": {
                    "id": 207,
                    "name": "Battleship",
                    "baseFuelCapacity": 1500,
                    "baseCargoCapacity": 1500,
                    "fuelConsumption": 187,
                    "speed": 20000
                },
                "215": {
                    "id": 215,
                    "name": "Battlecruiser",
                    "baseFuelCapacity": 750,
                    "baseCargoCapacity": 750,
                    "fuelConsumption": 93,
                    "speed": 20000
                },
                "211": {
                    "id": 211,
                    "name": "Bomber",
                    "baseFuelCapacity": 500,
                    "baseCargoCapacity": 500,
                    "fuelConsumption": 262,
                    "speed": 10400
                },
                "213": {
                    "id": 213,
                    "name": "Destroyer",
                    "baseFuelCapacity": 2000,
                    "baseCargoCapacity": 2000,
                    "fuelConsumption": 375,
                    "speed": 10000
                },
                "214": {
                    "id": 214,
                    "name": "Deathstar",
                    "baseFuelCapacity": 1000000,
                    "baseCargoCapacity": 1000000,
                    "fuelConsumption": 1,
                    "speed": 100
                },
                "218": {
                    "id": 218,
                    "name": "Reaper",
                    "baseFuelCapacity": 10000,
                    "baseCargoCapacity": 10000,
                    "fuelConsumption": 412,
                    "speed": 14000
                },
                "219": {
                    "id": 219,
                    "name": "Pathfinder",
                    "baseFuelCapacity": 12000,
                    "baseCargoCapacity": 12000,
                    "fuelConsumption": 112,
                    "speed": 24000
                },
                "202": {
                    "id": 202,
                    "name": "Small Cargo",
                    "baseFuelCapacity": 5000,
                    "baseCargoCapacity": 5000,
                    "fuelConsumption": 3,
                    "speed": 7500
                },
                "203": {
                    "id": 203,
                    "name": "Large Cargo",
                    "baseFuelCapacity": 25000,
                    "baseCargoCapacity": 25000,
                    "fuelConsumption": 18,
                    "speed": 11250
                },
                "208": {
                    "id": 208,
                    "name": "Colony Ship",
                    "baseFuelCapacity": 7500,
                    "baseCargoCapacity": 7500,
                    "fuelConsumption": 375,
                    "speed": 4000
                },
                "209": {
                    "id": 209,
                    "name": "Recycler",
                    "baseFuelCapacity": 24000,
                    "baseCargoCapacity": 24000,
                    "fuelConsumption": 112,
                    "speed": 5000
                },
                "210": {
                    "id": 210,
                    "name": "Espionage Probe",
                    "baseFuelCapacity": 5,
                    "baseCargoCapacity": 0,
                    "fuelConsumption": 1,
                    "speed": 150000000
                },
                "217": {
                    "id": 217,
                    "name": "Crawler",
                    "baseFuelCapacity": 0,
                    "baseCargoCapacity": 0,
                    "fuelConsumption": 1,
                    "speed": 0
                }
            };

            var speed = 100

            var PLAYER_ID_SPACE = 99999;
            var PLAYER_ID_LEGOR = 1;
            var DONUT_GALAXY = 1;
            var DONUT_SYSTEM = 1;
            var MAX_GALAXY = {{ $settings->numberOfGalaxies() }};
            var MAX_SYSTEM = {{ \OGame\GameConstants\UniverseConstants::MAX_SYSTEM_COUNT }};
            var MAX_POSITION = {{ \OGame\GameConstants\UniverseConstants::EXPEDITION_POSITION }};
            var SPEEDFAKTOR_FLEET_PEACEFUL = {{ $settings->fleetSpeedPeaceful() }};
            var SPEEDFAKTOR_FLEET_WAR = {{ $settings->fleetSpeedWar() }};
            var SPEEDFAKTOR_FLEET_HOLDING = {{ $settings->fleetSpeedHolding() }};
            var PLANETTYPE_PLANET = 1;
            var PLANETTYPE_DEBRIS = 2;
            var PLANETTYPE_MOON = 3;
            var EXPEDITION_POSITION = {{ \OGame\GameConstants\UniverseConstants::EXPEDITION_POSITION }};
            var MAX_NUMBER_OF_PLANETS = {{ $player->getMaxPlanetAmount() }};
            var COLONIZATION_ENABLED = true;

            var LOOT_PRIO_METAL = 2;
            var LOOT_PRIO_CRYSTAL = 3;
            var LOOT_PRIO_DEUTERIUM = 4;
            // var LOOT_PRIO_FOOD = 1;

            var missions = {
                "MISSION_NONE": 0,
                "MISSION_ATTACK": 1,
                "MISSION_UNIONATTACK": 2,
                "MISSION_TRANSPORT": 3,
                "MISSION_DEPLOY": 4,
                "MISSION_HOLD": 5,
                "MISSION_ESPIONAGE": 6,
                "MISSION_COLONIZE": 7,
                "MISSION_RECYCLE": 8,
                "MISSION_DESTROY": 9,
                "MISSION_MISSILEATTACK": 10,
                "MISSION_EXPEDITION": 15
            };
            var orderNames = {
                "15": "{{ __('t_ingame.fleet.mission_expedition') }}",
                "7": "{{ __('t_ingame.fleet.mission_colonise') }}",
                "8": "{{ __('t_ingame.fleet.mission_recycle') }}",
                "3": "{{ __('t_ingame.fleet.mission_transport') }}",
                "4": "{{ __('t_ingame.fleet.mission_deploy') }}",
                "6": "{{ __('t_ingame.fleet.mission_espionage') }}",
                "5": "{{ __('t_ingame.fleet.mission_acs_defend') }}",
                "1": "{{ __('t_ingame.fleet.mission_attack') }}",
                "2": "{{ __('t_ingame.fleet.mission_acs_attack') }}",
                "9": "{{ __('t_ingame.fleet.mission_destroy_moon') }}"
            };
            var orderDescriptions = {
                "1": "{{ __('t_ingame.fleet.desc_attack') }}",
                "2": "{{ __('t_ingame.fleet.desc_acs_attack') }}",
                "3": "{{ __('t_ingame.fleet.desc_transport') }}",
                "4": "{{ __('t_ingame.fleet.desc_deploy') }}",
                "5": "{{ __('t_ingame.fleet.desc_acs_defend') }}",
                "6": "{{ __('t_ingame.fleet.desc_espionage') }}",
                "7": "{{ __('t_ingame.fleet.desc_colonise') }}",
                "8": "{{ __('t_ingame.fleet.desc_recycle') }}",
                "9": "{{ __('t_ingame.fleet.desc_destroy_moon') }}",
                "15": "{{ __('t_ingame.fleet.desc_expedition') }}"
            };

            var currentPlanet = {
                "galaxy": {{ $planet->getPlanetCoordinates()->galaxy }},
                "system": {{ $planet->getPlanetCoordinates()->system }},
                "position": {{ $planet->getPlanetCoordinates()->position }},
                "type": {{ $planet->getPlanetType() }},
                "name": "{{ $planet->getPlanetName() }}"
            };
            var targetPlanet = {
                "galaxy": {{ $galaxy ?? $planet->getPlanetCoordinates()->galaxy }},
                "system": {{ $system ?? $planet->getPlanetCoordinates()->system }},
                "position": {{ $position ?? $planet->getPlanetCoordinates()->position }},
                "type": {{ $type ?? $planet->getPlanetType() }},
                "name": "{{ $targetPlanetName ?? $planet->getPlanetName() }}"
            };

            var targetPlayerId = "{{ $targetPlayerId ?? $player->getId() }}";
            var targetPlayerName = "{{ $targetPlayerName ?? $player->getUsername(false) }}";

            var shipsOnPlanet = [@foreach ($units as $unitGroup)@foreach ($unitGroup as $unit)@if($unit->amount > 0){
                "id": {{ $unit->object->id }},
                "number": {{ $unit->amount }}
            },@endif @endforeach @endforeach];
            var useHalfSteps = true;
            var shipsToSend = [];
            var planets = [{
                "galaxy": {{ $planet->getPlanetCoordinates()->galaxy }},
                "system": {{ $planet->getPlanetCoordinates()->system }},
                "position": {{ $planet->getPlanetCoordinates()->position }},
                "type": {{ $planet->getPlanetType() }},
                "name": "{{ $planet->getPlanetName() }}"
            }];
            var standardFleets = [];
            var unions = @json(collect($availableUnions)->map(fn($u) => ['id' => $u['id'], 'time' => $u['time']])->values());

            var mission = {{ $mission ?? 0}};
            var unionID = 0;
            var speed = 10;

            var missionHold = 5;
            var missionExpedition = 15;

            var holdingTime = 1;
            var expeditionTime = 0;
            // var lifeformEnabled = true;
            var metalOnPlanet = {{ $planet->metal()->getRounded() }};
            var crystalOnPlanet = {{ $planet->crystal()->getRounded() }};
            var deuteriumOnPlanet = {{ $planet->deuterium()->getRounded() }};
            // var foodOnPlanet = 0;

            var fleetCount = {{ $fleetSlotsInUse }};
            var maxFleetCount = {{ $fleetSlotsMax }};
            var expeditionCount = 0;
            var maxExpeditionCount = 1;

            var warningsEnabled = true;

            var playerId = {{ $player->getId() }};
            var hasAdmiral = false;
            var hasCommander = false;
            var isOnVacation = false;

            var moveInProgress = false;
            var planetCount = {{ $player->planets->planetCount() }};
            var explorationCount = 1;

            var loca = {
                "LOCA_FLEET_TITLE_MOVEMENTS": "{{ __('t_ingame.fleet.to_movement') }}",
                "LOCA_FLEET_MOVEMENT": "{{ __('t_ingame.fleet.movement_title') }}",
                "LOCA_FLEET_EDIT_STANDARTFLEET": "{{ __('t_ingame.fleet.edit_standard_fleets') }}",
                "LOCA_FLEET_STANDARD": "{{ __('t_ingame.fleet.standard_fleets') }}",
                "LOCA_FLEET_HEADLINE_ONE": "{{ __('t_ingame.fleet.dispatch_1_title') }}",
                "LOCA_FLEET_TOOLTIPP_SLOTS": "{{ __('t_ingame.fleet.tooltip_slots') }}",
                "LOCA_FLEET_FLEETSLOTS": "{{ __('t_ingame.fleet.fleets') }}",
                "LOCA_FLEET_NO_FREE_SLOTS": "{{ __('t_ingame.fleet.no_free_slots') }}",
                "LOCA_FLEETSENDING_NO_TARGET": "{{ __('t_ingame.fleet.no_target') }}",
                "LOCA_FLEET_TOOLTIPP_EXP_SLOTS": "{{ __('t_ingame.fleet.tooltip_exp_slots') }}",
                "LOCA_FLEET_EXPEDITIONS": "{{ __('t_ingame.fleet.expeditions') }}",
                "LOCA_ALL_NEVER": "{{ __('t_ingame.fleet.never') }}",
                "LOCA_FLEET_SEND_NOTAVAILABLE": "{{ __('t_ingame.fleet.dispatch_impossible') }}",
                "LOCA_FLEET_NO_SHIPS_ON_PLANET": "{{ __('t_ingame.fleet.no_ships') }}",
                "LOCA_SHIPYARD_HEADLINE_BATTLESHIPS": "{{ __('t_ingame.fleet.combat_ships') }}",
                "LOCA_SHIPYARD_HEADLINE_CIVILSHIPS": "{{ __('t_ingame.fleet.civil_ships') }}",
                "LOCA_FLEET_SELECT_SHIPS_ALL": "{{ __('t_ingame.fleet.select_all_ships') }}",
                "LOCA_FLEET_SELECTION_RESET": "{{ __('t_ingame.fleet.reset_choice') }}",
                "LOCA_API_FLEET_DATA": "{{ __('t_ingame.fleet.api_data') }}",
                "LOCA_ALL_BUTTON_FORWARD": "{{ __('t_ingame.fleet.continue') }}",
                "LOCA_FLEET_NO_SELECTION": "{{ __('t_ingame.fleet.no_selection') }}",
                "LOCA_ALL_TACTICAL_RETREAT": "{{ __('t_ingame.fleet.tactical_retreat') }}",
                "LOCA_FLEET1_TACTICAL_RETREAT_CONSUMPTION_TOOLTIP": "{{ __('t_ingame.fleet.tactical_retreat_tooltip') }}",
                "LOCA_FLEET_FUEL_CONSUMPTION": "{{ __('t_ingame.fleet.deuterium_consumption') }}",
                "LOCA_FLEET_ERROR_OWN_VACATION": "{{ __('t_ingame.fleet.vacation_error') }}",
                "LOCA_FLEET_CURRENTLY_OCCUPIED": "{{ __('t_ingame.fleet.in_combat') }}",
                "LOCA_FLEET_FREE_MARKET_SLOTS": "{{ __('t_ingame.fleet.market_slots') }}",
                "LOCA_FLEET_TOOLTIPP_FREE_MARKET_SLOTS": "{{ __('t_ingame.fleet.tooltip_market_slots') }}",
                "LOCA_FLEET_HEADLINE_TWO": "{{ __('t_ingame.fleet.dispatch_2_title') }}",
                "LOCA_FLEET_TAKEOFF_PLACE": "{{ __('t_ingame.fleet.origin') }}",
                "LOCA_FLEET_TARGET_PLACE": "{{ __('t_ingame.fleet.destination') }}",
                "LOCA_ALL_PLANET": "{{ __('t_ingame.fleet.planet') }}",
                "LOCA_ALL_MOON": "{{ __('t_ingame.fleet.moon') }}",
                "LOCA_FLEET_COORDINATES": "{{ __('t_ingame.fleet.coordinates') }}",
                "LOCA_FLEET_DISTANCE": "{{ __('t_ingame.fleet.distance') }}",
                "LOCA_FLEET_DEBRIS": "{{ __('t_ingame.fleet.debris_field_lower') }}",
                "LOCA_FLEET_SHORTLINKS": "{{ __('t_ingame.fleet.shortcuts') }}",
                "LOCA_FLEET_FIGHT_ASSOCIATION": "{{ __('t_ingame.fleet.combat_forces') }}",
                "LOCA_FLEET_BRIEFING": "{{ __('t_ingame.fleet.briefing') }}",
                "LOCA_FLEET_DURATION_ONEWAY": "{{ __('t_ingame.fleet.flight_duration') }}",
                "LOCA_FLEET_SPEED": "{{ __('t_ingame.fleet.speed') }}",
                "LOCA_FLEET_SPEED_MAX_SHORT": "{{ __('t_ingame.fleet.max_abbr') }}",
                "LOCA_FLEET_ARRIVAL": "{{ __('t_ingame.fleet.arrival') }}",
                "LOCA_FLEET_TIME_CLOCK": "{{ __('t_ingame.fleet.clock') }}",
                "LOCA_FLEET_RETURN": "{{ __('t_ingame.fleet.return_trip') }}",
                "LOCA_FLEET_HOLD_FREE": "{{ __('t_ingame.fleet.empty_cargobays') }}",
                "LOCA_ALL_BUTTON_BACK": "{{ __('t_ingame.fleet.back') }}",
                "LOCA_FLEET_PLANET_UNHABITATED": "{{ __('t_ingame.fleet.uninhabited_planet') }}",
                "LOCA_FLEET_NO_DEBIRS_FIELD": "{{ __('t_ingame.fleet.no_debris_field') }}",
                "LOCA_FLEET_PLAYER_UMODE": "{{ __('t_ingame.fleet.player_vacation') }}",
                "LOCA_FLEET_ADMIN": "{{ __('t_ingame.fleet.admin_gm') }}",
                "LOCA_ALL_NOOBSECURE": "{{ __('t_ingame.fleet.noob_protection') }}",
                "LOCA_GALAXY_ERROR_STRONG": "{{ __('t_ingame.fleet.player_too_strong') }}",
                "LOCA_FLEET_NO_MOON": "{{ __('t_ingame.fleet.no_moon') }}",
                "LOCA_FLEET_NO_RECYCLER": "{{ __('t_ingame.fleet.no_recycler') }}",
                "LOCA_ALL_NO_EVENT": "{{ __('t_ingame.fleet.no_events') }}",
                "LOCA_PLANETMOVE_ERROR_ALREADY_RESERVED": "{{ __('t_ingame.fleet.planet_already_reserved') }}",
                "LOCA_FLEET_ERROR_TARGET_MSG": "{{ __('t_ingame.fleet.cannot_send_to_target') }}",
                "LOCA_FLEETSENDING_NOT_ENOUGH_FOIL": "{{ __('t_ingame.fleet.not_enough_deuterium') }}",
                "LOCA_FLEET_HEADLINE_THREE": "{{ __('t_ingame.fleet.dispatch_3_title') }}",
                "LOCA_FLEET_TARGET_FOR_MISSION": "{{ __('t_ingame.fleet.select_mission') }}",
                "LOCA_FLEET_MISSION": "{{ __('t_ingame.fleet.mission_label') }}",
                "LOCA_FLEET_RESOURCE_LOAD": "{{ __('t_ingame.fleet.load_resources') }}",
                "LOCA_FLEET_SELECTION_NOT_AVAILABLE": "{{ __('t_ingame.fleet.cannot_start_mission') }}",
                "LOCA_FLEET_RETREAT_AFTER_DEFENDER_RETREAT_TOOLTIP": "{{ __('t_ingame.fleet.retreat_tooltip') }}",
                "LOCA_FLEET_RETREAT_AFTER_DEFENDER_RETREAT": "{{ __('t_ingame.fleet.retreat_on_defender') }}",
                "LOCA_FLEET_TARGET": "{{ __('t_ingame.fleet.target_label') }}",
                "LOCA_FLEET_DURATION_FEDERATION": "{{ __('t_ingame.fleet.federation_duration') }}",
                "LOCA_ALL_TIME_HOUR": "{{ __('t_ingame.fleet.hour_abbr') }}",
                "LOCA_FLEET_HOLD_TIME": "{{ __('t_ingame.fleet.hold_time') }}",
                "LOCA_FLEET_EXPEDITION_TIME": "{{ __('t_ingame.fleet.expedition_duration') }}",
                "LOCA_ALL_METAL": "{{ __('t_ingame.fleet.metal') }}",
                "LOCA_ALL_CRYSTAL": "{{ __('t_ingame.fleet.crystal') }}",
                "LOCA_ALL_DEUTERIUM": "{{ __('t_ingame.fleet.deuterium') }}",
                // "LOCA_ALL_FOOD": "Food",
                "LOCA_FLEET_LOAD_ROOM": "{{ __('t_ingame.fleet.cargo_bay') }}",
                "LOCA_FLEET_CARGO_SPACE": "{{ __('t_ingame.fleet.cargo_space') }}",
                "LOCA_FLEET_SEND": "{{ __('t_ingame.fleet.send_fleet') }}",
                "LOCA_ALL_NETWORK_ATTENTION": "{{ __('t_ingame.shared.caution') }}",
                "LOCA_PLANETMOVE_BREAKUP_WARNING": "{{ __('t_ingame.buildings.planet_move_warning') }}",
                "LOCA_ALL_YES": "{{ __('t_ingame.shared.yes') }}",
                "LOCA_ALL_NO": "{{ __('t_ingame.shared.no') }}",
                "LOCA_ALL_NOTICE": "{{ __('t_ingame.buildings.loca_notice') }}",
                "LOCA_FLEETSENDING_MAX_PLANET_WARNING": "{{ __('t_ingame.fleet.max_planet_warning') }}",
                "LOCA_ALL_PLAYER": "{{ __('t_ingame.fleet.player_label') }}",
                "LOCA_FLEET_RESOURCES_ALL_LOAD": "{{ __('t_ingame.fleet.load_all_resources') }}",
                "LOCA_FLEET_RESOURCES_ALL": "{{ __('t_ingame.fleet.all_resources') }}",
                "LOCA_NETWORK_USERNAME": "{{ __('t_ingame.fleet.player_name') }}",
                "LOCA_EVENTH_ENEMY_INFINITELY_SPACE": "{{ __('t_ingame.fleet.deep_space') }}",
                "LOCA_FLEETSENDING_NO_MISSION_SELECTED": "{{ __('t_ingame.fleet.no_mission_selected') }}",
                "LOCA_EMPTY_SYSTEMS": "{{ __('t_ingame.fleet.empty_systems') }}",
                "LOCA_INACTIVE_SYSTEMS": "{{ __('t_ingame.fleet.inactive_systems') }}",
                "LOCA_NETWORK_ON": "{{ __('t_ingame.fleet.network_on') }}",
                "LOCA_NETWORK_OFF": "{{ __('t_ingame.fleet.network_off') }}",
                // "LOCA_LOOT_FOOD": "Plunder food",
                "LOCA_BASHING_SYSTEM_LIMIT_REACHED_ATTACK_MISSIONS_DISABLED": "{{ __('t_ingame.fleet.bashing_disabled') }}"
            };
            var locadyn = {
                "locaAllOutlawWarning": "You are about to attack a stronger player. If you do this, your attack defenses will be shut down for 7 days and all players will be able to attack you without punishment. Are you sure you want to continue?",
                "localBashWarning": "In this universe, 0 attacks are permitted within a 24-hour period. This attack would probably exceed this limit. Do you really wish to launch it?",
                "locaOfficerbonusTooltipp": "+ 2 Fleet slots because of Admiral"
            };
            var errorCodeMap = {
                "601": "{{ __('t_ingame.fleet.err_generic') }}",
                "602": "{{ __('t_ingame.fleet.err_no_moon') }}",
                "603": "{{ __('t_ingame.fleet.err_newbie_protection') }}",
                "604": "{{ __('t_ingame.fleet.err_too_strong') }}",
                "605": "{{ __('t_ingame.fleet.err_vacation_mode') }}",
                "606": "{{ __('t_ingame.fleet.err_own_vacation') }}",
                "610": "{{ __('t_ingame.fleet.err_not_enough_ships') }}",
                "611": "{{ __('t_ingame.fleet.err_no_ships') }}",
                "612": "{{ __('t_ingame.fleet.err_no_slots') }}",
                "613": "{{ __('t_ingame.fleet.err_no_deuterium') }}",
                "614": "{{ __('t_ingame.fleet.err_no_planet') }}",
                "615": "{{ __('t_ingame.fleet.err_no_cargo') }}",
                "616": "{{ __('t_ingame.fleet.err_multi_alarm') }}",
                "617": "{{ __('t_ingame.fleet.admin_gm') }}",
                "618": "{{ __('t_ingame.fleet.err_attack_ban') }}"
            };

            var fleetDispatcher = null;

            var emptySystems = 0;
            var inactiveSystems = 0;

            var lootFoodOnAttack = false;

            $(function () {
                fleetDispatcher = new FleetDispatcher(window);
                fleetDispatcher.init();

                // Reserve exactly the class-adjusted fuel the PHP backend will deduct.
                // Floor/ceil avoids float residuals that cause "not enough resources".
                fleetDispatcher.getDeuteriumOnPlanetWithoutConsumption = function () {
                    return Math.max(0, Math.floor(this.deuteriumOnPlanet) - Math.ceil(this.getConsumption()));
                };
            });

            var apiDataJson = {
                "coords": "{{ $planet->getPlanetCoordinates()->asString() }}",
                "characterClassId": {{ $player->getUser()->character_class ?? 0 }},
                "allianceClassId": 0,
                "researches": {"109": 1, "110": 0, "111": 6, "115": 5, "117": 3, "118": 0, "114": 0},
                "defenses": {
                    "401": {"amount": 1, "weapon": 0, "shield": 0, "armor": 0},
                    "402": {"amount": 1, "weapon": 0, "shield": 0, "armor": 0},
                    "403": {"amount": 0, "weapon": 0, "shield": 0, "armor": 0},
                    "404": {"amount": 0, "weapon": 0, "shield": 0, "armor": 0},
                    "405": {"amount": 0, "weapon": 0, "shield": 0, "armor": 0},
                    "406": {"amount": 0, "weapon": 0, "shield": 0, "armor": 0},
                    "407": {"amount": 0, "weapon": 0, "shield": 0, "armor": 0},
                    "408": {"amount": 0, "weapon": 0, "shield": 0, "armor": 0}
                },
                "ships": {
                    "204": {"amount": 0, "weapon": 0, "shield": 0, "armor": 0, "cargo": 0, "speed": 0, "fuel": 0},
                    "205": {"amount": 1, "weapon": 0, "shield": 0, "armor": 0, "cargo": 0, "speed": 0, "fuel": 0},
                    "206": {"amount": 0, "weapon": 0, "shield": 0, "armor": 0, "cargo": 0, "speed": 0, "fuel": 0},
                    "207": {"amount": 0, "weapon": 0, "shield": 0, "armor": 0, "cargo": 0, "speed": 0, "fuel": 0},
                    "215": {"amount": 0, "weapon": 0, "shield": 0, "armor": 0, "cargo": 0, "speed": 0, "fuel": 0},
                    "211": {"amount": 0, "weapon": 0, "shield": 0, "armor": 0, "cargo": 0, "speed": 0, "fuel": 0},
                    "213": {"amount": 0, "weapon": 0, "shield": 0, "armor": 0, "cargo": 0, "speed": 0, "fuel": 0},
                    "214": {"amount": 0, "weapon": 0, "shield": 0, "armor": 0, "cargo": 0, "speed": 0, "fuel": 0},
                    "218": {"amount": 0, "weapon": 0, "shield": 0, "armor": 0, "cargo": 0, "speed": 0, "fuel": 0},
                    "219": {"amount": 0, "weapon": 0, "shield": 0, "armor": 0, "cargo": 0, "speed": 0, "fuel": 0},
                    "202": {"amount": 0, "weapon": 0, "shield": 0, "armor": 0, "cargo": 0, "speed": 0, "fuel": 0},
                    "203": {"amount": 0, "weapon": 0, "shield": 0, "armor": 0, "cargo": 0, "speed": 0, "fuel": 0},
                    "208": {"amount": 0, "weapon": 0, "shield": 0, "armor": 0, "cargo": 0, "speed": 0, "fuel": 0},
                    "209": {"amount": 0, "weapon": 0, "shield": 0, "armor": 0, "cargo": 0, "speed": 0, "fuel": 0},
                    "210": {"amount": 6, "weapon": 0, "shield": 0, "armor": 0, "cargo": 0, "speed": 0, "fuel": 0},
                    "212": {"amount": 2, "weapon": 0, "shield": 0, "armor": 0, "cargo": 0, "speed": 0, "fuel": 0},
                    "217": {"amount": 0, "weapon": 0, "shield": 0, "armor": 0, "cargo": 0, "speed": 0, "fuel": 0},
                    "401": {"amount": 0, "weapon": 0, "shield": 0, "armor": 0, "cargo": 0, "speed": 0, "fuel": 0},
                    "402": {"amount": 0, "weapon": 0, "shield": 0, "armor": 0, "cargo": 0, "speed": 0, "fuel": 0},
                    "403": {"amount": 0, "weapon": 0, "shield": 0, "armor": 0, "cargo": 0, "speed": 0, "fuel": 0},
                    "404": {"amount": 0, "weapon": 0, "shield": 0, "armor": 0, "cargo": 0, "speed": 0, "fuel": 0},
                    "405": {"amount": 0, "weapon": 0, "shield": 0, "armor": 0, "cargo": 0, "speed": 0, "fuel": 0},
                    "406": {"amount": 0, "weapon": 0, "shield": 0, "armor": 0, "cargo": 0, "speed": 0, "fuel": 0},
                    "407": {"amount": 0, "weapon": 0, "shield": 0, "armor": 0, "cargo": 0, "speed": 0, "fuel": 0},
                    "408": {"amount": 0, "weapon": 0, "shield": 0, "armor": 0, "cargo": 0, "speed": 0, "fuel": 0}
                },
                "missiles": {"502": {"amount": 0}, "503": {"amount": 0}},
                "bonuses": {
                    "recycleAttackerFleet": 0,
                    "moonChanceIncrease": 0,
                    // "lifeformProtection": 0,
                    "spaceDockExtender": 0,
                    "denCapacity": {"metal": 0, "crystal": 0, "deuterium": 0},
                    "characterClassBooster": {"1": 0, "2": 0, "3": 0}
                },
                "fleetspeed": {{ $fleetSpeedIncrement }}
            }
            var apiCommonData = [["coords", "{{ $planet->getPlanetCoordinates()->asString() }}"], ["characterClassId", {{ $player->getUser()->character_class ?? 0 }}]];
            var apiTechData = [[109, 1], [115, 5], [110, 0], [117, 3], [111, 6], [118, 0], [114, 0]];
            var apiDefenseData = [[401, 1], [402, 1]];
            var apiShipBaseData = [[202, 0], [204, 0], [205, 1], [208, 0], [210, 6], [212, 2], [217, 0]];
        </script>

        <div id="fleet1">
            <div id="inhalt">
                <div id="zeuch666" style="display:none;">
                    <div id="sftcontainer">
                        <div id="fleetzOverview">
                            <table id="fleetTemplates" class="list">
                                <tbody>
                                <tr class="separator alt">
                                    <th class="textCenter fleet_id">#</th>
                                    <th class="fleet_name">Name</th>
                                    <th class="fleet_actions">Actions</th>
                                    <th class="textCenter fleet_id">#</th>
                                    <th class="fleet_name">Name</th>
                                    <th class="fleet_actions">Actions</th>
                                </tr>
                                <tr>
                                    <td colspan="3"></td>

                                    <td colspan="3"></td>

                                </tr>
                                </tbody>
                            </table>
                            <a href="javascript: void(0);" class="btn_blue float_right overlay" id="addNewTpl"
                               onclick="setShipsFleet({&quot;202&quot;:0,&quot;203&quot;:0,&quot;204&quot;:0,&quot;205&quot;:0,&quot;206&quot;:0,&quot;207&quot;:0,&quot;208&quot;:0,&quot;209&quot;:0,&quot;210&quot;:0,&quot;211&quot;:0,&quot;212&quot;:0,&quot;213&quot;:0,&quot;214&quot;:0,&quot;215&quot;:0,&quot;218&quot;:0,&quot;219&quot;:0}, &quot;&quot;, 0)"
                               data-overlay-inline="#fleetTemplatesEdit" data-overlay-title="Add new template">
                                Add new template
                            </a>
                            <br class="clearfloat">
                        </div><!-- #fleetzOverview -->
                        <div id="fleetTemplatesEdit" style="display:none;">
                            <form method="POST" action="#" name="submit_std" id="submit_std" value="1">
                                <input type="hidden" name="open_std" value="1">
                                <input type="hidden" name="template_id" id="template_id" value="0">
                                <input type="hidden" name="mode" value="save">
                                <label class="fleet_tpl_name">Name</label>
                                <input size="20" maxlength="30" type="text" class="w200 textinput" name="template_name"
                                       id="template_name">
                                <table cellpadding="0" cellspacing="0" class="list ship_selection_table" id="mail">
                                    <tbody>
                                    <tr class="alt">
                                        <td class="ship_txt_row textLeft images">
                                            <div class="shipImage float_left">
                                                <img class="tech204" width="28" height="28" alt="Light Fighter" title="Light Fighter"
                                                     src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif">
                                            </div>
                                            <p>
                                                Light Fighter
                                            </p>
                                        </td>
                                        <td class="ship_input_row shipValue">
                                            <input type="text" pattern="[0-9,.]*" class="textRight textinput" size="3"
                                                   id="ship204" name="ship[204]" onfocus="clearInput(this);"
                                                   onblur="checkIntInput(this, 0, null);"
                                                   onkeyup="checkIntInput(this, 0, null);">
                                        </td>
                                        <td class="ship_txt_row textLeft images">
                                            <div class="shipImage float_left">
                                                <img class="tech205" width="28" height="28" alt="Heavy Fighter" title="Heavy Fighter"
                                                     src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif">
                                            </div>
                                            <p>
                                                Heavy Fighter
                                            </p>
                                        </td>
                                        <td class="ship_input_row shipValue">
                                            <input type="text" pattern="[0-9,.]*" class="textRight textinput" size="3"
                                                   id="ship205" name="ship[205]" onfocus="clearInput(this);"
                                                   onblur="checkIntInput(this, 0, null);"
                                                   onkeyup="checkIntInput(this, 0, null);">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="ship_txt_row textLeft images">
                                            <div class="shipImage float_left">
                                                <img class="tech206" width="28" height="28" alt="Cruiser" title="Cruiser"
                                                     src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif">
                                            </div>
                                            <p>
                                                Cruiser
                                            </p>
                                        </td>
                                        <td class="ship_input_row shipValue">
                                            <input type="text" pattern="[0-9,.]*" class="textRight textinput" size="3"
                                                   id="ship206" name="ship[206]" onfocus="clearInput(this);"
                                                   onblur="checkIntInput(this, 0, null);"
                                                   onkeyup="checkIntInput(this, 0, null);">
                                        </td>
                                        <td class="ship_txt_row textLeft images">
                                            <div class="shipImage float_left">
                                                <img class="tech207" width="28" height="28" alt="Battleship" title="Battleship"
                                                     src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif">
                                            </div>
                                            <p>
                                                Battleship
                                            </p>
                                        </td>
                                        <td class="ship_input_row shipValue">
                                            <input type="text" pattern="[0-9,.]*" class="textRight textinput" size="3"
                                                   id="ship207" name="ship[207]" onfocus="clearInput(this);"
                                                   onblur="checkIntInput(this, 0, null);"
                                                   onkeyup="checkIntInput(this, 0, null);">
                                        </td>
                                    </tr>
                                    <tr class="alt">
                                        <td class="ship_txt_row textLeft images">
                                            <div class="shipImage float_left">
                                                <img class="tech215" width="28" height="28" alt="Battlecruiser" title="Battlecruiser"
                                                     src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif">
                                            </div>
                                            <p>
                                                Battlecruiser
                                            </p>
                                        </td>
                                        <td class="ship_input_row shipValue">
                                            <input type="text" pattern="[0-9,.]*" class="textRight textinput" size="3"
                                                   id="ship215" name="ship[215]" onfocus="clearInput(this);"
                                                   onblur="checkIntInput(this, 0, null);"
                                                   onkeyup="checkIntInput(this, 0, null);">
                                        </td>
                                        <td class="ship_txt_row textLeft images">
                                            <div class="shipImage float_left">
                                                <img class="tech211" width="28" height="28" alt="Bomber" title="Bomber"
                                                     src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif">
                                            </div>
                                            <p>
                                                Bomber
                                            </p>
                                        </td>
                                        <td class="ship_input_row shipValue">
                                            <input type="text" pattern="[0-9,.]*" class="textRight textinput" size="3"
                                                   id="ship211" name="ship[211]" onfocus="clearInput(this);"
                                                   onblur="checkIntInput(this, 0, null);"
                                                   onkeyup="checkIntInput(this, 0, null);">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="ship_txt_row textLeft images">
                                            <div class="shipImage float_left">
                                                <img class="tech213" width="28" height="28" alt="Destroyer" title="Destroyer"
                                                     src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif">
                                            </div>
                                            <p>
                                                Destroyer
                                            </p>
                                        </td>
                                        <td class="ship_input_row shipValue">
                                            <input type="text" pattern="[0-9,.]*" class="textRight textinput" size="3"
                                                   id="ship213" name="ship[213]" onfocus="clearInput(this);"
                                                   onblur="checkIntInput(this, 0, null);"
                                                   onkeyup="checkIntInput(this, 0, null);">
                                        </td>
                                        <td class="ship_txt_row textLeft images">
                                            <div class="shipImage float_left">
                                                <img class="tech214" width="28" height="28" alt="Deathstar" title="Deathstar"
                                                     src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif">
                                            </div>
                                            <p>
                                                Deathstar
                                            </p>
                                        </td>
                                        <td class="ship_input_row shipValue">
                                            <input type="text" pattern="[0-9,.]*" class="textRight textinput" size="3"
                                                   id="ship214" name="ship[214]" onfocus="clearInput(this);"
                                                   onblur="checkIntInput(this, 0, null);"
                                                   onkeyup="checkIntInput(this, 0, null);">
                                        </td>
                                    </tr>
                                    <tr class="alt">
                                        <td class="ship_txt_row textLeft images">
                                            <div class="shipImage float_left">
                                                <img class="tech218" width="28" height="28" alt="Reaper" title="Reaper"
                                                     src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif">
                                            </div>
                                            <p>
                                                Reaper
                                            </p>
                                        </td>
                                        <td class="ship_input_row shipValue">
                                            <input type="text" pattern="[0-9,.]*" class="textRight textinput" size="3"
                                                   id="ship218" name="ship[218]" onfocus="clearInput(this);"
                                                   onblur="checkIntInput(this, 0, null);"
                                                   onkeyup="checkIntInput(this, 0, null);">
                                        </td>
                                        <td class="ship_txt_row textLeft images">
                                            <div class="shipImage float_left">
                                                <img class="tech219" width="28" height="28" alt="Pathfinder" title="Pathfinder"
                                                     src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif">
                                            </div>
                                            <p>
                                                Pathfinder
                                            </p>
                                        </td>
                                        <td class="ship_input_row shipValue">
                                            <input type="text" pattern="[0-9,.]*" class="textRight textinput" size="3"
                                                   id="ship219" name="ship[219]" onfocus="clearInput(this);"
                                                   onblur="checkIntInput(this, 0, null);"
                                                   onkeyup="checkIntInput(this, 0, null);">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="ship_txt_row textLeft images">
                                            <div class="shipImage float_left">
                                                <img class="tech202" width="28" height="28" alt="Small Cargo" title="Small Cargo"
                                                     src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif">
                                            </div>
                                            <p>
                                                Small Cargo
                                            </p>
                                        </td>
                                        <td class="ship_input_row shipValue">
                                            <input type="text" pattern="[0-9,.]*" class="textRight textinput" size="3"
                                                   id="ship202" name="ship[202]" onfocus="clearInput(this);"
                                                   onblur="checkIntInput(this, 0, null);"
                                                   onkeyup="checkIntInput(this, 0, null);">
                                        </td>
                                        <td class="ship_txt_row textLeft images">
                                            <div class="shipImage float_left">
                                                <img class="tech203" width="28" height="28" alt="Large Cargo" title="Large Cargo"
                                                     src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif">
                                            </div>
                                            <p>
                                                Large Cargo
                                            </p>
                                        </td>
                                        <td class="ship_input_row shipValue">
                                            <input type="text" pattern="[0-9,.]*" class="textRight textinput" size="3"
                                                   id="ship203" name="ship[203]" onfocus="clearInput(this);"
                                                   onblur="checkIntInput(this, 0, null);"
                                                   onkeyup="checkIntInput(this, 0, null);">
                                        </td>
                                    </tr>
                                    <tr class="alt">
                                        <td class="ship_txt_row textLeft images">
                                            <div class="shipImage float_left">
                                                <img class="tech208" width="28" height="28" alt="Colony Ship" title="Colony Ship"
                                                     src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif">
                                            </div>
                                            <p>
                                                Colony Ship
                                            </p>
                                        </td>
                                        <td class="ship_input_row shipValue">
                                            <input type="text" pattern="[0-9,.]*" class="textRight textinput" size="3"
                                                   id="ship208" name="ship[208]" onfocus="clearInput(this);"
                                                   onblur="checkIntInput(this, 0, null);"
                                                   onkeyup="checkIntInput(this, 0, null);">
                                        </td>
                                        <td class="ship_txt_row textLeft images">
                                            <div class="shipImage float_left">
                                                <img class="tech209" width="28" height="28" alt="Recycler" title="Recycler"
                                                     src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif">
                                            </div>
                                            <p>
                                                Recycler
                                            </p>
                                        </td>
                                        <td class="ship_input_row shipValue">
                                            <input type="text" pattern="[0-9,.]*" class="textRight textinput" size="3"
                                                   id="ship209" name="ship[209]" onfocus="clearInput(this);"
                                                   onblur="checkIntInput(this, 0, null);"
                                                   onkeyup="checkIntInput(this, 0, null);">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="ship_txt_row textLeft images">
                                            <div class="shipImage float_left">
                                                <img class="tech210" width="28" height="28" alt="Espionage Probe" title="Espionage Probe"
                                                     src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif">
                                            </div>
                                            <p>
                                                Espionage Probe
                                            </p>
                                        </td>
                                        <td class="ship_input_row shipValue">
                                            <input type="text" pattern="[0-9,.]*" class="textRight textinput" size="3"
                                                   id="ship210" name="ship[210]" onfocus="clearInput(this);"
                                                   onblur="checkIntInput(this, 0, null);"
                                                   onkeyup="checkIntInput(this, 0, null);">
                                        </td>
                                    </tr>
                                    <tr class="alt">
                                        <td colspan="4" class="textRight name">
                                            <a href="javascript: void(0);"
                                               class="tooltip js_hideTipOnMobile standardFleetReset float_right icon_link"
                                               title="Delete template/input">
                                                <span class="icon icon_trash"></span>
                                            </a>
                                            <a href="javascript: void(0);"
                                               class="tooltip js_hideTipOnMobile standardFleetSubmit float_right icon_link"
                                               title="Save template">
                                                <span class="icon icon_checkmark"></span>
                                            </a>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </form>
                        </div><!-- #fleetTemplatesEdit -->
                    </div>
                    <script type="text/javascript">
                        (function ($) {
                            initStandardFleet();
                        })(jQuery);
                    </script>
                </div>

                <div id="planet" class="planet-header ">
                    <h2>Fleet Dispatch I - {{ $planet->getPlanetName() }}</h2>
                    <a class="toggleHeader" data-name="fleet1">
                        <img alt="" src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" height="22" width="22">
                    </a>
                </div>
                <div class="fleetStatus">
                    <div id="slots" class="fleft">
                        <div class="fleft">
                            <span class="tooltip advice {{ $fleetSlotsInUse >= $fleetSlotsMax ? 'overmark' : '' }}" title="Used/Total fleet slots"><span>Fleets:</span> {{ $fleetSlotsInUse }}/{{ $fleetSlotsMax }}</span>
                            <div class="tooltip bonus dark_highlight_tablet" title="+ 2 Fleet slots because of General">
                                <span class="sprite characterclass small warrior"></span>
                            </div>
                        </div>
                        <div class="fleft">
                                        <span class="tooltip advice {{ $expeditionSlotsInUse >= $expeditionSlotsMax ? 'overmark' : '' }}" title="Used/Total expedition slots">
                            <span>Expeditions:</span>
                            {{ $expeditionSlotsInUse }}/{{ $expeditionSlotsMax }}
                        </span>
                        </div>
                    </div>

                    <div id="movements" class="fright">
                        <a class="tooltip js_hideTipOnMobile dark_highlight_tablet" title="To fleet movement"
                           href="{{ route('fleet.movement') }}">
                            <img src="/img/icons/f9cb590cdf265f499b0e2e5d91fc75.gif">
                            <span>Fleet movement</span>
                        </a>
                    </div>
                    <br class="clearfloat">
                </div>
                <div class="fleetStatus">
                    <div id="slots" class="fleft">
                        <div class="fleft tactical_retreat">
                            <a title="Tactical retreat|Fleets are able to automatically retreat if they are attacked by a superior force five times stronger than themselves. The crucial factor in this are the attacker&amp;#96;s fleet points in comparison to your fleet points. Defense facilities are not considered.<br />
<br />
Civil ships only count 25%, solar satellites and espionage probes are not considered. <br />
<br />
Select the option *never* if you would like to deactivate the automatic retreat.<br />
<br />
Held fleets are in principle not able to retreat. Death Stars, Espionage Probes and Solar Satellites are also unable to retreat.<br />
<br />
Use the Admiral to enable your fleets to retreat from forces three times bigger than your own.<br />
<br />
The &amp;#96;tactical retreat&amp;#96; option ends with 500,000 points." href="javascript:void(0);"
                               class="tooltipHTML tooltipRight help"></a>
                            <form class="fleft" name="tacticalRetreat" method="POST" action="">
                            <span class="tooltipHTML tooltipRight" title="Tactical retreat|Fleets are able to automatically retreat if they are attacked by a superior force five times stronger than themselves. The crucial factor in this are the attacker&amp;#96;s fleet points in comparison to your fleet points. Defense facilities are not considered.<br />
<br />
Civil ships only count 25%, solar satellites and espionage probes are not considered. <br />
<br />
Select the option *never* if you would like to deactivate the automatic retreat.<br />
<br />
Held fleets are in principle not able to retreat. Death Stars, Espionage Probes and Solar Satellites are also unable to retreat.<br />
<br />
Use the Admiral to enable your fleets to retreat from forces three times bigger than your own.<br />
<br />
The &amp;#96;tactical retreat&amp;#96; option ends with 500,000 points.">
                                Tactical retreat:
                            </span>
                                <input onclick="ajaxFormSubmit('tacticalRetreat', '{{ route('overview.index') }}#TODO_tacticalRetreat&amp;tacticalRetreatState=0');"
                                       type="radio" name="tacticalRetreat" value="0">
                                Never
                                <input onclick="ajaxFormSubmit('tacticalRetreat', '{{ route('overview.index') }}#TODO_tacticalRetreat&amp;tacticalRetreatState=5');"
                                       checked="&quot;checked&quot;" type="radio" name="tacticalRetreat" value="5"> 5:1
                                <input type="radio" disabled="disabled" name="tacticalRetreat">
                                <a href="{{ route('premium.index', ['openDetail' => '3']) }}"
                                   class="disabled tooltipHTML"
                                   title="Tactical retreat|Use the Admiral to enable your fleets to retreat from forces three times bigger than your own.">
                                    3:1
                                </a>
                            </form>
                        </div>
                        <div class="fleft tooltip" title="Show Deuterium usage per tactical retreat">
                        <span>
                            Deuterium consumption:
                        </span>
                            5
                        </div>
                        <br class="clearfloat">
                    </div>
                    <br class="clearfloat">
                </div>
                <div class="c-left"></div>
                <div class="c-right"></div>
                @if ($shipAmount == 0)
                    <div id="warning">
                        <h3>{{ __('t_ingame.fleet.dispatch_impossible') }}</h3>
                        <p>
                            <span class="icon icon_warning"></span>
                            {{ __('t_ingame.fleet.no_ships') }}
                        </p>
                    </div>
                @else
                    <div class="fleetStatus" id="statusBarFleet">
                        <ul>
                            <li><span class="title">{{ __('t_ingame.fleet.mission_label') }}:</span> <span
                                        class="missionName">{{ __('t_ingame.fleet.no_selection') }}</span></li>
                            <li><span class="title">{{ __('t_ingame.fleet.target_label') }}:</span> <span class="targetName">[{{ $planet->getPlanetCoordinates()->asString() }}] <figure
                                            class="planetIcon {{ $planet->isPlanet() ? 'planet' : 'moon' }} tooltip js_hideTipOnMobile"
                                            title="{{ $planet->isPlanet() ? 'Planet' : 'Moon' }}"></figure>{{ $planet->getPlanetName() }}</span></li>
                            <li><span class="title">{{ __('t_ingame.fleet.player_name_label') }}:</span> <span
                                        class="targetPlayerName">{{ $player->getUsername() }}</span></li>
                        </ul>
                    </div>
                    <div id="buttonz">
                        <div class="content">
                            <form name="shipsChosen" id="shipsChosen" method="post"
                                  action="{{ route('overview.index') }}#TODO_page=fleet2">
                                <div id="technologies">
                                    <div id="battleships">
                                        <div class="header"><h2>{{ __('t_ingame.fleet.combat_ships') }}</h2></div>
                                        <ul id="military" class="iconsUNUSED">
                                            @php /** @var OGame\ViewModels\UnitViewModel $object */ @endphp
                                            @foreach ($units[0] as $object)
                                                <li class="technology {{ $object->object->class_name }} interactive hasDetails tooltip hideTooltipOnMouseenter js_hideTipOnMobile ipiHintable"
                                                    data-technology="{{ $object->object->id }}"
                                                    data-status="{{ $object->amount == 0 ? 'off' : 'on' }}"
                                                    data-is-spaceprovider="" aria-label="{{ $object->object->title }}"
                                                    title="{{ $object->object->title }} ({{ $object->amount }})"
                                                    data-ipi-hint="ipiFleetselect{{ $object->object->class_name }}">
												<span class="icon sprite sprite_small small {{ $object->object->class_name }}">
													<span class="amount" data-value="{{ $object->amount }}"
                                                          data-bonus="0">
														<span>{{ \OGame\Facades\AppUtil::formatNumberShort($object->amount) }}</span> <span
                                                                class="bonus"></span>
													</span>
												</span>
                                                    <input type="text" name="{{ $object->object->class_name }}"
                                                           data-ipi-highlight-step="ipiFleetselect{{ $object->object->class_name }}" {{ $object->amount == 0 ? 'disabled' : '' }}>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div id="civilships">
                                        <div class="header"><h2>{{ __('t_ingame.fleet.civil_ships') }}</h2></div>
                                        <ul id="civil" class="iconsUNUSED">
                                            @php /** @var OGame\ViewModels\QueueUnitViewModel $object */ @endphp
                                            @foreach ($units[1] as $object)
                                                <li class="technology {{ $object->object->class_name }} interactive hasDetails tooltip hideTooltipOnMouseenter js_hideTipOnMobile ipiHintable"
                                                    data-technology="{{ $object->object->id }}"
                                                    data-status="{{ $object->amount == 0 ? 'off' : 'on' }}"
                                                    data-is-spaceprovider="" aria-label="{{ $object->object->title }}"
                                                    title="{{ $object->object->title }} ({{ $object->amount }})"
                                                    data-ipi-hint="ipiFleetselect{{ $object->object->class_name }}">

    <span class="icon sprite sprite_small small {{ $object->object->class_name }}">



                    <span class="amount" data-value="{{ $object->amount }}" data-bonus="0">
                <span>{{ $object->amount }}</span> <span class="bonus"></span>
            </span>
            </span>

                                                    <input type="text" name="{{ $object->object->class_name }}"
                                                           data-ipi-highlight-step="ipiFleetselect{{ $object->object->class_name }}" {{ $object->amount == 0 ? 'disabled' : '' }}>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </form>
                            <div class="clearfloat"></div>
                            <div id="allornone" style="position:relative">
                                <div class="allornonewrap">
                                    <div class="secondcol fleft">
                                <span class="send_all">
                                    <a id="sendall" class="tooltip js_hideTipOnMobile" title="Select all ships">
                                        <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif">
                                    </a>
                                </span>
                                        <span class="send_none">
                                    <a id="resetall" class="tooltip js_hideTipOnMobile" title="Reset choice">
                                        <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif">
                                    </a>
                                </span>
                                        <div class="clearfloat"></div>
                                    </div>
                                    <div class="firstcol fleft">
                                        <a id="combatunits" class="overlay dark_highlight_tablet"
                                           data-overlay-inline="#zeuch666" data-overlay-title="Edit standard fleets">
                                            <span class="icon icon_combatunits"></span>
                                            {{ __('t_ingame.fleet.standard_fleets') }}
                                        </a>
                                        <select class="combatunits" size="1" id="standardfleet">
                                            <option>-</option>
                                        </select>
                                    </div>
                                    <span class="show_fleet_apikey tooltipCustom tpd-hideOnClickOutside" title="">
                            </span>
                                    <a id="continueToFleet2" class="continue off" href="">
                                        <span class="ipiHintable" data-ipi-hint="ipiFleetContinueToPage2"
                                              data-ipi-highlight-step="ipiFleetContinueToPage2">{{ __('t_ingame.fleet.continue') }}</span>
                                    </a>
                                    <div class="clearfloat"></div>
                                    <p class="info">{{ __('t_ingame.fleet.no_selection') }}</p>
                                </div>
                            </div>
                            <div class="footer"></div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div id="fleet2" style="display: none;">
            <input name="type" id="type" type="hidden" value="1">
            <input name="mission" type="hidden" value="0">
            <input name="union" type="hidden" value="0">

            <div id="inhalt">
                <div id="planet" class="planet-header ">
                    <h2>{{ __('t_ingame.fleet.dispatch_2_title') }} - {{ $planet->getPlanetName() }}</h2>
                    <a class="toggleHeader" data-name="fleet2">
                        <img alt="" src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" height="22" width="22">
                    </a>
                </div>
                <div class="c-left shortCorner"></div>
                <div class="c-right shortCorner"></div>
                <div class="fleetStatus" id="statusBarFleet">
                    <ul>
                        <li><span class="title">{{ __('t_ingame.fleet.mission_label') }}:</span> <span
                                    class="missionName">{{ __('t_ingame.fleet.no_selection') }}</span></li>
                        <li><span class="title">{{ __('t_ingame.fleet.target_label') }}:</span> <span class="targetName">[{{ $planet->getPlanetCoordinates()->asString() }}] <figure
                                        class="planetIcon {{ $planet->isPlanet() ? 'planet' : 'moon' }} tooltip js_hideTipOnMobile" title="{{ $planet->isPlanet() ? 'Planet' : 'Moon' }}"></figure>{{ $planet->getPlanetName() }}</span>
                        </li>
                        <li><span class="title">{{ __('t_ingame.fleet.player_name_label') }}:</span> <span
                                    class="targetPlayerName">{{ $player->getUsername() }}</span></li>
                    </ul>
                </div>
                <div id="buttonz" class="sortable ui-sortable">
                    <!-- START: Block 1 -->

                    <!-- END: Block 1 -->
                    <!-- START: Block 2 -->

                    <!-- END: Block 2 -->
                    <!-- START: Block 3 -->

                    <!-- END: Block 3 -->
                    <div id="fleetboxdestination" class="ui-state-default">
                        <div class="move-box-wrapper">
                            <div class="move-box ui-sortable-handle"></div>
                        </div>
                        <div class="header"></div>
                        <div class="content">
                            <div class="ajax_loading" style="display: none;">
                                <div class="ajax_loading_overlay">
                                    <div class="ajax_loading_indicator"></div>
                                </div>
                            </div>
                            <table cellpadding="0" cellspacing="0" id="mission">
                                <tbody>
                                <tr>
                                    <th><h2>{{ __('t_ingame.fleet.origin') }}:</h2></th>
                                    <th></th>
                                    <th><h2>{{ __('t_ingame.fleet.destination') }}:</h2></th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <td id="start" class="border5px">
                                        <div class="planetname">{{ $planet->getPlanetName() }}</div>
                                        <div class="target">
                                            <a class="planet_source{{ $planet->isPlanet() ? '_selected' : '' }}">
                                                <span class="textlabel">{{ __('t_ingame.fleet.planet') }}</span>
                                            </a>
                                            <a class="moon_source{{ $planet->isMoon() ? '_selected' : '' }}">
                                                <span class="textlabel">{{ __('t_ingame.fleet.moon') }}</span>
                                            </a>
                                            <br class="clearfloat">
                                        </div>
                                        <div class="coords">
                                            {{ __('t_ingame.fleet.coordinates') }}:
                                            <span style="color: #ffffff; font-weight: bold;">{{ $planet->getPlanetCoordinates()->asString() }}</span>
                                        </div>
                                    </td>
                                    <td id="distance">
                                        <div id="distanceValue">5</div>
                                        <div class="coords">{{ __('t_ingame.fleet.distance') }}</div>
                                    </td>
                                    <td id="target" class="border5px">
                                        <div class="planetname" id="targetPlanetName">{{ $planet->getPlanetName() }}</div>
                                        <div class="target">
                                            <a class="planet{{ $planet->isPlanet() ? '_selected' : '' }}" href="" id="pbutton">
                                                <span class="textlabel">{{ __('t_ingame.fleet.planet') }}</span>
                                            </a>
                                            <a class="moon{{ $planet->isMoon() ? '_selected' : '' }}" href="" id="mbutton">
                                                <span class="textlabel">{{ __('t_ingame.fleet.moon') }}</span>
                                            </a>
                                            <a class="debris" href="" id="dbutton">
                                                <span class="textlabel">{{ __('t_ingame.fleet.debris_field_lower') }}</span>
                                            </a>
                                            <br class="clearfloat">
                                        </div>
                                        <div class="coords">
                                            {{ __('t_ingame.fleet.coordinates') }}:
                                            <br>
                                            <div class="coordsSection ipiHintable"
                                                 data-ipi-hint="ipiFleetDestinationCoordsSystem">
                                                <input name="galaxy" id="galaxy" type="text" pattern="[0-9]*"
                                                       class="galaxy hideNumberSpin" size="1" value="7">
                                            </div>
                                            <div class="coordsSection ipiHintable"
                                                 data-ipi-hint="ipiFleetDestinationCoordsGalaxy">
                                                <input name="system" id="system" type="text" pattern="[0-9]*"
                                                       class="system hideNumberSpin" size="3" value="158">
                                            </div>
                                            <div class="coordsSection ipiHintable"
                                                 data-ipi-hint="ipiFleetDestinationCoordsPosition">
                                                <input name="position" id="position" type="text" pattern="[0-9]*"
                                                       class="planet hideNumberSpin" size="2" value="10"
                                                       data-ipi-highlight-step="ipiFleetDestinationCoordsPosition">
                                            </div>
                                        </div>
                                    </td>
                                    <td id="shortcuts">
                                        <div>
                                            <span id="shortlinks tips">{{ __('t_ingame.fleet.shortcuts') }}:</span>
                                            <div class="glow">
                                                <select size="1" class="planets" id="slbox">
                                                    <option value="-">-</option>
                                                    @foreach ($player->planets->all() as $planet_record)
                                                        @if ($planet_record->getPlanetId() !== $planet->getPlanetId())
                                                        <option
                                                            value="{{ $planet_record->getPlanetCoordinates()->galaxy }}#{{ $planet_record->getPlanetCoordinates()->system }}#{{ $planet_record->getPlanetCoordinates()->position }}#{{ $planet_record->getPlanetType() }}#{{ $planet_record->getPlanetName() }}"
                                                            data-html-prepend="<figure class=&quot;planetIcon {{ $planet_record->getPlanetType() === PlanetType::Planet ? 'planet' : 'moon' }} tooltip js_hideTipOnMobile&quot; title=&quot;{{ $planet_record->getPlanetType() === PlanetType::Planet ? 'Planet' : 'Moon' }}&quot;></figure>"
                                                            >
                                                            {{ $planet_record->getPlanetName() }} [{{ $planet_record->getPlanetCoordinates()->asString() }}]
                                                        </option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div style="padding-top: 12px;">
                                            {{-- TODO: show the player their synchronized arrival time when a union is
                                                 selected. The live arrival time update logic is in ingame.js
                                                 (FleetDispatcher.prototype refreshFleetTimes / MISSION_UNIONATTACK block). --}}
                                            <span id="combatunits tips">{{ __('t_ingame.fleet.combat_forces') }}:</span>
                                            <div class="glow">
                                                <select size="1" class="combatunits" id="aksbox"
                                                        name="acsValues">
                                                    <option value="-">-</option>
                                                    @foreach ($availableUnions as $union)
                                                        <option value="{{ $union['galaxy'] }}#{{ $union['system'] }}#{{ $union['position'] }}#{{ $union['planet_type'] }}#{{ $union['name'] }}#{{ $union['id'] }}">
                                                            {{ $union['name'] }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <br class="clearfloat">
                            <div class="footer"></div>
                        </div>
                    </div>
                    <div id="fleetboxmission" class="ui-state-default">

                        <div class="move-box-wrapper">
                            <div class="move-box ui-sortable-handle"></div>
                        </div>
                        <div class="header">
                            <h2>{{ __('t_ingame.fleet.select_mission') }}:</h2>
                        </div>
                        <div class="content">
                            <div class="ajax_loading" style="display: none;">
                                <div class="ajax_loading_overlay">
                                    <div class="ajax_loading_indicator"></div>
                                </div>
                            </div>
                            <div id="attackMissionsDisabledBashingLimit" style="display: none;"><span
                                        class="icon icon_warning"></span> {{ __('t_ingame.fleet.bashing_disabled') }}
                            </div>
                            <ul id="missions">
                                <li id="button15" class="off ipiHintable" data-ipi-hint="ipiFleetMission15">
                                    <a id="missionButton15" href="" data-mission="15"
                                       data-ipi-highlight-step="ipiFleetMission15">
                                        <span class="textlabel">{{ __('t_ingame.fleet.mission_expedition') }}</span>
                                    </a>
                                </li>
                                <li id="button7" class="off ipiHintable" data-ipi-hint="ipiFleetMission7">
                                    <a id="missionButton7" href="" data-mission="7"
                                       data-ipi-highlight-step="ipiFleetMission7">
                                        <span class="textlabel">{{ __('t_ingame.fleet.mission_colonise') }}</span>
                                    </a>
                                </li>
                                <li id="button8" class="off ipiHintable" data-ipi-hint="ipiFleetMission8">
                                    <a id="missionButton8" href="" data-mission="8"
                                       data-ipi-highlight-step="ipiFleetMission8">
                                        <span class="textlabel">{{ __('t_ingame.fleet.mission_recycle') }}</span>
                                    </a>
                                </li>
                                <li id="button3" class="off ipiHintable" data-ipi-hint="ipiFleetMission3">
                                    <a id="missionButton3" href="" data-mission="3"
                                       data-ipi-highlight-step="ipiFleetMission3">
                                        <span class="textlabel">{{ __('t_ingame.fleet.mission_transport') }}</span>
                                    </a>
                                </li>
                                <li id="button4" class="off ipiHintable" data-ipi-hint="ipiFleetMission4">
                                    <a id="missionButton4" href="" data-mission="4"
                                       data-ipi-highlight-step="ipiFleetMission4">
                                        <span class="textlabel">{{ __('t_ingame.fleet.mission_deploy') }}</span>
                                    </a>
                                </li>
                                <li id="button6" class="off ipiHintable" data-ipi-hint="ipiFleetMission6">
                                    <a id="missionButton6" href="" data-mission="6"
                                       data-ipi-highlight-step="ipiFleetMission6">
                                        <span class="textlabel">{{ __('t_ingame.fleet.mission_espionage') }}</span>
                                    </a>
                                </li>
                                <li id="button5" class="off ipiHintable" data-ipi-hint="ipiFleetMission5">
                                    <a id="missionButton5" href="" data-mission="5"
                                       data-ipi-highlight-step="ipiFleetMission5">
                                        <span class="textlabel">{{ __('t_ingame.fleet.mission_acs_defend') }}</span>
                                    </a>
                                </li>
                                <li id="button1" class="off ipiHintable" data-ipi-hint="ipiFleetMission1">
                                    <a id="missionButton1" href="" data-mission="1"
                                       data-ipi-highlight-step="ipiFleetMission1">
                                        <span class="textlabel">{{ __('t_ingame.fleet.mission_attack') }}</span>
                                    </a>
                                </li>
                                <li id="button2" class="off ipiHintable" data-ipi-hint="ipiFleetMission2">
                                    <a id="missionButton2" href="" data-mission="2"
                                       data-ipi-highlight-step="ipiFleetMission2">
                                        <span class="textlabel">{{ __('t_ingame.fleet.mission_acs_attack') }}</span>
                                    </a>
                                </li>
                                <li id="button9" class="off ipiHintable" data-ipi-hint="ipiFleetMission9">
                                    <a id="missionButton9" href="" data-mission="9"
                                       data-ipi-highlight-step="ipiFleetMission9">
                                        <span class="textlabel">{{ __('t_ingame.fleet.mission_destroy_moon') }}</span>
                                    </a>
                                </li>
                            </ul>
                            <br class="clearfloat">
                            <div id="missionNameWrapper" class="off">
                                {{ __('t_ingame.fleet.mission_label') }}:
                                <span id="missionName" class="missionName">{{ __('t_ingame.fleet.no_selection') }}</span>
                                <p class="mission_description"></p>
                            </div>
                            <div class="footer"></div>
                        </div>
                    </div>
                    <div id="fleetboxbriefingandresources" class="ui-state-default">
                        <div class="move-box-wrapper">
                            <div class="move-box ui-sortable-handle"></div>
                        </div>
                        <div class="header"></div>
                        <div class="content">
                            <div class="ajax_loading" style="display: none;">
                                <div class="ajax_loading_overlay">
                                    <div class="ajax_loading_indicator"></div>
                                </div>
                            </div>
                            <form name="sendForm" method="post"
                                  action="{{ route('overview.index') }}#TODO_page=ingame&amp;component=movement">
                                <div id="mission">
                                    <div class="briefing_overlay">{{ __('t_ingame.fleet.cannot_start_mission') }}</div>
                                    <div style="display:none">
                                        <input name="galaxy" type="hidden" value="7">
                                        <input name="system" type="hidden" value="158">
                                        <input name="position" type="hidden" value="10">
                                        <input name="type" type="hidden" value="1">
                                        <input name="mission" type="hidden" value="0">
                                        <input name="union2" type="hidden" value="0">
                                        <input name="holdingOrExpTime" id="holdingOrExpTime" type="hidden" value="0">
                                        <input name="speed" type="hidden" value="10">
                                        <input name="acsValues" type="hidden" value="">
                                        <input name="prioMetal" type="hidden" value="2">
                                        <input name="prioCrystal" type="hidden" value="3">
                                        <input name="prioDeuterium" type="hidden" value="4">
                                        <!-- <input name="prioFood" type="hidden" value="1"> -->

                                    </div>
                                    <div class="missionHeader">{{ __('t_ingame.fleet.briefing') }}:</div>
                                    <div class="missionHeader">{{ __('t_ingame.fleet.load_resources') }}:</div>
                                    <!-- START: Briefing -->
                                    <div id="start" class="border5px">
                                        <ul id="fleetBriefingPart1" class="fleetBriefing">
                                            <li id="fightAfterRetreat" style="display: none;">
                                                <span class="tooltip advice"
                                                      title="If this option is activated, your fleet will also withdraw without a fight if your opponent flees.">Return upon retreat by defenders:</span>
                                                <span class="value" style="vertical-align: middle;">
                            <square-checkbox id="fleetRetreatSquareCheckbox">
                                <input type="checkbox" value="None" id="square-checkboxRetreatAfterDefenderRetreat"
                                       name="retreatAfterDefenderRetreat">
                                <label for="square-checkboxRetreatAfterDefenderRetreat"></label>
                            </square-checkbox>
                        </span>
                                            </li>
                                            <li>
                                                {{ __('t_ingame.fleet.target_label') }}:
                                                <span class="value tooltip active tpd-hideOnClickOutside"
                                                      id="targetPlanet" title="">[{{ $planet->getPlanetCoordinates()->asString() }}] {{ $planet->getPlanetName() }}</span>
                                            </li>
                                            <li>
                                                {{ __('t_ingame.fleet.flight_duration') }}:
                                                <span class="value" id="duration">0:00:00 h</span>
                                            </li>
                                            <li>
                                                {{ __('t_ingame.fleet.arrival') }}: <span class="value"><span
                                                            id="arrivalTime">18.03.24 23:16:15</span> {{ __('t_ingame.fleet.clock') }}</span>
                                            </li>
                                            <li>
                                                {{ __('t_ingame.fleet.return_trip') }}: <span class="value"><span
                                                            id="returnTime">18.03.24 23:16:15</span> {{ __('t_ingame.fleet.clock') }}</span>
                                            </li>
                                            <li>
                                                {{ __('t_ingame.fleet.deuterium_consumption') }}:
                                                <span class="value"><span id="consumption"><span class="undermark">0 (NaN%)</span></span></span>
                                            </li>
                                            <li>
                                                {{ __('t_ingame.fleet.empty_cargobays') }}: <span class="value" id="storage"><span
                                                            class="undermark">0</span></span>
                                            </li>
                                            <li id="holdtimeline" style="display: none;">
                                                {{ __('t_ingame.fleet.hold_time') }}:
                                                <select name="holdingtime" id="holdingtime"
                                                        style="display: none;">
                                                    <option value="0">0</option>
                                                    <option value="1" selected="'selected'">1</option>
                                                    <option value="2">2</option>
                                                    <option value="4">4</option>
                                                    <option value="8">8</option>
                                                    <option value="16">16</option>
                                                    <option value="32">32</option>
                                                </select>
                                            </li>
                                            <li id="expeditiontimeline">
                                                {{ __('t_ingame.fleet.expedition_duration') }}:
                                                <select name="expeditiontime" id="expeditiontime">
                                                    @for ($i = 1; $i <= $player->getResearchLevel('astrophysics'); $i++)
                                                        <option value="{{ $i }}" {{ $i == 1 ? 'selected' : '' }}>{{ $i }}</option>
                                                    @endfor
                                                </select>
                                                <span class="value">h</span>
                                            </li>
                                            <li>
                                                <input type="hidden" name="speed" id="speed" value="10">
                                                {{ __('t_ingame.fleet.speed') }} ({{ __('t_ingame.fleet.max_abbr') }} <span id="maxspeed">1,000,000,000</span>)
                                            </li>
                                        </ul>
                                    </div>
                                    <!-- END: Briefing -->
                                    <!-- START: Resources -->
                                    <div class="border5px">
                                        <div id="resources" class="border5px lifeforms-enabled">
                                            <div class="res_wrap border3px">
                                                <div class="resourceIcon metal tooltip" title="Metal"></div>
                                                <div class="res">
                                                    <input type="text" pattern="[0-9,.]*"
                                                           class="checkThousandSeparator hideNumberSpin" name="metal"
                                                           tabindex="1" id="metal"
                                                           value="{{ $planet->metal()->get() }}">
                                                    <a id="selectMinMetal" class="min">
                                                        <img src="/img/icons/45494a6e18d52e5c60c8fb56dfbcc4.gif">
                                                    </a>
                                                    <a id="selectMaxMetal" class="max">
                                                        <img src="/img/icons/fa0c8ee62604e3af52e6ef297faf3c.gif">
                                                    </a>

                                                </div>
                                            </div>
                                            <div class="res_wrap border3px">
                                                <div class="resourceIcon crystal tooltip" title="Crystal"></div>
                                                <div class="res">
                                                    <input type="text" pattern="[0-9,.]*"
                                                           class="checkThousandSeparator hideNumberSpin" name="crystal"
                                                           id="crystal" value="{{ $planet->crystal()->get() }}"
                                                           tabindex="2">
                                                    <a id="selectMinCrystal" class="min">
                                                        <img src="/img/icons/45494a6e18d52e5c60c8fb56dfbcc4.gif">
                                                    </a>
                                                    <a id="selectMaxCrystal" class="max">
                                                        <img src="/img/icons/fa0c8ee62604e3af52e6ef297faf3c.gif">
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="res_wrap border3px">
                                                <div class="resourceIcon deuterium tooltip" title="Deuterium"></div>
                                                <div class="res">
                                                    <input type="text" pattern="[0-9,.]*"
                                                           class="checkThousandSeparator hideNumberSpin"
                                                           name="deuterium" id="deuterium"
                                                           value="{{ $planet->deuterium()->get() }}" tabindex="3">
                                                    <a id="selectMinDeuterium" class="min">
                                                        <img src="/img/icons/45494a6e18d52e5c60c8fb56dfbcc4.gif">
                                                    </a>
                                                    <a id="selectMaxDeuterium" class="max">
                                                        <img src="/img/icons/fa0c8ee62604e3af52e6ef297faf3c.gif">
                                                    </a>
                                                </div>
                                            </div>
                                            <!-- <div class="res_wrap border3px">
                                                <div class="resourceIcon food tooltip" title="Food"></div>
                                                <div class="res">
                                                    <input type="text" pattern="[0-9,.]*"
                                                           class="checkThousandSeparator hideNumberSpin" name="food"
                                                           id="food" value="0" tabindex="4">
                                                    <a id="selectMinFood" class="min">
                                                        <img src="/img/icons/45494a6e18d52e5c60c8fb56dfbcc4.gif">
                                                    </a>
                                                    <a id="selectMaxFood" class="max">
                                                        <img src="/img/icons/fa0c8ee62604e3af52e6ef297faf3c.gif">
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="fleet_dispatch_toggle_wrap" style="display: none;">
                                                <span>{{ __('t_ingame.fleet.plunder_food') }}:</span>
                                                <toggle-switch>
                                                    <input type="checkbox" value="None" id="lootFoodInput"
                                                           name="lootFoodOnAttack">
                                                    <label for="lootFoodInput"></label>
                                                </toggle-switch>
                                                <script>

                                                </script>
                                            </div> -->
                                            <div id="loadAllResources">
                                                <div class="allResourcesWrap ipiHintable"
                                                     data-ipi-hint="ipiFleetCargoLoadAll">
                                                    <a id="allresources">
                                                        <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif"
                                                             width="32" height="32"
                                                             data-ipi-highlight-step="ipiFleetCargoLoadAll">
                                                    </a>
                                                </div>
                                                {{ __('t_ingame.fleet.all_resources') }}
                                            </div>
                                            <div id="loadRoom">
                                                {{ __('t_ingame.fleet.cargo_bay') }}:
                                                <div class="fleft bar_container" data-current-amount="0"
                                                     data-capacity="0">
                                                    <div class="filllevel_bar filllevel_overmark"></div>
                                                </div>
                                                <div class="tooltip" title="Available space / Max. cargo space">
                                                    <span id="remainingresources"><span
                                                                class="undermark">0</span></span> / <span
                                                            id="maxresources">0</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- END: Resources -->
                                    <!-- START: Speed percentage -->
                                    <div class="percentageBarWrapper">
                                        <div id="speedPercentage" class="percentageBar"
                                             style="float:left;width:600px;margin-left:20px" value="10" steps="{{ 100 / $fleetSpeedIncrement }}"
                                             stepsize="{{ $fleetSpeedIncrement }}" minvalue="{{ $fleetSpeedIncrement / 100 }}" usehalfstep="{{ $fleetSpeedIncrement == 5 ? 'true' : 'false' }}"></div>
                                        <div id="percentStatus" style="float:left; font-size:12px;"></div>
                                        <script>
                                            $('.percentSelector').bind('click', function () {
                                                var callback = $(this).attr('onpercentchange');
                                                var x = eval(callback);
                                                x($(this).attr("percent"));
                                            });
                                        </script>
                                        <div id="additionalFleetSpeedInfo" style="margin-top:43px"></div>
                                    </div>
                                    <!-- END: Speed percentage -->
                                </div>
                                <br class="clearfloat">
                                <div id="naviActions">
                                    <a id="sendFleet" class="start ipiHintable off" href=""
                                       data-ipi-hint="ipiFleetSend">
                                        <span style="padding-top:9px;">{{ __('t_ingame.fleet.send_fleet') }}</span>
                                    </a>
                                    <a id="backToFleet1" class="back" href="">
                                        <span style="font-size:12px; text-transform:uppercase;">{{ __('t_ingame.fleet.back') }}</span>
                                    </a>
                                    <br class="clearfloat">
                                </div>
                            </form>

                            <br class="clearfloat">
                            <div class="footer"></div>
                        </div>
                        <br class="clearfloat">
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
