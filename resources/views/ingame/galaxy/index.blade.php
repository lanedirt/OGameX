@extends('ingame.layouts.main')

@section('content')

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <div id="galaxycomponent" class="maincontent">
        <script type="text/javascript">
            var galaxy = {{ $current_galaxy }};
            var system = {{ $current_system }};
            var maxGalaxies = 6;
            var maxSystems = 499;
            var spionageAmount = 3;
            var contentLink = "#?page=ingame&component=galaxyContent&ajax=1";
            var galaxyContentLink = "#?page=ingame&component=galaxy&action=fetchGalaxyContent&ajax=1&asJson=1";
            var preserveSystemOnPlanetChange = false;
            var galaxyLoca = {"reservationSuccess":"The position has been reserved for you. Your colony`s relocation has begun.","questionTitle":"Resettle Planet","question":"Are you sure you want to relocate your planet to these coordinates? To finance the relocation you`ll need 240,000 Dark Matter.","deuteriumNeeded":"You don`t have enough Deuterium! You need 10 Units of Deuterium.","fleetAttacking":"Fleet is attacking!","fleetUnderway":"Fleet is en-route","discoverySend":"Dispatch exploration ship\n","discoverySuccess":"Exploration ship dispatched\n","discoveryUnavailable":"You can\u2019t dispatch an exploration ship to this location.\n","discoveryUnderway":"An Exploration Ship is already on approach to this planet.\n","discoveryLocked":"You haven\u2019t unlocked the research to discover new lifeforms yet.\n","discoverQuestionTitle":"Exploration Ship\n","discoverQuestionText":"Do you want to dispatch an exploration ship to this planet?\n<br>Metal: 5000 Crystal: 1000 Deuterium: 500"};
            var loca = {"LOCA_FLEET_EXPEDITION_TYPE":"Expedition","LOCA_FLEET_DEBRIS":"debris field","LOCA_UNAVAILABLE_PHALANXSYSTEM":"The system phalanx can only be used by the alliance class Researcher!","LOCA_PHALANX_SYSTEM_BUTTON":"System Phalanx","LOCA_SPY_SYSTEM_BUTTON":"System Espionage","locaErrorNoRequiredResearch":"You have to research Astrophysics first.","LOCA_GALAXY_ERROR_INACCESSIBLE_DUE_TO_VACATION":"You cannot use the galaxy view whilst in vacation mode!","LOCA_FLEET_PLAYER_UMODE":"Player in vacation mode","LOCA_LEFTMENU_GALAXY":"Galaxy","LOCA_GALAXY_HEADLINE_SUNSYSTEM":"System","LOCA_GALAXY_LETS_GO":"Go!","LOCA_ALL_PLANET":"Planet","LOCA_ALL_NAME":"Name","LOCA_ALL_MOON":"Moon","LOCA_ALL_DEBRIS_FIELD_SHORT":"DF","LOCA_GALAXY_PLAYER_STATUS":"Player (Status)","LOCA_NETWORK_ALLY":"Alliance","LOCA_NETWORK_ACTION":"Action","LOCA_GALAXY_PLANETS_SETTLED":"Planets colonized","LOCA_TECH_ESPIONAGEPROBE_SNAME":"Esp.Probe","LOCA_TECH_RECYCLER_SNAME":"Recy.","LOCA_TECH_INTERPLANETARYMISSILE_SNAME":"IPM.","LOCA_GALAXY_SLOTS_FULL":"Used slots","LOCA_GALAXY_LEGEND":"Legend","LOCA_GALAXY_PLAYER_STATUS_A":"A","LOCA_GALAXY_LEGEND_ADMIN":"Administrator","LOCA_GALAXY_PLAYER_STATUS_S":"s","LOCA_GALAXY_LEGEND_STRONG_PLAYER":"stronger player","LOCA_GALAXY_PLAYER_STATUS_N":"n","LOCA_GALAXY_LEGEND_NOOB":"weaker player (newbie)","LOCA_GALAXY_PLAYER_STATUS_OUTLAW":"o","LOCA_GALAXY_LEGEND_OUTLAW":"Outlaw (temporary)","LOCA_GALAXY_PLAYER_STATUS_U":"v","LOCA_STATION_JUMP_VACATION":"Vacation Mode","LOCA_GALAXY_PLAYER_STATUS_G":"b","LOCA_GALAXY_LEGEND_BANNED":"banned","LOCA_GALAXY_PLAYER_STATUS_I":"i","LOCA_GALAXY_LEGEND_SEVEN_DAYS_INACTIVE":"7 days inactive","LOCA_GALAXY_PLAYER_STATUS_I_LONG":"I","LOCA_GALAXY_LEGEND_TWENTYEIGHT_DAYS_INACTIVE":"28 days inactive","LOCA_GALAXY_PLAYER_STATUS_EP":"hp","LOCA_GALAXY_LEGEND_HONORABLE_TARGET":"Honorable target","LOCA_ALL_ACTIVITY":"Activity","LOCA_FLEET_NO_ACTION_AVAILABLE":"No actions available.","LOCA_ALL_TIME_MINUTE":"m","LOCA_GALAXY_MOON_DIAMETER_KM":"Diameter of moon in km","LOCA_OVERVIEW_JS_KM":"km","LOCA_ALL_METAL":"Metal","LOCA_ALL_CRYSTAL":"Crystal","LOCA_ALL_DEUTERIUM":"Deuterium","LOCA_GALAXY_PATHFINDER_NEEDED":"Pathfinders needed","LOCA_GALAXY_RECYCLER_NEEDED":"Recyclers needed","LOCA_GALAXY_DEBRIS_REDUCE":"Mine","LOCA_PHALANX_ERROR_NOT_ENOUTH_DEUT":"Not enough deuterium to deploy phalanx.","LOCA_GALAXY_USE_PHALANX":"Use phalanx","LOCA_GALAXY_ERROR_COLONIZATION":"It is not possible to colonize a planet without a colony ship.","LOCA_ALL_PLAYER":"Player","LOCA_GALAXY_RANKING":"Ranking","LOCA_MESSAGES_ESPIONAGEREPORT":"Espionage report","LOCA_FLEET_MISSILEATTACK":"Missile Attack","LOCA_GALAXY_RANK":"Rank","LOCA_NETWORK_USERS":"Member","LOCA_ALLIANCE_CLASS":"Alliance Class","LOCA_FLEET_NO_FREE_SLOTS":"No fleet slots available","LOCA_ALL_AJAXLOAD":"load...","LOCA_EVENTH_ENEMY_INFINITELY_SPACE":"Deep space","LOCA_FLEET_NO_ESPIONAGE":"Espionage not possible","LOCA_FLEET_ESPIONAGE":"Espionage","LOCA_HEADER_GETADMIRAL":"Hire admiral","LOCA_ALL_DARKMATTER":"Dark Matter","LOCA_OUTLAW_EXPLANATION":"If you are an outlaw, you no longer have any attack protection and can be attacked by all players.","LOCA_GALAXY_LEGEND_HONORABLE_TARGET_EXPLANATION":"In battle against this target you can receive honour points and plunder 50% more loot.","LOCA_GALAXY_SYSTEM_DISCOVERY":"Discoveries","LOCA_GALAXY_SYSTEM_DISCOVERY_TOOLTIP":"Launch a discovery mission to all possible locations","LOCA_EXPEDITION_FLEET_TEMPLATE":"Expedition Fleet","LOCA_FLEET_SEND":"Send fleet","LOCA_ALL_SEND":"send","LOCA_FLEET_TEMPLATE_ADMIRAL_NEEDED":"You need an Admiral to use this feature."};
            var shipsendingDone = 1;
            var premiumLink = "#?page=premium&openDetail=3";
            var sendDiscoverSystemUrl = "";
            var missleAttackLink = "#?page=ajax&component=missileattacklayer&width=669&height=250";
            var canSwitchGalaxy = true;
            var notEnoughDeuteriumMessage = "You don`t have enough Deuterium! You need 10 Units of Deuterium.";
            var toGalaxyLink = "#?page=ingame&component=galaxy&galaxy=2&system=3";
            var mobile = false;
            var inProgress = false;
            var expeditionFleetTemplates = [];

            function initGalaxy()
            {
                $("input#galaxy_input")
                    .keypress(function(event) {
                        if (event.key === 'Enter') {
                            event.preventDefault();
                            trySubmit();
                        }
                    })
                    .keyup(function() {
                        checkIntInput($(this), 1, 6)
                    })
                    .focus(function() {
                        $(this).val("");
                    });
                $("input#system_input")
                    .keypress(function(event) {
                        if (event.key === 'Enter') {
                            event.preventDefault();
                            trySubmit();
                        }
                    })
                    .keyup(function() {
                        checkIntInput($(this), 1, 6)
                    })
                    .focus(function() {
                        $(this).val("");
                    });
                initSpySystem();
                tabletInitGalaxy();
                loadContent(galaxy, system, false);


                focusOnTabChange("#showbutton a", true);
            }

            function initGalaxyNew()
            {
                $("input#galaxy_input")
                    .keypress(function(event) {
                        if (event.key === 'Enter') {
                            event.preventDefault();
                            trySubmit();
                        }
                    })
                    .keyup(function() {
                        checkIntInput($(this), 1, 6)
                    })
                    .focus(function() {
                        $(this).val("");
                    });
                $("input#system_input")
                    .keypress(function(event) {
                        if (event.key === 'Enter') {
                            event.preventDefault();
                            trySubmit();
                        }
                    })
                    .keyup(function() {
                        checkIntInput($(this), 1, 499)
                    })
                    .focus(function() {
                        $(this).val("");
                    });
                initSpySystem();
                tabletInitGalaxy();
                initExpeditionFleetTemplates();
                loadContentNew(galaxy, system);

            }

            function trySubmit()
            {
                galaxy = $("#galaxy_input").val();
                system = $("#system_input").val();
                if(0 === galaxy.length || $.isNumeric(+galaxy) === false) {
                    galaxy = 1;
                }
                if(0 === system.length || $.isNumeric(+system) === false) {
                    system = 1;
                }
                loadContentNew(galaxy, system);
            }

            fadingDivs = new Array();

            function doExpedition()
            {
                let expeditionLink = "#?page=ingame&component=fleetdispatch&mission=15&position=16&type=1";

                location.href = expeditionLink + "&galaxy=" + galaxy + "&system=" + system + "&type=1";
            }

        </script>

        <div id="inhalt">
            <div id="expeditionFleetOverlay" style="display:none;">
                <div id="expeditionfleettemplatecomponent">

                </div>
            </div>
            <div id="galaxyHeader" class="ct_head_row">
                <form action="#ingame&amp;component=galaxy&amp;action=fetchGalaxyContent&amp;ajax=1&amp;asJson=1" name="galaform" method="post">
                    <span class="galaxy_icons galaxy tooltip tpd-hideOnClickOutside" title=""></span>
                    <span class="galaxy_icons prev" onclick="submitOnKey('ArrowDown');"></span>
                    <input id="galaxy_input" class="hideNumberSpin" maxlength="3" type="number" pattern="[0-9]*" value="2" name="galaxy" tabindex="2">
                    <span class="galaxy_icons next" onclick="submitOnKey('ArrowUp');"></span>
                    <span class="galaxy_icons solarsystem tooltip" title="System"></span>
                    <span class="galaxy_icons prev ipiHintable" onclick="submitOnKey('ArrowLeft');" data-ipi-hint="ipiGalaxySwitchGalaxy"></span>
                    <input id="system_input" class="hideNumberSpin" maxlength="3" type="number" pattern="[0-9]*" value="3" tabindex="2" name="system">
                    <span class="galaxy_icons next ipiHintable" onclick="submitOnKey('ArrowRight');" data-ipi-hint="ipiGalaxySwitchGalaxy"></span>
                    <div class="btn_blue" onclick="submitForm();">Go!</div>
                    <div class="systembuttons">
                        <a class="btn_blue tooltip phalanxlink btn_system_action" href="javascript:void(0);" title="System Phalanx" disabled="disabled">
                            <img alt="" src="/img/icons/1cae570e41fc188133be9d548d6523.gif" class="icon_allianceBonus" style="filter:grayscale(1);"> System Phalanx
                        </a>
                        <a class="btn_blue tooltip spysystemlink btn_system_action" disabled="disabled" title="System Espionage">
                            <img alt="" src="/img/icons/1cae570e41fc188133be9d548d6523.gif" class="icon_allianceBonus" style="filter:grayscale(1);"> System Espionage
                        </a>

                        <div id="discoverSystemBtn" class="btn_blue tooltip discoverSystemLink btn_system_action" title="Launch a discovery mission to all possible locations" disabled="disabled">
                            <div class="disabled"></div>&nbsp;Discoveries
                        </div>
                    </div>
                </form>
            </div>

            <div id="galaxyLoading" style="display: none;">
                <img src="/img/icons/6e0f46d7504242302bc8055ad9c8c2.gif" alt="">
            </div>
            <div id="galaxyContent">
                {!! $galaxy_table_html !!}
            </div>
            <div id="legendTT" style="display: none;" class="htmlTooltip">
                <h1>Legend</h1>
                <div class="splitLine"></div>
                <dl>
                    <dt class="abbreviation status_abbr_admin">A</dt>
                    <dd class="description">Administrator</dd>

                    <dt class="abbreviation status_abbr_strong">s</dt>
                    <dd class="description">stronger player</dd>

                    <dt class="abbreviation status_abbr_noob">n</dt>
                    <dd class="description">weaker player (newbie)</dd>

                    <dt class="abbreviation status_abbr_outlaw">o</dt>
                    <dd class="description">Outlaw (temporary)</dd>

                    <dt class="abbreviation status_abbr_vacation">v</dt>
                    <dd class="description">Vacation Mode</dd>

                    <dt class="abbreviation status_abbr_banned">b</dt>
                    <dd class="description">banned</dd>

                    <dt class="abbreviation status_abbr_inactive">i</dt>
                    <dd class="description">7 days inactive</dd>

                    <dt class="abbreviation status_abbr_longinactive">I</dt>
                    <dd class="description">28 days inactive</dd>

                    <dt class="abbreviation status_abbr_honorableTarget">hp</dt>
                    <dd class="description">Honorable target</dd>
                </dl>
            </div>
        </div>
        <br class="clearfix">
        <script type="text/javascript">
            var checkTargetUrl = "#?page=componentOnly&component=fleetdispatch&action=checkTarget&asJson=1"
            var missionExpedition = 15
            var spaceObjectTypePlanet = 1
            var expeditionPosition = 16

            var buildListCountdowns = new Array();
            (function($){
                initGalaxyNew();
                $(document.documentElement).off( "keyup" );
                $(document.documentElement).on( "keyup", keyevent );
            })(jQuery)
        </script>
    </div>
@endsection
