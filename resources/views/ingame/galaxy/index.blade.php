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
            var maxGalaxies = {{ $max_galaxies }};
            var maxSystems = 499;
            var spionageAmount = 3;
            var contentLink = "{{ route('galaxy.ajax') }}";
            var galaxyContentLink = "{{ route('galaxy.ajax') }}";
            var preserveSystemOnPlanetChange = false;
            var galaxyLoca = {"reservationSuccess":"The position has been reserved for you. Your colony`s relocation has begun.","questionTitle":"Resettle Planet","question":"Are you sure you want to relocate your planet to these coordinates? To finance the relocation you`ll need 240,000 Dark Matter.","deuteriumNeeded":"You don`t have enough Deuterium! You need 10 Units of Deuterium.","fleetAttacking":"Fleet is attacking!","fleetUnderway":"Fleet is en-route","discoverySend":"Dispatch exploration ship\n","discoverySuccess":"Exploration ship dispatched\n","discoveryUnavailable":"You can\u2019t dispatch an exploration ship to this location.\n","discoveryUnderway":"An Exploration Ship is already on approach to this planet.\n","discoveryLocked":"You haven\u2019t unlocked the research to discover new lifeforms yet.\n","discoverQuestionTitle":"Exploration Ship\n","discoverQuestionText":"Do you want to dispatch an exploration ship to this planet?\n<br>Metal: 5000 Crystal: 1000 Deuterium: 500"};
            var loca = {"LOCA_FLEET_EXPEDITION_TYPE":"Expedition","LOCA_FLEET_DEBRIS":"debris field","LOCA_UNAVAILABLE_PHALANXSYSTEM":"The system phalanx can only be used by the alliance class Researcher!","LOCA_PHALANX_SYSTEM_BUTTON":"System Phalanx","LOCA_SPY_SYSTEM_BUTTON":"System Espionage","locaErrorNoRequiredResearch":"You have to research Astrophysics first.","LOCA_GALAXY_ERROR_INACCESSIBLE_DUE_TO_VACATION":"You cannot use the galaxy view whilst in vacation mode!","LOCA_FLEET_PLAYER_UMODE":"Player in vacation mode","LOCA_LEFTMENU_GALAXY":"Galaxy","LOCA_GALAXY_HEADLINE_SUNSYSTEM":"System","LOCA_GALAXY_LETS_GO":"Go!","LOCA_ALL_PLANET":"Planet","LOCA_ALL_NAME":"Name","LOCA_ALL_MOON":"Moon","LOCA_ALL_DEBRIS_FIELD_SHORT":"DF","LOCA_GALAXY_PLAYER_STATUS":"Player (Status)","LOCA_NETWORK_ALLY":"Alliance","LOCA_NETWORK_ACTION":"Action","LOCA_GALAXY_PLANETS_SETTLED":"Planets colonized","LOCA_TECH_ESPIONAGEPROBE_SNAME":"Esp.Probe","LOCA_TECH_RECYCLER_SNAME":"Recy.","LOCA_TECH_INTERPLANETARYMISSILE_SNAME":"IPM.","LOCA_GALAXY_SLOTS_FULL":"Used slots","LOCA_GALAXY_LEGEND":"Legend","LOCA_GALAXY_PLAYER_STATUS_A":"A","LOCA_GALAXY_LEGEND_ADMIN":"Administrator","LOCA_GALAXY_PLAYER_STATUS_S":"s","LOCA_GALAXY_LEGEND_STRONG_PLAYER":"stronger player","LOCA_GALAXY_PLAYER_STATUS_N":"n","LOCA_GALAXY_LEGEND_NOOB":"weaker player (newbie)","LOCA_GALAXY_PLAYER_STATUS_OUTLAW":"o","LOCA_GALAXY_LEGEND_OUTLAW":"Outlaw (temporary)","LOCA_GALAXY_PLAYER_STATUS_U":"v","LOCA_STATION_JUMP_VACATION":"Vacation Mode","LOCA_GALAXY_PLAYER_STATUS_G":"b","LOCA_GALAXY_LEGEND_BANNED":"banned","LOCA_GALAXY_PLAYER_STATUS_I":"i","LOCA_GALAXY_LEGEND_SEVEN_DAYS_INACTIVE":"7 days inactive","LOCA_GALAXY_PLAYER_STATUS_I_LONG":"I","LOCA_GALAXY_LEGEND_TWENTYEIGHT_DAYS_INACTIVE":"28 days inactive","LOCA_GALAXY_PLAYER_STATUS_EP":"hp","LOCA_GALAXY_LEGEND_HONORABLE_TARGET":"Honorable target","LOCA_ALL_ACTIVITY":"Activity","LOCA_FLEET_NO_ACTION_AVAILABLE":"No actions available.","LOCA_ALL_TIME_MINUTE":"m","LOCA_GALAXY_MOON_DIAMETER_KM":"Diameter of moon in km","LOCA_OVERVIEW_JS_KM":"km","LOCA_ALL_METAL":"Metal","LOCA_ALL_CRYSTAL":"Crystal","LOCA_ALL_DEUTERIUM":"Deuterium","LOCA_GALAXY_PATHFINDER_NEEDED":"Pathfinders needed","LOCA_GALAXY_RECYCLER_NEEDED":"Recyclers needed","LOCA_GALAXY_DEBRIS_REDUCE":"Mine","LOCA_PHALANX_ERROR_NOT_ENOUTH_DEUT":"Not enough deuterium to deploy phalanx.","LOCA_GALAXY_USE_PHALANX":"Use phalanx","LOCA_GALAXY_ERROR_COLONIZATION":"It is not possible to colonize a planet without a colony ship.","LOCA_ALL_PLAYER":"Player","LOCA_GALAXY_RANKING":"Ranking","LOCA_MESSAGES_ESPIONAGEREPORT":"Espionage report","LOCA_FLEET_MISSILEATTACK":"Missile Attack","LOCA_GALAXY_RANK":"Rank","LOCA_NETWORK_USERS":"Member","LOCA_ALLIANCE_CLASS":"Alliance Class","LOCA_FLEET_NO_FREE_SLOTS":"No fleet slots available","LOCA_ALL_AJAXLOAD":"load...","LOCA_EVENTH_ENEMY_INFINITELY_SPACE":"Deep space","LOCA_FLEET_NO_ESPIONAGE":"Espionage not possible","LOCA_FLEET_ESPIONAGE":"Espionage","LOCA_HEADER_GETADMIRAL":"Hire admiral","LOCA_ALL_DARKMATTER":"Dark Matter","LOCA_OUTLAW_EXPLANATION":"If you are an outlaw, you no longer have any attack protection and can be attacked by all players.","LOCA_GALAXY_LEGEND_HONORABLE_TARGET_EXPLANATION":"In battle against this target you can receive honour points and plunder 50% more loot.","LOCA_GALAXY_SYSTEM_DISCOVERY":"Discoveries","LOCA_GALAXY_SYSTEM_DISCOVERY_TOOLTIP":"Launch a discovery mission to all possible locations","LOCA_EXPEDITION_FLEET_TEMPLATE":"Expedition Fleet","LOCA_FLEET_SEND":"Send fleet","LOCA_ALL_SEND":"send","LOCA_FLEET_TEMPLATE_ADMIRAL_NEEDED":"You need an Admiral to use this feature."};
            var shipsendingDone = 1;
            var premiumLink = "#?page=premium&openDetail=3";
            var sendDiscoverSystemUrl = "";
            var missleAttackLink = "{{ route('galaxy.missile-attack') }}";
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
                let expeditionLink = "{{ route('fleet.index') }}";

                location.href = expeditionLink + "?mission=15&galaxy=" + galaxy + "&system=" + system + "&position=16&type=1";
            }

        </script>

        <div id="inhalt">
            <div id="expeditionFleetOverlay" style="display:none;">
                <div id="expeditionfleettemplatecomponent">

                </div>
            </div>
            <div id="galaxyHeader" class="ct_head_row">
                <form action="{{ route('galaxy.ajax') }}" name="galaform" method="post">
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
                <div class="galaxyTable">
                    <div class="galaxyRow ctGalaxyHead">
                        <div class="galaxyCell span11">
                            <div id="probes">
                                Esp.Probe:<span id="probeValue">0</span>
                            </div>
                            <div id="recycler">
                                Recy.:<span id="recyclerValue">0</span>
                            </div>
                            <div id="rockets">
                                IPM.:<span id="missileValue">0</span>
                            </div>
                            <div id="slots">
                                Used slots:<span id="slotUsed">0</span>/<span id="slotValue">0</span>
                            </div>
                            <div id="galaxyHeaderDiscoveryCount">
                                Discoveries: 0/3950
                            </div>
                        </div>

                        <div id="filterCell" class="filterCell">
                            <div id="filter_empty" class="filter" onclick="filterToggle(event);">E</div>
                            <div id="filter_inactive" class="filter" onclick="filterToggle(event);">I</div>
                            <div id="filter_newbie" class="filter" onclick="filterToggle(event);">N</div>
                            <div id="filter_strong" class="filter" onclick="filterToggle(event);">S</div>
                            <div id="filter_vacation" class="filter" onclick="filterToggle(event);">V</div>
                        </div>
                    </div>
                    <div class="galaxyRow ctGalaxyHead headBold">
                        <div class="galaxyCell span1-2">Planet</div>
                        <div class="galaxyCell cellPlanetName">Name</div>
                        <div class="galaxyCell cellMoon">Moon</div>
                        <div class="galaxyCell cellDebris">DF</div>
                        <div class="galaxyCell cellPlayerName">Player (Status)</div>
                        <div class="galaxyCell cellAlliance">Alliance</div>
                        <div class="galaxyCell cellAction">Action</div>
                    </div>
                    <div class="galaxyRow ctContentRow" id="galaxyRow1">
                        <div class="galaxyCell cellPosition">1</div>
                        <div class="galaxyCell cellPlanet"></div>
                        <div class="galaxyCell cellPlanetName"></div>
                        <div class="galaxyCell cellMoon"></div>
                        <div class="galaxyCell cellDebris"></div>
                        <div class="galaxyCell cellPlayerName"></div>
                        <div class="galaxyCell cellAlliance"></div>
                        <div class="galaxyCell cellAction"></div>
                    </div>
                    <div class="galaxyRow ctContentRow" id="galaxyRow2">
                        <div class="galaxyCell cellPosition">2</div>
                        <div class="galaxyCell cellPlanet"></div>
                        <div class="galaxyCell cellPlanetName"></div>
                        <div class="galaxyCell cellMoon"></div>
                        <div class="galaxyCell cellDebris"></div>
                        <div class="galaxyCell cellPlayerName"></div>
                        <div class="galaxyCell cellAlliance"></div>
                        <div class="galaxyCell cellAction"></div>
                    </div>
                    <div class="galaxyRow ctContentRow" id="galaxyRow3">
                        <div class="galaxyCell cellPosition">3</div>
                        <div class="galaxyCell cellPlanet"></div>
                        <div class="galaxyCell cellPlanetName"></div>
                        <div class="galaxyCell cellMoon"></div>
                        <div class="galaxyCell cellDebris"></div>
                        <div class="galaxyCell cellPlayerName"></div>
                        <div class="galaxyCell cellAlliance"></div>
                        <div class="galaxyCell cellAction"></div>
                    </div>
                    <div class="galaxyRow ctContentRow" id="galaxyRow4">
                        <div class="galaxyCell cellPosition">4</div>
                        <div class="galaxyCell cellPlanet"></div>
                        <div class="galaxyCell cellPlanetName"></div>
                        <div class="galaxyCell cellMoon"></div>
                        <div class="galaxyCell cellDebris"></div>
                        <div class="galaxyCell cellPlayerName"></div>
                        <div class="galaxyCell cellAlliance"></div>
                        <div class="galaxyCell cellAction"></div>
                    </div>
                    <div class="galaxyRow ctContentRow" id="galaxyRow5">
                        <div class="galaxyCell cellPosition">5</div>
                        <div class="galaxyCell cellPlanet"></div>
                        <div class="galaxyCell cellPlanetName"></div>
                        <div class="galaxyCell cellMoon"></div>
                        <div class="galaxyCell cellDebris"></div>
                        <div class="galaxyCell cellPlayerName"></div>
                        <div class="galaxyCell cellAlliance"></div>
                        <div class="galaxyCell cellAction"></div>
                    </div>
                    <div class="galaxyRow ctContentRow" id="galaxyRow6">
                        <div class="galaxyCell cellPosition">6</div>
                        <div class="galaxyCell cellPlanet"></div>
                        <div class="galaxyCell cellPlanetName"></div>
                        <div class="galaxyCell cellMoon"></div>
                        <div class="galaxyCell cellDebris"></div>
                        <div class="galaxyCell cellPlayerName"></div>
                        <div class="galaxyCell cellAlliance"></div>
                        <div class="galaxyCell cellAction"></div>
                    </div>
                    <div class="galaxyRow ctContentRow" id="galaxyRow7">
                        <div class="galaxyCell cellPosition">7</div>
                        <div class="galaxyCell cellPlanet"></div>
                        <div class="galaxyCell cellPlanetName"></div>
                        <div class="galaxyCell cellMoon"></div>
                        <div class="galaxyCell cellDebris"></div>
                        <div class="galaxyCell cellPlayerName"></div>
                        <div class="galaxyCell cellAlliance"></div>
                        <div class="galaxyCell cellAction"></div>
                    </div>
                    <div class="galaxyRow ctContentRow" id="galaxyRow8">
                        <div class="galaxyCell cellPosition">8</div>
                        <div class="galaxyCell cellPlanet"></div>
                        <div class="galaxyCell cellPlanetName"></div>
                        <div class="galaxyCell cellMoon"></div>
                        <div class="galaxyCell cellDebris"></div>
                        <div class="galaxyCell cellPlayerName"></div>
                        <div class="galaxyCell cellAlliance"></div>
                        <div class="galaxyCell cellAction"></div>
                    </div>
                    <div class="galaxyRow ctContentRow" id="galaxyRow9">
                        <div class="galaxyCell cellPosition">9</div>
                        <div class="galaxyCell cellPlanet"></div>
                        <div class="galaxyCell cellPlanetName"></div>
                        <div class="galaxyCell cellMoon"></div>
                        <div class="galaxyCell cellDebris"></div>
                        <div class="galaxyCell cellPlayerName"></div>
                        <div class="galaxyCell cellAlliance"></div>
                        <div class="galaxyCell cellAction"></div>
                    </div>
                    <div class="galaxyRow ctContentRow" id="galaxyRow10">
                        <div class="galaxyCell cellPosition">10</div>
                        <div class="galaxyCell cellPlanet"></div>
                        <div class="galaxyCell cellPlanetName"></div>
                        <div class="galaxyCell cellMoon"></div>
                        <div class="galaxyCell cellDebris"></div>
                        <div class="galaxyCell cellPlayerName"></div>
                        <div class="galaxyCell cellAlliance"></div>
                        <div class="galaxyCell cellAction"></div>
                    </div>
                    <div class="galaxyRow ctContentRow" id="galaxyRow11">
                        <div class="galaxyCell cellPosition">11</div>
                        <div class="galaxyCell cellPlanet"></div>
                        <div class="galaxyCell cellPlanetName"></div>
                        <div class="galaxyCell cellMoon"></div>
                        <div class="galaxyCell cellDebris"></div>
                        <div class="galaxyCell cellPlayerName"></div>
                        <div class="galaxyCell cellAlliance"></div>
                        <div class="galaxyCell cellAction"></div>
                    </div>
                    <div class="galaxyRow ctContentRow" id="galaxyRow12">
                        <div class="galaxyCell cellPosition">12</div>
                        <div class="galaxyCell cellPlanet"></div>
                        <div class="galaxyCell cellPlanetName"></div>
                        <div class="galaxyCell cellMoon"></div>
                        <div class="galaxyCell cellDebris"></div>
                        <div class="galaxyCell cellPlayerName"></div>
                        <div class="galaxyCell cellAlliance"></div>
                        <div class="galaxyCell cellAction"></div>
                    </div>
                    <div class="galaxyRow ctContentRow" id="galaxyRow13">
                        <div class="galaxyCell cellPosition">13</div>
                        <div class="galaxyCell cellPlanet"></div>
                        <div class="galaxyCell cellPlanetName"></div>
                        <div class="galaxyCell cellMoon"></div>
                        <div class="galaxyCell cellDebris"></div>
                        <div class="galaxyCell cellPlayerName"></div>
                        <div class="galaxyCell cellAlliance"></div>
                        <div class="galaxyCell cellAction"></div>
                    </div>
                    <div class="galaxyRow ctContentRow" id="galaxyRow14">
                        <div class="galaxyCell cellPosition">14</div>
                        <div class="galaxyCell cellPlanet"></div>
                        <div class="galaxyCell cellPlanetName"></div>
                        <div class="galaxyCell cellMoon"></div>
                        <div class="galaxyCell cellDebris"></div>
                        <div class="galaxyCell cellPlayerName"></div>
                        <div class="galaxyCell cellAlliance"></div>
                        <div class="galaxyCell cellAction"></div>
                    </div>
                    <div class="galaxyRow ctContentRow" id="galaxyRow15">
                        <div class="galaxyCell cellPosition">15</div>
                        <div class="galaxyCell cellPlanet"></div>
                        <div class="galaxyCell cellPlanetName"></div>
                        <div class="galaxyCell cellMoon"></div>
                        <div class="galaxyCell cellDebris"></div>
                        <div class="galaxyCell cellPlayerName"></div>
                        <div class="galaxyCell cellAlliance"></div>
                        <div class="galaxyCell cellAction"></div>
                    </div>
                    <div class="expeditionDebrisSlotBoxRow">
                        <div class="expeditionDebrisSlotBoxCell cellPosition">16</div>
                        <div class="expeditionDebrisSlotBox" id="galaxyRow16">
                            <div>
                                <h3 class="title float_left">Deep space:</h3>
                            </div>
                            <div id="expeditionDebrisSlotDebrisContainer">

                            </div>
                            <div id="expeditionDebrisSlotActions">
                                <div id="galaxyExpeditionFleetTemplateContainer"
                                     class="tooltip hideTooltipOnMouseenter disabled"
                                     title="You need an Admiral to use this feature."
                                >
                                    <a id="expeditionFleetTemplateBtn"
                                       class="dark_highlight_tablet"
                                    >
                                        <span class="icon icon_combatunits"></span>
                                        <span class="expedtionFleetTemplateBtnTitle">Expedition Fleet</span>
                                    </a>
                                    <select class="expeditionFleetTemplateSelect" size="1" title="test"
                                            id="expeditionFleetTemplateSelect" disabled>
                                        <option value="0">-</option>
                                    </select>
                                </div>

                                <div id="expeditionbutton" class="btn_blue float_right btn_system_action" onClick="doExpedition();">
                                    Expedition
                                </div>
                                <div id="sendExpeditionFleetTemplateFleet" class="btn_blue float_right btn_system_action" style="display: none" onClick="sendExpedtionFleetFromTemplate()" disabled>
                                    send
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="galaxyRow ctGalaxyFleetInfo" id="fleetstatusrow"></div>
                    <div class="galaxyRow ctGalaxyFooter">
                        <div id="colonized"><span id="amountColonized">0</span>&nbsp;Planets colonized</div>
                        <div id="legend" class="filterCell">
                            <a href="javascript: void(0);"
                               class="tooltipRel tooltipClose"
                               rel="legendTT"
                               data-tippy-placement="top"
                            >
                                <span class="icon icon_info"></span>
                            </a>
                        </div>
                    </div>
                </div>
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

                // Remove overlay class from phalanx links to prevent redirect interception
                // The existing JS might add 'overlay' class which triggers unwanted behavior
                setInterval(function() {
                    $('a.phalanxlink.overlay').removeClass('overlay');
                }, 100);

                // Handle phalanx link clicks - using capture phase to run before overlay handlers
                document.addEventListener('click', function(e) {
                    var target = e.target;
                    // Find the phalanx link (might click on child element)
                    while (target && target !== document) {
                        if (target.matches && target.matches('a.phalanxlink')) {
                            e.preventDefault();
                            e.stopPropagation();
                            e.stopImmediatePropagation();

                            var $link = $(target);

                            // Get coordinates from data attributes (set by the existing JS)
                            var galaxy = $link.data('galaxy');
                            var system = $link.data('system');
                            var position = $link.data('position');

                            // If coordinates aren't in data attributes, try to get from row context
                            if (!galaxy || !system || !position) {
                                var $row = $link.closest('.galaxyRow');
                                if ($row.length) {
                                    var rowId = $row.attr('id');
                                    if (rowId) {
                                        position = parseInt(rowId.replace('galaxyRow', ''));
                                    }
                                }
                                galaxy = parseInt($('#galaxy_input').val());
                                system = parseInt($('#system_input').val());
                            }

                            // Validate coordinates
                            if (!galaxy || !system || !position) {
                                alert('Could not determine target coordinates');
                                return false;
                            }

                            // Get CSRF token
                            var csrfToken = $('meta[name="csrf-token"]').attr('content');

                            // Make AJAX request to phalanx scan endpoint
                            $.ajax({
                                url: '{{ route('galaxy.phalanx-scan') }}',
                                type: 'POST',
                                data: {
                                    _token: csrfToken,
                                    galaxy: galaxy,
                                    system: system,
                                    position: position
                                },
                                success: function(response) {
                                    if (response.success) {
                                        // Create styled overlay for results
                                        var overlayHtml = '<div class="phalanxResultsOverlay" style="display:none;">';
                                        overlayHtml += '<div class="messagebox">';
                                        overlayHtml += '<div id="message"><div id="inhalt"><div class="contentz">';

                                        // Header
                                        overlayHtml += '<h2 style="margin-bottom: 10px;">Sensor Phalanx Scan Results</h2>';
                                        overlayHtml += '<div class="splitLine"></div>';
                                        overlayHtml += '<p class="textCenter" style="margin: 10px 0;">Target: <strong>[' + response.scanned_coordinates.galaxy + ':' +
                                                      response.scanned_coordinates.system + ':' +
                                                      response.scanned_coordinates.position + ']</strong></p>';
                                        overlayHtml += '<p class="textCenter" style="margin-bottom: 5px;">Deuterium Cost: <strong>' + response.deuterium_cost.toLocaleString() + '</strong></p>';
                                        overlayHtml += '<p class="textCenter" style="margin-bottom: 15px; font-size: 11px; color: #848484; font-style: italic;">Note: Resources carried by fleets are not visible</p>';

                                        if (response.fleets.length > 0) {
                                            // Create fleet table similar to fleet events
                                            overlayHtml += '<table cellpadding="0" cellspacing="0" style="width: 100%; margin: 10px 0;">';
                                            overlayHtml += '<thead><tr>';
                                            overlayHtml += '<th style="padding: 8px; text-align: left;">Time</th>';
                                            overlayHtml += '<th style="padding: 8px; text-align: center;">Mission</th>';
                                            overlayHtml += '<th style="padding: 8px; text-align: left;">Origin</th>';
                                            overlayHtml += '<th style="padding: 8px; text-align: center;">Ships</th>';
                                            overlayHtml += '<th style="padding: 8px; text-align: left;">Destination</th>';
                                            overlayHtml += '</tr></thead>';
                                            overlayHtml += '<tbody>';

                                            response.fleets.forEach(function(fleet) {
                                                var arrivalDate = new Date(fleet.time_arrival * 1000);
                                                var now = new Date();
                                                var timeUntilArrival = Math.floor((arrivalDate - now) / 1000);
                                                var hours = Math.floor(timeUntilArrival / 3600);
                                                var minutes = Math.floor((timeUntilArrival % 3600) / 60);
                                                var seconds = timeUntilArrival % 60;
                                                var timeRemaining = hours + 'h ' + minutes + 'm ' + seconds + 's';

                                                var rowClass = fleet.direction === 'incoming' ? 'hostile' : 'neutral';
                                                var missionIcon = '/img/fleet/' + fleet.mission_type + '.gif';

                                                overlayHtml += '<tr class="eventFleet" style="border-bottom: 1px solid #0d1014;">';

                                                // Time
                                                overlayHtml += '<td style="padding: 10px; vertical-align: middle;">';
                                                overlayHtml += '<div style="font-weight: bold; color: #6f9fc8;">' + timeRemaining + '</div>';
                                                overlayHtml += '<div style="font-size: 11px; color: #848484;">' + arrivalDate.toLocaleTimeString() + '</div>';
                                                overlayHtml += '</td>';

                                                // Mission
                                                overlayHtml += '<td style="padding: 10px; text-align: center; vertical-align: middle;">';
                                                overlayHtml += '<img src="' + missionIcon + '" alt="' + fleet.mission_name + '" title="' + fleet.mission_name + '" style="display: block; margin: 0 auto 5px;"/>';
                                                overlayHtml += '<div style="font-size: 11px; color: ' + (fleet.direction === 'incoming' ? '#d43635' : '#848484') + ';">' + fleet.direction.toUpperCase() + '</div>';
                                                overlayHtml += '</td>';

                                                // Origin
                                                overlayHtml += '<td style="padding: 10px; vertical-align: middle;">';
                                                overlayHtml += '<figure class="planetIcon planet" style="display: inline-block; margin-right: 5px;"></figure>';
                                                overlayHtml += '<span>[' + fleet.origin.galaxy + ':' + fleet.origin.system + ':' + fleet.origin.position + ']</span>';
                                                overlayHtml += '</td>';

                                                // Ships (with tooltip showing composition)
                                                var shipTooltip = '<div class="htmlTooltip">';
                                                shipTooltip += '<h1>Fleet Composition:</h1>';
                                                shipTooltip += '<div class="splitLine"></div>';
                                                shipTooltip += '<table cellpadding="0" cellspacing="0" class="fleetinfo">';
                                                shipTooltip += '<tr><th colspan="3">Ships:</th></tr>';
                                                fleet.ship_details.forEach(function(ship) {
                                                    shipTooltip += '<tr>';
                                                    shipTooltip += '<td colspan="2">' + ship.name + ':</td>';
                                                    shipTooltip += '<td class="value">' + ship.count.toLocaleString() + '</td>';
                                                    shipTooltip += '</tr>';
                                                });
                                                shipTooltip += '</table>';
                                                shipTooltip += '</div>';

                                                overlayHtml += '<td style="padding: 10px; text-align: center; vertical-align: middle;">';
                                                overlayHtml += '<span class="tooltip tooltipRight tooltipClose" title="' + shipTooltip.replace(/"/g, '&quot;') + '">';
                                                overlayHtml += '<div style="font-weight: bold; font-size: 14px; cursor: help;">' + fleet.total_ships.toLocaleString() + '</div>';
                                                overlayHtml += '<div style="font-size: 11px; color: #848484; cursor: help;">ships</div>';
                                                overlayHtml += '</span>';
                                                overlayHtml += '</td>';

                                                // Destination
                                                overlayHtml += '<td style="padding: 10px; vertical-align: middle;">';
                                                overlayHtml += '<figure class="planetIcon planet" style="display: inline-block; margin-right: 5px;"></figure>';
                                                overlayHtml += '<span>[' + fleet.destination.galaxy + ':' + fleet.destination.system + ':' + fleet.destination.position + ']</span>';
                                                overlayHtml += '</td>';

                                                overlayHtml += '</tr>';
                                            });

                                            overlayHtml += '</tbody></table>';
                                        } else {
                                            overlayHtml += '<div class="splitLine"></div>';
                                            overlayHtml += '<p class="textCenter" style="padding: 30px; font-size: 14px; color: #848484;">No fleet movements detected at this location.</p>';
                                            overlayHtml += '<div class="splitLine"></div>';
                                        }

                                        overlayHtml += '<div class="textCenter" style="padding: 15px 0 10px;">';
                                        overlayHtml += '<button class="btn_blue closePhalanxOverlay">Close</button>';
                                        overlayHtml += '</div>';

                                        overlayHtml += '</div></div></div></div></div>';

                                        // Remove any existing overlay
                                        $('.phalanxResultsOverlay').remove();

                                        // Add to body and show with jQuery UI dialog
                                        $('body').append(overlayHtml);

                                        $('.phalanxResultsOverlay').dialog({
                                            modal: true,
                                            width: 850,
                                            height: 'auto',
                                            maxHeight: 650,
                                            title: 'Sensor Phalanx',
                                            dialogClass: 'phalanx-dialog',
                                            close: function() {
                                                $(this).dialog('destroy').remove();
                                            }
                                        });

                                        // Close button handler
                                        $('.closePhalanxOverlay').on('click', function() {
                                            $('.phalanxResultsOverlay').dialog('close');
                                        });

                                        // Reload galaxy to update deuterium display
                                        loadContentNew(galaxy, system);
                                    } else {
                                        alert('Phalanx scan failed: ' + response.message);
                                    }
                                },
                                error: function(xhr) {
                                    var errorMsg = 'Unknown error';
                                    if (xhr.responseJSON && xhr.responseJSON.message) {
                                        errorMsg = xhr.responseJSON.message;
                                    } else if (xhr.responseText) {
                                        errorMsg = xhr.responseText;
                                    }
                                    alert('Error performing phalanx scan: ' + errorMsg);
                                }
                            });

                            return false;
                        }
                        target = target.parentNode;
                    }
                }, true); // true = capture phase
            })(jQuery)
        </script>
    </div>
@endsection
