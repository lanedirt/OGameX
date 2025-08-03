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
            var MAX_SYSTEM = 499;
            var MAX_POSITION = 16;
            var SPEEDFAKTOR_FLEET_PEACEFUL = {{ $settings->fleetSpeed() }};
            var SPEEDFAKTOR_FLEET_WAR = {{ $settings->fleetSpeed() }};
            var SPEEDFAKTOR_FLEET_HOLDING = {{ $settings->fleetSpeed() }};
            var PLANETTYPE_PLANET = 1;
            var PLANETTYPE_DEBRIS = 2;
            var PLANETTYPE_MOON = 3;
            var EXPEDITION_POSITION = 16;
            var MAX_NUMBER_OF_PLANETS = {{ $player->getMaxPlanetAmount() }};
            var COLONIZATION_ENABLED = true;

            var LOOT_PRIO_METAL = 2;
            var LOOT_PRIO_CRYSTAL = 3;
            var LOOT_PRIO_DEUTERIUM = 4;
            var LOOT_PRIO_FOOD = 1;

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
                "15": "Expedition",
                "7": "Colonisation",
                "8": "Recycle Debris Field",
                "3": "Transport",
                "4": "Deployment",
                "6": "Espionage",
                "5": "ACS Defend",
                "1": "Attack",
                "2": "ACS Attack",
                "9": "Moon Destruction"
            };
            var orderDescriptions = {
                "1": "Attacks the fleet and defense of your opponent.",
                "2": "Honourable battles can become dishonourable battles if strong players enter through ACS. The attacker`s sum of total military points in comparison to the defender`s sum of total military points is the decisive factor here.",
                "3": "Transports your resources to other planets.",
                "4": "Sends your fleet permanently to another planet of your empire.",
                "5": "Defend the planet of your team-mate.",
                "6": "Spy the worlds of foreign emperors.",
                "7": "Colonizes a new planet.",
                "8": "Send your recyclers to a debris field to collect the resources floating around there.",
                "9": "Destroys the moon of your enemy.",
                "15": "Send your ships to the furthest reaches of space to complete exciting quests."
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
            var unions = [];

            var mission = {{ $mission ?? 0}};
            var unionID = 0;
            var speed = 10;

            var missionHold = 5;
            var missionExpedition = 15;

            var holdingTime = 1;
            var expeditionTime = 0;
            var lifeformEnabled = true;
            var metalOnPlanet = {{ $planet->metal()->getRounded() }};
            var crystalOnPlanet = {{ $planet->crystal()->getRounded() }};
            var deuteriumOnPlanet = {{ $planet->deuterium()->getRounded() }};
            var foodOnPlanet = 0;

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
                "LOCA_FLEET_TITLE_MOVEMENTS": "To fleet movement",
                "LOCA_FLEET_MOVEMENT": "Fleet movement",
                "LOCA_FLEET_EDIT_STANDARTFLEET": "Edit standard fleets",
                "LOCA_FLEET_STANDARD": "Standard fleets",
                "LOCA_FLEET_HEADLINE_ONE": "Fleet Dispatch I",
                "LOCA_FLEET_TOOLTIPP_SLOTS": "Used\/Total fleet slots",
                "LOCA_FLEET_FLEETSLOTS": "Fleets",
                "LOCA_FLEET_NO_FREE_SLOTS": "No fleet slots available",
                "LOCA_FLEETSENDING_NO_TARGET": "You have to select a valid target.",
                "LOCA_FLEET_TOOLTIPP_EXP_SLOTS": "Used\/Total expedition slots",
                "LOCA_FLEET_EXPEDITIONS": "Expeditions",
                "LOCA_ALL_NEVER": "Never",
                "LOCA_FLEET_SEND_NOTAVAILABLE": "Fleet dispatch impossible",
                "LOCA_FLEET_NO_SHIPS_ON_PLANET": "There are no ships on this planet.",
                "LOCA_SHIPYARD_HEADLINE_BATTLESHIPS": "Combat ships",
                "LOCA_SHIPYARD_HEADLINE_CIVILSHIPS": "Civil ships",
                "LOCA_FLEET_SELECT_SHIPS_ALL": "Select all ships",
                "LOCA_FLEET_SELECTION_RESET": "Reset choice",
                "LOCA_API_FLEET_DATA": "This data can be entered into a compatible combat simulator:",
                "LOCA_ALL_BUTTON_FORWARD": "Continue",
                "LOCA_FLEET_NO_SELECTION": "Nothing has been selected",
                "LOCA_ALL_TACTICAL_RETREAT": "Tactical retreat",
                "LOCA_FLEET1_TACTICAL_RETREAT_CONSUMPTION_TOOLTIP": "Show Deuterium usage per tactical retreat",
                "LOCA_FLEET_FUEL_CONSUMPTION": "Deuterium consumption",
                "LOCA_FLEET_ERROR_OWN_VACATION": "No fleets can be sent from vacation mode!",
                "LOCA_FLEET_CURRENTLY_OCCUPIED": "The fleet is currently in combat.",
                "LOCA_FLEET_FREE_MARKET_SLOTS": "Offers",
                "LOCA_FLEET_TOOLTIPP_FREE_MARKET_SLOTS": "Used\/Total trading fleets",
                "LOCA_FLEET_HEADLINE_TWO": "Fleet Dispatch II",
                "LOCA_FLEET_TAKEOFF_PLACE": "Origin",
                "LOCA_FLEET_TARGET_PLACE": "Destination",
                "LOCA_ALL_PLANET": "Planet",
                "LOCA_ALL_MOON": "Moon",
                "LOCA_FLEET_COORDINATES": "Coordinates",
                "LOCA_FLEET_DISTANCE": "Distance",
                "LOCA_FLEET_DEBRIS": "debris field",
                "LOCA_FLEET_SHORTLINKS": "Shortcuts",
                "LOCA_FLEET_FIGHT_ASSOCIATION": "Combat forces",
                "LOCA_FLEET_BRIEFING": "Briefing",
                "LOCA_FLEET_DURATION_ONEWAY": "Duration of flight (one way)",
                "LOCA_FLEET_SPEED": "Speed:",
                "LOCA_FLEET_SPEED_MAX_SHORT": "max.",
                "LOCA_FLEET_ARRIVAL": "Arrival",
                "LOCA_FLEET_TIME_CLOCK": "Clock",
                "LOCA_FLEET_RETURN": "Return",
                "LOCA_FLEET_HOLD_FREE": "Empty cargobays",
                "LOCA_ALL_BUTTON_BACK": "Back",
                "LOCA_FLEET_PLANET_UNHABITATED": "Uninhabited planet",
                "LOCA_FLEET_NO_DEBIRS_FIELD": "No debris field",
                "LOCA_FLEET_PLAYER_UMODE": "Player in vacation mode",
                "LOCA_FLEET_ADMIN": "Admin or GM",
                "LOCA_ALL_NOOBSECURE": "Noob protection",
                "LOCA_GALAXY_ERROR_STRONG": "This planet can not be attacked as the player is to strong!",
                "LOCA_FLEET_NO_MOON": "No moon available.",
                "LOCA_FLEET_NO_RECYCLER": "No recycler available.",
                "LOCA_ALL_NO_EVENT": "There are currently no events running.",
                "LOCA_PLANETMOVE_ERROR_ALREADY_RESERVED": "This planet has already been reserved for a relocation.",
                "LOCA_FLEET_ERROR_TARGET_MSG": "Fleets can not be sent to this target.",
                "LOCA_FLEETSENDING_NOT_ENOUGH_FOIL": "Not enough deuterium!",
                "LOCA_FLEET_HEADLINE_THREE": "Fleet Dispatch III",
                "LOCA_FLEET_TARGET_FOR_MISSION": "Select mission for target",
                "LOCA_FLEET_MISSION": "Mission",
                "LOCA_FLEET_RESOURCE_LOAD": "Load resources",
                "LOCA_FLEET_SELECTION_NOT_AVAILABLE": "You cannot start this mission.",
                "LOCA_FLEET_RETREAT_AFTER_DEFENDER_RETREAT_TOOLTIP": "If this option is activated, your fleet will also withdraw without a fight if your opponent flees.",
                "LOCA_FLEET_RETREAT_AFTER_DEFENDER_RETREAT": "Return upon retreat by defenders",
                "LOCA_FLEET_TARGET": "Target",
                "LOCA_FLEET_DURATION_FEDERATION": "Flight Duration (fleet union)",
                "LOCA_ALL_TIME_HOUR": "h",
                "LOCA_FLEET_HOLD_TIME": "Hold time",
                "LOCA_FLEET_EXPEDITION_TIME": "Duration of expedition",
                "LOCA_ALL_METAL": "Metal",
                "LOCA_ALL_CRYSTAL": "Crystal",
                "LOCA_ALL_DEUTERIUM": "Deuterium",
                "LOCA_ALL_FOOD": "Food",
                "LOCA_FLEET_LOAD_ROOM": "cargo bay",
                "LOCA_FLEET_CARGO_SPACE": "Available space \/ Max. cargo space",
                "LOCA_FLEET_SEND": "Send fleet",
                "LOCA_ALL_NETWORK_ATTENTION": "Caution",
                "LOCA_PLANETMOVE_BREAKUP_WARNING": "Caution! This mission may still be running once the relocation period starts and if this is the case, the process will be cancelled. Do you really want to continue with this job?",
                "LOCA_ALL_YES": "yes",
                "LOCA_ALL_NO": "No",
                "LOCA_ALL_NOTICE": "Reference",
                "LOCA_FLEETSENDING_MAX_PLANET_WARNING": "Attention! No further planets may be colonised at the moment. Two levels of astrotechnology research are necessary for each new colony. Do you still want to send your fleet?",
                "LOCA_ALL_PLAYER": "Player",
                "LOCA_FLEET_RESOURCES_ALL_LOAD": "Load all resources",
                "LOCA_FLEET_RESOURCES_ALL": "all resources",
                "LOCA_NETWORK_USERNAME": "Player\u2019s Name",
                "LOCA_EVENTH_ENEMY_INFINITELY_SPACE": "Deep space",
                "LOCA_FLEETSENDING_NO_MISSION_SELECTED": "No mission selected!",
                "LOCA_EMPTY_SYSTEMS": "Empty Systems",
                "LOCA_INACTIVE_SYSTEMS": "Inactive Systems",
                "LOCA_NETWORK_ON": "On",
                "LOCA_NETWORK_OFF": "Off",
                "LOCA_LOOT_FOOD": "Plunder food",
                "LOCA_BASHING_SYSTEM_LIMIT_REACHED_ATTACK_MISSIONS_DISABLED": "Attack missions have been deactivated as a result of too many attacks on the target."
            };
            var locadyn = {
                "locaAllOutlawWarning": "You are about to attack a stronger player. If you do this, your attack defenses will be shut down for 7 days and all players will be able to attack you without punishment. Are you sure you want to continue?",
                "localBashWarning": "In this universe, 0 attacks are permitted within a 24-hour period. This attack would probably exceed this limit. Do you really wish to launch it?",
                "locaOfficerbonusTooltipp": "+ 2 Fleet slots because of Admiral"
            };
            var errorCodeMap = {
                "601": "An error has occurred",
                "602": "Error, there is no moon",
                "603": "Error, player can`t be approached because of newbie protection",
                "604": "Player is too strong to be attacked",
                "605": "Error, player is in vacation mode",
                "606": "No fleets can be sent from vacation mode!",
                "610": "Error, not enough ships available, send maximum number:",
                "611": "Error, no ships available",
                "612": "Error, no free fleet slots available",
                "613": "Error, you don`t have enough deuterium",
                "614": "Error, there is no planet there",
                "615": "Error, not enough cargo capacity",
                "616": "Multi-alarm",
                "617": "Admin or GM",
                "618": "Attack ban until 01.01.1970 01:00:00"
            };

            var fleetDispatcher = null;

            var emptySystems = 0;
            var inactiveSystems = 0;

            var lootFoodOnAttack = false;

            $(function () {
                fleetDispatcher = new FleetDispatcher(window);
                fleetDispatcher.init();
            });

            var apiDataJson = {
                "coords": "{{ $planet->getPlanetCoordinates()->asString() }}",
                "characterClassId": 2,
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
                    "lifeformProtection": 0,
                    "spaceDockExtender": 0,
                    "denCapacity": {"metal": 0, "crystal": 0, "deuterium": 0},
                    "characterClassBooster": {"1": 0, "2": 0, "3": 0}
                },
                "fleetspeed": 10
            }
            var apiCommonData = [["coords", "{{ $planet->getPlanetCoordinates()->asString() }}"], ["characterClassId", 2]];
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
                                                <img class="tech204" width="28" height="28" alt="Light Fighter"
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
                                                <img class="tech205" width="28" height="28" alt="Heavy Fighter"
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
                                                <img class="tech206" width="28" height="28" alt="Cruiser"
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
                                                <img class="tech207" width="28" height="28" alt="Battleship"
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
                                                <img class="tech215" width="28" height="28" alt="Battlecruiser"
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
                                                <img class="tech211" width="28" height="28" alt="Bomber"
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
                                                <img class="tech213" width="28" height="28" alt="Destroyer"
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
                                                <img class="tech214" width="28" height="28" alt="Deathstar"
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
                                                <img class="tech218" width="28" height="28" alt="Reaper"
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
                                                <img class="tech219" width="28" height="28" alt="Pathfinder"
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
                                                <img class="tech202" width="28" height="28" alt="Small Cargo"
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
                                                <img class="tech203" width="28" height="28" alt="Large Cargo"
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
                                                <img class="tech208" width="28" height="28" alt="Colony Ship"
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
                                                <img class="tech209" width="28" height="28" alt="Recycler"
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
                                                <img class="tech210" width="28" height="28" alt="Espionage Probe"
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
                        <h3>@lang('Fleet dispatch impossible')</h3>
                        <p>
                            <span class="icon icon_warning"></span>
                            @lang('There are no ships on this planet.')
                        </p>
                    </div>
                @else
                    <div class="fleetStatus" id="statusBarFleet">
                        <ul>
                            <li><span class="title">@lang('Mission:')</span> <span
                                        class="missionName">@lang('Nothing has been selected')</span></li>
                            <li><span class="title">@lang('Target:')</span> <span class="targetName">[{{ $planet->getPlanetCoordinates()->asString() }}] <figure
                                            class="planetIcon {{ $planet->isPlanet() ? 'planet' : 'moon' }} tooltip js_hideTipOnMobile"
                                            title="{{ $planet->isPlanet() ? 'Planet' : 'Moon' }}"></figure>{{ $planet->getPlanetName() }}</span></li>
                            <li><span class="title">@lang('Player\'s Name:')</span> <span
                                        class="targetPlayerName">{{ $player->getUsername() }}</span></li>
                        </ul>
                    </div>
                    <div id="buttonz">
                        <div class="content">
                            <form name="shipsChosen" id="shipsChosen" method="post"
                                  action="{{ route('overview.index') }}#TODO_page=fleet2">
                                <div id="technologies">
                                    <div id="battleships">
                                        <div class="header"><h2>@lang('Combat ships')</h2></div>
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
                                        <div class="header"><h2>@lang('Civil ships')</h2></div>
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
                                            @lang('Standard fleets')
                                        </a>
                                        <select class="combatunits dropdownInitialized" size="1" id="standardfleet"
                                                style="display: none;">
                                            <option>-</option>
                                            <option value="954">20 battleships</option>
                                        </select><span class="dropdown currentlySelected combatunits" rel="dropdown484"
                                                       style="width: 144px;"><a class="undefined" data-value="-"
                                                                                rel="dropdown484"
                                                                                href="javascript:void(0);">-</a></span>
                                    </div>
                                    <span class="show_fleet_apikey tooltipCustom tpd-hideOnClickOutside" title="">
                            </span>
                                    <a id="continueToFleet2" class="continue off" href="">
                                        <span class="ipiHintable" data-ipi-hint="ipiFleetContinueToPage2"
                                              data-ipi-highlight-step="ipiFleetContinueToPage2">@lang('Continue')</span>
                                    </a>
                                    <div class="clearfloat"></div>
                                    <p class="info">@lang('Nothing has been selected')</p>
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
                    <h2>@lang('Fleet Dispatch II') - {{ $planet->getPlanetName() }}</h2>
                    <a class="toggleHeader" data-name="fleet2">
                        <img alt="" src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" height="22" width="22">
                    </a>
                </div>
                <div class="c-left shortCorner"></div>
                <div class="c-right shortCorner"></div>
                <div class="fleetStatus" id="statusBarFleet">
                    <ul>
                        <li><span class="title">@lang('Mission:')</span> <span
                                    class="missionName">@lang('Nothing has been selected')</span></li>
                        <li><span class="title">@lang('Target:')</span> <span class="targetName">[{{ $planet->getPlanetCoordinates()->asString() }}] <figure
                                        class="planetIcon {{ $planet->isPlanet() ? 'planet' : 'moon' }} tooltip js_hideTipOnMobile" title="{{ $planet->isPlanet() ? 'Planet' : 'Moon' }}"></figure>{{ $planet->getPlanetName() }}</span>
                        </li>
                        <li><span class="title">@lang('Player\'s Name:')</span> <span
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
                                    <th><h2>@lang('Origin:')</h2></th>
                                    <th></th>
                                    <th><h2>@lang('Destination:')</h2></th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <td id="start" class="border5px">
                                        <div class="planetname">{{ $planet->getPlanetName() }}</div>
                                        <div class="target">
                                            <a class="planet_source{{ $planet->isPlanet() ? '_selected' : '' }}">
                                                <span class="textlabel">@lang('Planet')</span>
                                            </a>
                                            <a class="moon_source{{ $planet->isMoon() ? '_selected' : '' }}">
                                                <span class="textlabel">@lang('Moon')</span>
                                            </a>
                                            <br class="clearfloat">
                                        </div>
                                        <div class="coords">
                                            @lang('Coordinates:')
                                            <span style="color: #ffffff; font-weight: bold;">{{ $planet->getPlanetCoordinates()->asString() }}</span>
                                        </div>
                                    </td>
                                    <td id="distance">
                                        <div id="distanceValue">5</div>
                                        <div class="coords">@lang('Distance')</div>
                                    </td>
                                    <td id="target" class="border5px">
                                        <div class="planetname" id="targetPlanetName">{{ $planet->getPlanetName() }}</div>
                                        <div class="target">
                                            <a class="planet{{ $planet->isPlanet() ? '_selected' : '' }}" href="" id="pbutton">
                                                <span class="textlabel">@lang('Planet')</span>
                                            </a>
                                            <a class="moon{{ $planet->isMoon() ? '_selected' : '' }}" href="" id="mbutton">
                                                <span class="textlabel">@lang('Moon')</span>
                                            </a>
                                            <a class="debris" href="" id="dbutton">
                                                <span class="textlabel">@lang('debris field')</span>
                                            </a>
                                            <br class="clearfloat">
                                        </div>
                                        <div class="coords">
                                            @lang('Coordinates:')
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
                                            <span id="shortlinks tips">@lang('Shortcuts:')</span>
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
                                            <span id="combatunits tips">@lang('Combat forces:')</span>
                                            <div class="glow">
                                                <select size="1" class="combatunits dropdownInitialized" id="aksbox"
                                                        name="acsValues" style="display: none;">
                                                    <option value="-">-</option>
                                                </select><span class="dropdown currentlySelected combatunits"
                                                               rel="dropdown568" style="width: 144px;"><a
                                                            class="undefined" data-value="-" rel="dropdown568"
                                                            href="javascript:void(0);">-</a></span>
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
                            <h2>@lang('Select mission for target:')</h2>
                        </div>
                        <div class="content">
                            <div class="ajax_loading" style="display: none;">
                                <div class="ajax_loading_overlay">
                                    <div class="ajax_loading_indicator"></div>
                                </div>
                            </div>
                            <div id="attackMissionsDisabledBashingLimit" style="display: none;"><span
                                        class="icon icon_warning"></span> @lang('Attack missions have been deactivated as a result of too many attacks on the target.')
                            </div>
                            <ul id="missions">
                                <li id="button15" class="off ipiHintable" data-ipi-hint="ipiFleetMission15">
                                    <a id="missionButton15" href="" data-mission="15"
                                       data-ipi-highlight-step="ipiFleetMission15">
                                        <span class="textlabel">@lang('Expedition')</span>
                                    </a>
                                </li>
                                <li id="button7" class="off ipiHintable" data-ipi-hint="ipiFleetMission7">
                                    <a id="missionButton7" href="" data-mission="7"
                                       data-ipi-highlight-step="ipiFleetMission7">
                                        <span class="textlabel">@lang('Colonisation')</span>
                                    </a>
                                </li>
                                <li id="button8" class="off ipiHintable" data-ipi-hint="ipiFleetMission8">
                                    <a id="missionButton8" href="" data-mission="8"
                                       data-ipi-highlight-step="ipiFleetMission8">
                                        <span class="textlabel">@lang('Recycle Debris Field')</span>
                                    </a>
                                </li>
                                <li id="button3" class="off ipiHintable" data-ipi-hint="ipiFleetMission3">
                                    <a id="missionButton3" href="" data-mission="3"
                                       data-ipi-highlight-step="ipiFleetMission3">
                                        <span class="textlabel">@lang('Transport')</span>
                                    </a>
                                </li>
                                <li id="button4" class="off ipiHintable" data-ipi-hint="ipiFleetMission4">
                                    <a id="missionButton4" href="" data-mission="4"
                                       data-ipi-highlight-step="ipiFleetMission4">
                                        <span class="textlabel">@lang('Deployment')</span>
                                    </a>
                                </li>
                                <li id="button6" class="off ipiHintable" data-ipi-hint="ipiFleetMission6">
                                    <a id="missionButton6" href="" data-mission="6"
                                       data-ipi-highlight-step="ipiFleetMission6">
                                        <span class="textlabel">@lang('Espionage')</span>
                                    </a>
                                </li>
                                <li id="button5" class="off ipiHintable" data-ipi-hint="ipiFleetMission5">
                                    <a id="missionButton5" href="" data-mission="5"
                                       data-ipi-highlight-step="ipiFleetMission5">
                                        <span class="textlabel">@lang('ACS Defend')</span>
                                    </a>
                                </li>
                                <li id="button1" class="off ipiHintable" data-ipi-hint="ipiFleetMission1">
                                    <a id="missionButton1" href="" data-mission="1"
                                       data-ipi-highlight-step="ipiFleetMission1">
                                        <span class="textlabel">@lang('Attack')</span>
                                    </a>
                                </li>
                                <li id="button2" class="off ipiHintable" data-ipi-hint="ipiFleetMission2">
                                    <a id="missionButton2" href="" data-mission="2"
                                       data-ipi-highlight-step="ipiFleetMission2">
                                        <span class="textlabel">@lang('ACS Attack')</span>
                                    </a>
                                </li>
                                <li id="button9" class="off ipiHintable" data-ipi-hint="ipiFleetMission9">
                                    <a id="missionButton9" href="" data-mission="9"
                                       data-ipi-highlight-step="ipiFleetMission9">
                                        <span class="textlabel">@lang('Moon Destruction')</span>
                                    </a>
                                </li>
                            </ul>
                            <br class="clearfloat">
                            <div id="missionNameWrapper" class="off">
                                @lang('Mission:')
                                <span id="missionName" class="missionName">@lang('Nothing has been selected')</span>
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
                                    <div class="briefing_overlay">@lang('You cannot start this mission.')</div>
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
                                        <input name="prioFood" type="hidden" value="1">

                                    </div>
                                    <div class="missionHeader">@lang('Briefing:')</div>
                                    <div class="missionHeader">@lang('Load resources:')</div>
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
                                                @lang('Target:')
                                                <span class="value tooltip active tpd-hideOnClickOutside"
                                                      id="targetPlanet" title="">[{{ $planet->getPlanetCoordinates()->asString() }}] {{ $planet->getPlanetName() }}</span>
                                            </li>
                                            <li>
                                                @lang('Duration of flight (one way):')
                                                <span class="value" id="duration">0:00:00 h</span>
                                            </li>
                                            <li>
                                                @lang('Arrival:') <span class="value"><span
                                                            id="arrivalTime">18.03.24 23:16:15</span> @lang('Clock')</span>
                                            </li>
                                            <li>
                                                @lang('Return:') <span class="value"><span
                                                            id="returnTime">18.03.24 23:16:15</span> @lang('Clock')</span>
                                            </li>
                                            <li>
                                                @lang('Deuterium consumption:')
                                                <span class="value"><span id="consumption"><span class="undermark">0 (NaN%)</span></span></span>
                                            </li>
                                            <li>
                                                @lang('Empty cargobays:') <span class="value" id="storage"><span
                                                            class="undermark">0</span></span>
                                            </li>
                                            <li id="holdtimeline" style="display: none;">
                                                @lang('Hold time:')
                                                <select name="holdingtime" id="holdingtime"
                                                        style="display: none;">
                                                    <option value="0">0</option>
                                                    <option value="1" selected="'selected'">1</option>
                                                    <option value="2">2</option>
                                                    <option value="4">4</option>
                                                    <option value="8">8</option>
                                                    <option value="16">16</option>
                                                    <option value="32">32</option>
                                                </select><span class="dropdown currentlySelected undefined"
                                                               rel="dropdown284" style="width: 45px;"><a
                                                            class="undefined" data-value="1" rel="dropdown284"
                                                            href="javascript:void(0);">1</a></span>
                                            </li>
                                            <li id="expeditiontimeline">
                                                @lang('Duration of expedition:')
                                                <select name="expeditiontime" id="expeditiontime">
                                                    <option value="1" >1</option>
                                                    <option value="2" >2</option>
                                                    <option value="3" >3</option>
                                                    <option value="4" >4</option>
                                                    <option value="5" >5</option>
                                                    <option value="6" >6</option>
                                                </select>
                                                <span class="value">h</span>
                                            </li>
                                            <li>
                                                <input type="hidden" name="speed" id="speed" value="10">
                                                @lang('Speed:') (@lang('max.') <span id="maxspeed">1,000,000,000</span>)
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
                                            <div class="res_wrap border3px">
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
                                                <span>@lang('Plunder food:')</span>
                                                <toggle-switch>
                                                    <input type="checkbox" value="None" id="lootFoodInput"
                                                           name="lootFoodOnAttack">
                                                    <label for="lootFoodInput"></label>
                                                </toggle-switch>
                                                <script>

                                                </script>
                                            </div>
                                            <div id="loadAllResources">
                                                <div class="allResourcesWrap ipiHintable"
                                                     data-ipi-hint="ipiFleetCargoLoadAll">
                                                    <a id="allresources">
                                                        <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif"
                                                             width="32" height="32"
                                                             data-ipi-highlight-step="ipiFleetCargoLoadAll">
                                                    </a>
                                                </div>
                                                @lang('all resources')
                                            </div>
                                            <div id="loadRoom">
                                                @lang('cargo bay:')
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
                                             style="float:left;width:600px;margin-left:20px" value="10" steps="20"
                                             stepsize="5" minvalue="0.5" usehalfstep="true"></div>
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
                                        <span style="padding-top:9px;">@lang('Send fleet')</span>
                                    </a>
                                    <a id="backToFleet1" class="back" href="">
                                        <span style="font-size:12px; text-transform:uppercase;">@lang('Back')</span>
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
