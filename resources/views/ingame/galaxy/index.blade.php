@extends('ingame.layouts.main')

@section('content')

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    @if ($is_in_vacation_mode ?? false)
    <div id="galaxycomponent" class="maincontent">
        <div id="inhalt">
            <div id="galaxyContent">
                <div class="galaxyTable" style="background: #000; border: none !important;">
                    <div class="galaxyRow ctGalaxyFleetInfo" id="fleetstatusrow" style="text-align: center;">
                        <span class="fleetStatus" style="color: #B00001; font-weight: bold; font-size: 12px;">{{ __('t_ingame.fleet.player_vacation') }}</span>
                    </div>
                    <div class="galaxyRow" style="display: flex; justify-content: center; align-items: center; padding: 10px 0 30px 0;">
                        <span style="color: #B00001; font-weight: bold; text-align: center; display: flex; align-items: center; gap: 5px;">
                            <span class="icon icon_warning" style="display: inline-block; margin: 0 !important; vertical-align: middle;"></span>
                            <span style="display: inline-block; line-height: 16px; vertical-align: middle;">{{ __('t_ingame.galaxy.vacation_error') }}</span>
                            <span class="icon icon_warning" style="display: inline-block; margin: 0 !important; vertical-align: middle;"></span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
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
            @php
                $galaxyLocaData = [
                    'reservationSuccess'   => __('t_ingame.galaxy.relocate_success'),
                    'questionTitle'        => __('t_ingame.galaxy.relocate_title'),
                    'question'             => __('t_ingame.galaxy.relocate_question', ['cost' => number_format($planet_relocation_cost)]),
                    'deuteriumNeeded'      => __('t_ingame.galaxy.deut_needed_relocate'),
                    'fleetAttacking'       => __('t_ingame.galaxy.fleet_attacking'),
                    'fleetUnderway'        => __('t_ingame.galaxy.fleet_underway'),
                    'discoverySend'        => __('t_ingame.galaxy.discovery_send') . "\n",
                    'discoverySuccess'     => __('t_ingame.galaxy.discovery_success') . "\n",
                    'discoveryUnavailable' => __('t_ingame.galaxy.discovery_unavailable') . "\n",
                    'discoveryUnderway'    => __('t_ingame.galaxy.discovery_underway') . "\n",
                    'discoveryLocked'      => __('t_ingame.galaxy.discovery_locked') . "\n",
                    'discoverQuestionTitle'=> __('t_ingame.galaxy.discovery_title') . "\n",
                    'discoverQuestionText' => __('t_ingame.galaxy.discovery_question'),
                ];
                $locaData = [
                    'LOCA_FLEET_EXPEDITION_TYPE'                     => __('t_ingame.fleet.mission_expedition'),
                    'LOCA_FLEET_DEBRIS'                              => __('t_ingame.fleet.debris_field_lower'),
                    'LOCA_UNAVAILABLE_PHALANXSYSTEM'                 => __('t_ingame.galaxy.phalanx_restricted'),
                    'LOCA_PHALANX_SYSTEM_BUTTON'                     => __('t_ingame.galaxy.system_phalanx'),
                    'LOCA_SPY_SYSTEM_BUTTON'                         => __('t_ingame.galaxy.system_espionage'),
                    'locaErrorNoRequiredResearch'                    => __('t_ingame.galaxy.astro_required'),
                    'LOCA_GALAXY_ERROR_INACCESSIBLE_DUE_TO_VACATION' => __('t_ingame.galaxy.vacation_error'),
                    'LOCA_FLEET_PLAYER_UMODE'                        => __('t_ingame.fleet.player_vacation'),
                    'LOCA_LEFTMENU_GALAXY'                           => __('t_ingame.galaxy.galaxy_nav'),
                    'LOCA_GALAXY_HEADLINE_SUNSYSTEM'                 => __('t_ingame.galaxy.system'),
                    'LOCA_GALAXY_LETS_GO'                            => __('t_ingame.galaxy.go'),
                    'LOCA_ALL_PLANET'                                => __('t_ingame.fleet.planet'),
                    'LOCA_ALL_NAME'                                  => __('t_ingame.galaxy.name_col'),
                    'LOCA_ALL_MOON'                                  => __('t_ingame.fleet.moon'),
                    'LOCA_ALL_DEBRIS_FIELD_SHORT'                    => __('t_ingame.galaxy.debris_short'),
                    'LOCA_GALAXY_PLAYER_STATUS'                      => __('t_ingame.galaxy.player_status'),
                    'LOCA_NETWORK_ALLY'                              => __('t_ingame.galaxy.alliance'),
                    'LOCA_NETWORK_ACTION'                            => __('t_ingame.galaxy.action'),
                    'LOCA_GALAXY_PLANETS_SETTLED'                    => __('t_ingame.galaxy.planets_colonized'),
                    'LOCA_TECH_ESPIONAGEPROBE_SNAME'                 => __('t_ingame.galaxy.probes_short'),
                    'LOCA_TECH_RECYCLER_SNAME'                       => __('t_ingame.galaxy.recycler_short'),
                    'LOCA_TECH_INTERPLANETARYMISSILE_SNAME'          => __('t_ingame.galaxy.ipm_short'),
                    'LOCA_GALAXY_SLOTS_FULL'                         => __('t_ingame.galaxy.used_slots'),
                    'LOCA_GALAXY_LEGEND'                             => __('t_ingame.galaxy.legend'),
                    'LOCA_GALAXY_PLAYER_STATUS_A'                    => __('t_ingame.galaxy.status_admin_abbr'),
                    'LOCA_GALAXY_LEGEND_ADMIN'                       => __('t_ingame.galaxy.legend_admin'),
                    'LOCA_GALAXY_PLAYER_STATUS_S'                    => __('t_ingame.galaxy.status_strong_abbr'),
                    'LOCA_GALAXY_LEGEND_STRONG_PLAYER'               => __('t_ingame.galaxy.legend_strong'),
                    'LOCA_GALAXY_PLAYER_STATUS_N'                    => __('t_ingame.galaxy.status_noob_abbr'),
                    'LOCA_GALAXY_LEGEND_NOOB'                        => __('t_ingame.galaxy.legend_noob'),
                    'LOCA_GALAXY_PLAYER_STATUS_OUTLAW'               => __('t_ingame.galaxy.status_outlaw_abbr'),
                    'LOCA_GALAXY_LEGEND_OUTLAW'                      => __('t_ingame.galaxy.legend_outlaw'),
                    'LOCA_GALAXY_PLAYER_STATUS_U'                    => __('t_ingame.galaxy.status_vacation_abbr'),
                    'LOCA_STATION_JUMP_VACATION'                     => __('t_ingame.galaxy.vacation_mode'),
                    'LOCA_GALAXY_PLAYER_STATUS_G'                    => __('t_ingame.galaxy.status_banned_abbr'),
                    'LOCA_GALAXY_LEGEND_BANNED'                      => __('t_ingame.galaxy.legend_banned'),
                    'LOCA_GALAXY_PLAYER_STATUS_I'                    => __('t_ingame.galaxy.status_inactive_abbr'),
                    'LOCA_GALAXY_LEGEND_SEVEN_DAYS_INACTIVE'         => __('t_ingame.galaxy.legend_inactive_7'),
                    'LOCA_GALAXY_PLAYER_STATUS_I_LONG'               => __('t_ingame.galaxy.status_longinactive_abbr'),
                    'LOCA_GALAXY_LEGEND_TWENTYEIGHT_DAYS_INACTIVE'   => __('t_ingame.galaxy.legend_inactive_28'),
                    'LOCA_GALAXY_PLAYER_STATUS_EP'                   => __('t_ingame.galaxy.status_honorable_abbr'),
                    'LOCA_GALAXY_LEGEND_HONORABLE_TARGET'            => __('t_ingame.galaxy.legend_honorable'),
                    'LOCA_ALL_ACTIVITY'                              => __('t_ingame.galaxy.activity'),
                    'LOCA_FLEET_NO_ACTION_AVAILABLE'                 => __('t_ingame.galaxy.no_action'),
                    'LOCA_ALL_TIME_MINUTE'                           => __('t_ingame.galaxy.time_minute_abbr'),
                    'LOCA_GALAXY_MOON_DIAMETER_KM'                   => __('t_ingame.galaxy.moon_diameter_km'),
                    'LOCA_OVERVIEW_JS_KM'                            => __('t_ingame.galaxy.km'),
                    'LOCA_ALL_METAL'                                 => __('t_ingame.fleet.metal'),
                    'LOCA_ALL_CRYSTAL'                               => __('t_ingame.fleet.crystal'),
                    'LOCA_ALL_DEUTERIUM'                             => __('t_ingame.fleet.deuterium'),
                    'LOCA_GALAXY_PATHFINDER_NEEDED'                  => __('t_ingame.galaxy.pathfinders_needed'),
                    'LOCA_GALAXY_RECYCLER_NEEDED'                    => __('t_ingame.galaxy.recyclers_needed'),
                    'LOCA_GALAXY_DEBRIS_REDUCE'                      => __('t_ingame.galaxy.mine_debris'),
                    'LOCA_PHALANX_ERROR_NOT_ENOUTH_DEUT'             => __('t_ingame.galaxy.phalanx_no_deut'),
                    'LOCA_GALAXY_USE_PHALANX'                        => __('t_ingame.galaxy.use_phalanx'),
                    'LOCA_GALAXY_ERROR_COLONIZATION'                 => __('t_ingame.galaxy.colonize_error'),
                    'LOCA_ALL_PLAYER'                                => __('t_ingame.fleet.player_label'),
                    'LOCA_GALAXY_RANKING'                            => __('t_ingame.galaxy.ranking'),
                    'LOCA_MESSAGES_ESPIONAGEREPORT'                  => __('t_ingame.galaxy.espionage_report'),
                    'LOCA_FLEET_MISSILEATTACK'                       => __('t_ingame.galaxy.missile_attack'),
                    'LOCA_GALAXY_RANK'                               => __('t_ingame.galaxy.rank'),
                    'LOCA_NETWORK_USERS'                             => __('t_ingame.galaxy.alliance_member'),
                    'LOCA_ALLIANCE_CLASS'                            => __('t_ingame.galaxy.alliance_class'),
                    'LOCA_FLEET_NO_FREE_SLOTS'                       => __('t_ingame.fleet.no_free_slots'),
                    'LOCA_ALL_AJAXLOAD'                              => __('t_ingame.fleet.load_dots'),
                    'LOCA_EVENTH_ENEMY_INFINITELY_SPACE'             => __('t_ingame.fleet.deep_space'),
                    'LOCA_FLEET_NO_ESPIONAGE'                        => __('t_ingame.galaxy.espionage_not_possible'),
                    'LOCA_FLEET_ESPIONAGE'                           => __('t_ingame.galaxy.espionage'),
                    'LOCA_HEADER_GETADMIRAL'                         => __('t_ingame.galaxy.hire_admiral'),
                    'LOCA_ALL_DARKMATTER'                            => __('t_ingame.galaxy.dark_matter'),
                    'LOCA_OUTLAW_EXPLANATION'                        => __('t_ingame.galaxy.outlaw_explanation'),
                    'LOCA_GALAXY_LEGEND_HONORABLE_TARGET_EXPLANATION' => __('t_ingame.galaxy.honorable_target_explanation'),
                    'LOCA_GALAXY_SYSTEM_DISCOVERY'                   => __('t_ingame.galaxy.discoveries'),
                    'LOCA_GALAXY_SYSTEM_DISCOVERY_TOOLTIP'           => __('t_ingame.galaxy.discoveries_tooltip'),
                    'LOCA_EXPEDITION_FLEET_TEMPLATE'                 => __('t_ingame.galaxy.expedition_fleet'),
                    'LOCA_FLEET_SEND'                                => __('t_ingame.fleet.send_fleet'),
                    'LOCA_ALL_SEND'                                  => __('t_ingame.galaxy.send'),
                    'LOCA_FLEET_TEMPLATE_ADMIRAL_NEEDED'             => __('t_ingame.galaxy.admiral_needed'),
                ];
            @endphp
            var galaxyLoca = @json($galaxyLocaData);
            var loca = @json($locaData);
            var shipsendingDone = 1;
            var premiumLink = "#?page=premium&openDetail=3";
            var sendDiscoverSystemUrl = "";
            var missleAttackLink = "#?page=ajax&component=missileattacklayer&width=669&height=250";
            var canSwitchGalaxy = true;
            var notEnoughDeuteriumMessage = @json(__('t_ingame.galaxy.deut_needed_relocate'));
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
                        checkIntInput($(this), 1, maxGalaxies)
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
                        checkIntInput($(this), 1, maxSystems)
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
                        checkIntInput($(this), 1, maxGalaxies)
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
                        checkIntInput($(this), 1, maxSystems)
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
                    <span class="galaxy_icons solarsystem tooltip" title="{{ __('t_ingame.galaxy.system') }}"></span>
                    <span class="galaxy_icons prev ipiHintable" onclick="submitOnKey('ArrowLeft');" data-ipi-hint="ipiGalaxySwitchGalaxy"></span>
                    <input id="system_input" class="hideNumberSpin" maxlength="3" type="number" pattern="[0-9]*" value="3" tabindex="2" name="system">
                    <span class="galaxy_icons next ipiHintable" onclick="submitOnKey('ArrowRight');" data-ipi-hint="ipiGalaxySwitchGalaxy"></span>
                    <div class="btn_blue" onclick="submitForm();">{{ __('t_ingame.galaxy.go') }}</div>
                    <div class="systembuttons">
                        <a class="btn_blue tooltip phalanxlink btn_system_action" href="javascript:void(0);" title="{{ __('t_ingame.galaxy.system_phalanx') }}" disabled="disabled">
                            <img alt="" src="/img/icons/1cae570e41fc188133be9d548d6523.gif" class="icon_allianceBonus" style="filter:grayscale(1);"> {{ __('t_ingame.galaxy.system_phalanx') }}
                        </a>
                        <a class="btn_blue tooltip spysystemlink btn_system_action" disabled="disabled" title="{{ __('t_ingame.galaxy.system_espionage') }}">
                            <img alt="" src="/img/icons/1cae570e41fc188133be9d548d6523.gif" class="icon_allianceBonus" style="filter:grayscale(1);"> {{ __('t_ingame.galaxy.system_espionage') }}
                        </a>

                        <div id="discoverSystemBtn" class="btn_blue tooltip discoverSystemLink btn_system_action" title="{{ __('t_ingame.galaxy.discoveries_tooltip') }}" disabled="disabled">
                            <div class="disabled"></div>&nbsp;{{ __('t_ingame.galaxy.discoveries') }}
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
                                {{ __('t_ingame.galaxy.probes_short') }}:<span id="probeValue">0</span>
                            </div>
                            <div id="recycler">
                                {{ __('t_ingame.galaxy.recycler_short') }}:<span id="recyclerValue">0</span>
                            </div>
                            <div id="rockets">
                                {{ __('t_ingame.galaxy.ipm_short') }}:<span id="missileValue">0</span>
                            </div>
                            <div id="slots">
                                {{ __('t_ingame.galaxy.used_slots') }}:<span id="slotUsed">0</span>/<span id="slotValue">0</span>
                            </div>
                            <div id="galaxyHeaderDiscoveryCount">
                                {{ __('t_ingame.galaxy.discoveries') }}: 0/3950
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
                        <div class="galaxyCell span1-2">{{ __('t_ingame.galaxy.planet_col') }}</div>
                        <div class="galaxyCell cellPlanetName">{{ __('t_ingame.galaxy.name_col') }}</div>
                        <div class="galaxyCell cellMoon">{{ __('t_ingame.galaxy.moon_col') }}</div>
                        <div class="galaxyCell cellDebris">{{ __('t_ingame.galaxy.debris_short') }}</div>
                        <div class="galaxyCell cellPlayerName">{{ __('t_ingame.galaxy.player_status') }}</div>
                        <div class="galaxyCell cellAlliance">{{ __('t_ingame.galaxy.alliance') }}</div>
                        <div class="galaxyCell cellAction">{{ __('t_ingame.galaxy.action') }}</div>
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
                                <h3 class="title float_left">{{ __('t_ingame.fleet.deep_space') }}:</h3>
                            </div>
                            <div id="expeditionDebrisSlotDebrisContainer">

                            </div>
                            <div id="expeditionDebrisSlotActions">
                                <div id="galaxyExpeditionFleetTemplateContainer"
                                     class="tooltip hideTooltipOnMouseenter disabled"
                                     title="{{ __('t_ingame.galaxy.admiral_needed') }}"
                                >
                                    <a id="expeditionFleetTemplateBtn"
                                       class="dark_highlight_tablet"
                                    >
                                        <span class="icon icon_combatunits"></span>
                                        <span class="expedtionFleetTemplateBtnTitle">{{ __('t_ingame.galaxy.expedition_fleet') }}</span>
                                    </a>
                                    <select class="expeditionFleetTemplateSelect" size="1" title="test"
                                            id="expeditionFleetTemplateSelect" disabled>
                                        <option value="0">-</option>
                                    </select>
                                </div>

                                <div id="expeditionbutton" class="btn_blue float_right btn_system_action" onClick="doExpedition();">
                                    {{ __('t_ingame.fleet.mission_expedition') }}
                                </div>
                                <div id="sendExpeditionFleetTemplateFleet" class="btn_blue float_right btn_system_action" style="display: none" onClick="sendExpedtionFleetFromTemplate()" disabled>
                                    {{ __('t_ingame.galaxy.send') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="galaxyRow ctGalaxyFleetInfo" id="fleetstatusrow"></div>
                    <div class="galaxyRow ctGalaxyFooter">
                        <div id="colonized"><span id="amountColonized">0</span>&nbsp;{{ __('t_ingame.galaxy.planets_colonized') }}</div>
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
                <h1>{{ __('t_ingame.galaxy.legend') }}</h1>
                <div class="splitLine"></div>
                <dl>
                    <dt class="abbreviation status_abbr_admin">{{ __('t_ingame.galaxy.status_admin_abbr') }}</dt>
                    <dd class="description">{{ __('t_ingame.galaxy.legend_admin') }}</dd>

                    <dt class="abbreviation status_abbr_strong">{{ __('t_ingame.galaxy.status_strong_abbr') }}</dt>
                    <dd class="description">{{ __('t_ingame.galaxy.legend_strong') }}</dd>

                    <dt class="abbreviation status_abbr_noob">{{ __('t_ingame.galaxy.status_noob_abbr') }}</dt>
                    <dd class="description">{{ __('t_ingame.galaxy.legend_noob') }}</dd>

                    <dt class="abbreviation status_abbr_outlaw">{{ __('t_ingame.galaxy.status_outlaw_abbr') }}</dt>
                    <dd class="description">{{ __('t_ingame.galaxy.legend_outlaw') }}</dd>

                    <dt class="abbreviation status_abbr_vacation">{{ __('t_ingame.galaxy.status_vacation_abbr') }}</dt>
                    <dd class="description">{{ __('t_ingame.galaxy.vacation_mode') }}</dd>

                    <dt class="abbreviation status_abbr_banned">{{ __('t_ingame.galaxy.status_banned_abbr') }}</dt>
                    <dd class="description">{{ __('t_ingame.galaxy.legend_banned') }}</dd>

                    <dt class="abbreviation status_abbr_inactive">{{ __('t_ingame.galaxy.status_inactive_abbr') }}</dt>
                    <dd class="description">{{ __('t_ingame.galaxy.legend_inactive_7') }}</dd>

                    <dt class="abbreviation status_abbr_longinactive">{{ __('t_ingame.galaxy.status_longinactive_abbr') }}</dt>
                    <dd class="description">{{ __('t_ingame.galaxy.legend_inactive_28') }}</dd>

                    <dt class="abbreviation status_abbr_honorableTarget">{{ __('t_ingame.galaxy.status_honorable_abbr') }}</dt>
                    <dd class="description">{{ __('t_ingame.galaxy.legend_honorable') }}</dd>
                </dl>
            </div>
        </div>
        <br class="clearfix">
        <script type="text/javascript">
            var checkTargetUrl = "#?page=componentOnly&component=fleetdispatch&action=checkTarget&asJson=1"
            var missionExpedition = 15
            var spaceObjectTypePlanet = 1
            var expeditionPosition = 16

            // Update deuterium after phalanx scan
            function updateDeuteriumAfterScan(scanCost) {
                if (scanCost && typeof resourcesBar !== 'undefined') {
                    var newDeuterium = resourcesBar.resources.deuterium.amount - scanCost;
                    resourcesBar.resources.deuterium.amount = newDeuterium;
                    var deuteriumElement = $('#resources_deuterium');
                    deuteriumElement.attr('data-raw', newDeuterium);
                    deuteriumElement.text(newDeuterium.toLocaleString('de-DE'));
                }
            }

            // Phalanx scan functionality
            function scanWithPhalanx(galaxy, system, position) {
                var scan_url = "{{ route('phalanx.scan') }}";
                var csrf_token = "{{ csrf_token() }}";

                $.ajax({
                    url: scan_url,
                    type: 'POST',
                    data: {
                        _token: csrf_token,
                        galaxy: galaxy,
                        system: system,
                        position: position
                    },
                    success: function(response) {
                        if (response.success) {
                            updateDeuteriumAfterScan(response.scan_cost);
                            showPhalanxResults(response);
                        } else {
                            alert('Phalanx scan failed: ' + (response.error || 'Unknown error'));
                        }
                    },
                    error: function(xhr) {
                        var error_message = 'Phalanx scan failed';
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            error_message = xhr.responseJSON.error;
                        }
                        alert(error_message);
                    }
                });
            }

            function showPhalanxResults(data) {
                // Build OGame-style phalanx dialog
                var coords = data.target.galaxy + ':' + data.target.system + ':' + data.target.position;
                var dialog_title = coords + ' {{ __('t_ingame.galaxy.sensor_report') }}';

                if (data.target.planet_name && data.target.player_name) {
                    dialog_title = data.target.planet_name + ' ' + coords + ' (' + data.target.player_name + ') {{ __('t_ingame.galaxy.sensor_report') }}';
                }

                // If error, wrap error_message in div
                var content_html = data.content_html;
                if (data.is_error) {
                    content_html = '<div id="phalanxEventContent">' + data.error_message + '</div>';
                }

                var modal_html = '<div id="phalanx-dialog" tabindex="-1" role="dialog" class="ui-dialog ui-corner-all ui-widget ui-widget-content ui-front ui-draggable" style="display: none; height: auto; width: auto;" aria-describedby="phalanx-content" aria-labelledby="phalanx-title">';
                modal_html += '<div class="ui-dialog-titlebar ui-corner-all ui-widget-header ui-helper-clearfix ui-draggable-handle">';
                modal_html += '<span id="phalanx-title" class="ui-dialog-title">' + dialog_title;

                // Refresh button inside title
                modal_html += '<a class="refreshPhalanxLink tooltip js_hideTipOnMobile overlay fleft" data-overlay-same="true" ';
                modal_html += 'href="javascript:void(0)" onclick="refreshPhalanxContent(' + data.target.galaxy + ',' + data.target.system + ',' + data.target.position + ')" ';
                modal_html += 'data-tooltip-title="{{ __('t_ingame.galaxy.refresh') }}">';
                modal_html += '<span class="icon icon_reload"></span>';
                modal_html += '</a>';

                modal_html += '</span>';
                modal_html += '<button type="button" class="ui-button ui-corner-all ui-widget ui-button-icon-only ui-dialog-titlebar-close" onclick="closePhalanxModal()" title="">';
                modal_html += '<span class="ui-button-icon ui-icon ui-icon-closethick"></span>';
                modal_html += '<span class="ui-button-icon-space"> </span>';
                modal_html += '</button>';
                modal_html += '</div>';

                modal_html += '<div id="phalanx-content" class="overlayDiv phalanx ui-dialog-content ui-widget-content" style="width: auto; min-height: 112px; max-height: none; height: auto;" data-page="ajax">';
                modal_html += '<div id="phalanxWrap">';
                // Insert HTML from backend
                modal_html += content_html;
                modal_html += '</div>'; // End phalanxWrap
                modal_html += '</div>'; // End dialog-content
                modal_html += '</div>'; // End dialog

                // Remove existing dialog if any
                $('#phalanx-dialog').remove();

                // Add dialog to body
                $('body').append(modal_html);

                // Show dialog and initialize
                var dialog = $('#phalanx-dialog');
                dialog.show();

                // Calculate and set position (center on screen)
                var windowWidth = $(window).width();
                var windowHeight = $(window).height();
                var dialogWidth = dialog.outerWidth();
                var dialogHeight = dialog.outerHeight();

                var left = Math.max(0, (windowWidth - dialogWidth) / 2);
                var top = Math.max(50, (windowHeight - dialogHeight) / 2);

                dialog.css({
                    'left': left + 'px',
                    'top': top + 'px',
                    'width': 'auto',
                    'height': 'auto'
                });

                // Make draggable if jQuery UI is available
                if (typeof $.fn.draggable !== 'undefined') {
                    dialog.draggable({
                        handle: '.ui-dialog-titlebar',
                        containment: 'window',
                        cursor: 'move'
                    });
                }

                // Fade in
                dialog.hide().fadeIn(200);

            }


            function formatFleetTime(timestamp) {
                var date = new Date(timestamp * 1000);
                var now = new Date();
                var diff = Math.floor((date - now) / 1000);

                if (diff < 0) {
                    return @json(__('t_ingame.galaxy.arrived'));
                }

                var hours = Math.floor(diff / 3600);
                var minutes = Math.floor((diff % 3600) / 60);
                var seconds = diff % 60;

                return hours + 'h ' + minutes + 'm ' + seconds + 's';
            }

            function refreshPhalanxContent(galaxy, system, position) {
                $.ajax({
                    url: '{{ route('phalanx.scan') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        galaxy: galaxy,
                        system: system,
                        position: position
                    },
                    success: function(response) {
                        if (response.success) {
                            updateDeuteriumAfterScan(response.scan_cost);
                            updatePhalanxContent(response);
                        } else {
                            alert('Phalanx refresh failed: ' + (response.error || 'Unknown error'));
                        }
                    },
                    error: function(xhr) {
                        var error_message = 'Phalanx refresh failed';
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            error_message = xhr.responseJSON.error;
                        }
                        alert(error_message);
                    }
                });
            }

            function updatePhalanxContent(data) {
                // If error, wrap error_message in div
                var content_html = data.content_html;
                if (data.is_error) {
                    content_html = '<div id="phalanxEventContent">' + data.error_message + '</div>';
                }

                // Update content with HTML from backend
                $('#phalanxWrap').html(content_html);

            }

            function closePhalanxModal() {
                $('#phalanx-dialog').fadeOut(200, function() {
                    $(this).remove();
                });
            }
        </script>

@include('ingame.shared.buddy.bbcode-parser')

        <script type="text/javascript">
            var buildListCountdowns = new Array();
            (function($){
                initGalaxyNew();
                $(document.documentElement).off( "keyup" );
                $(document.documentElement).on( "keyup", keyevent );
            })(jQuery)

            // Initialize buddy dialog after it loads
            window.initBuddyDialog = function() {
                var locaKeys = {"bold":"Bold","italic":"Italic","underline":"Underline","stroke":"Strikethrough","sub":"Subscript","sup":"Superscript","fontColor":"Font colour","fontSize":"Font size","backgroundColor":"Background colour","backgroundImage":"Background image","tooltip":"Tool-tip","alignLeft":"Left align","alignCenter":"Centre align","alignRight":"Right align","alignJustify":"Justify","block":"Break","code":"Code","spoiler":"Spoiler","moreopts":"","list":"List","hr":"Horizontal line","picture":"Image","link":"Link","email":"Email","player":"Player","item":"Item","coordinates":"Coordinates","preview":"Preview","textPlaceHolder":"Text...","playerPlaceHolder":"Player ID or name","itemPlaceHolder":"Item ID","coordinatePlaceHolder":"Galaxy:system:position","charsLeft":"Characters remaining","colorPicker":{"ok":"Ok","cancel":"Cancel","rgbR":"R","rgbG":"G","rgbB":"B"},"backgroundImagePicker":{"ok":"Ok","repeatX":"Repeat horizontally","repeatY":"Repeat vertically"}};

                // Block BBCode preview AJAX calls temporarily to prevent 405 errors
                var blockPreviewCalls = true;
                $.ajaxPrefilter(function(options, originalOptions, jqXHR) {
                    // Block POST requests to preview URLs (empty, /overview, or invalid URLs)
                    if (blockPreviewCalls && options.type === 'POST' &&
                        (!options.url || options.url === '' || options.url.indexOf('/overview') > -1 ||
                         options.url.indexOf('&imgAllowed=') === 0)) {
                        jqXHR.abort();
                        return false;
                    }
                });

                initBuddyRequestForm();

                // TODO: The BBCode editor includes an "Item" dropdown for linking game items.
                // This feature is not yet implemented as the item system is not available.
                // When items are implemented, update the BBCode parser and preview to support [item]ItemID[/item] tags.
                initBBCodeEditor(locaKeys, {}, false, '.buddy_request_textarea', 5000, true);

                // Re-enable AJAX calls after initialization
                setTimeout(function() {
                    blockPreviewCalls = false;
                }, 500);

                setTimeout(function() {
                    var $textarea = $('.buddy_request_textarea');
                    var $container = $textarea.closest('.markItUpContainer');
                    var $preview = $container.find('.miu_preview_container');

                    $container.find('.preview_link').off('click').on('click', function(e) {
                        e.preventDefault();
                        if ($preview.is(':visible')) {
                            $preview.hide();
                            $(this).removeClass('active');
                        } else {
                            $preview.html(window.buddyBBCodeParser($textarea.val())).show();
                            $(this).addClass('active');
                        }
                    });
                }, 150);

                $('#buddyRequestForm').off('submit').on('submit', function(e) {
                    e.preventDefault();
                    var form = $(this);
                    $.ajax({
                        url: form.attr('action'),
                        type: 'POST',
                        data: form.serialize(),
                        success: function(response) {
                            if (response.success) {
                                fadeBox(@json(__('t_ingame.buddy.request_sent')), false);
                                form.closest('.ui-dialog-content').dialog('close');
                                setTimeout(function() {
                                    form.closest('.overlayDiv').remove();
                                    form.closest('.ui-dialog').remove();
                                }, 100);
                            } else {
                                fadeBox(response.message || @json(__('t_ingame.buddy.request_failed')), true);
                            }
                        },
                        error: function(xhr) {
                            var errorMessage = @json(__('t_ingame.buddy.request_failed'));
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            fadeBox(errorMessage, true);
                        }
                    });
                });
            };

            // Global function for sending buddy requests (accessible from galaxy AJAX content)
            window.sendBuddyRequestDialog = function(playerId, playerName) {
                // Close any existing buddy request dialogs
                $('.buddyRequestDialog').each(function() {
                    try {
                        $(this).dialog('destroy');
                    } catch(e) {}
                    $(this).remove();
                });
                $('.ui-dialog:has(.buddyRequestDialog)').remove();

                // Create dialog container
                var $dialog = $('<div class="overlayDiv buddyRequestDialog"></div>').css('display', 'none');
                $('body').append($dialog);

                // Initialize the dialog first
                $dialog.dialog({
                    title: @json(__('t_ingame.buddy.request_to')) + ' ' + playerName,
                    width: 'auto',
                    height: 'auto',
                    modal: false,
                    closeText: '',
                    position: { my: "center", at: "center" },
                    close: function() {
                        $(this).dialog('destroy');
                        $(this).remove();
                    }
                });

                // Load content via AJAX
                var dialogUrl = '{{ route('buddies.requestdialog') }}?id=' + playerId + '&name=' + encodeURIComponent(playerName) + '&_=' + Date.now();

                $.get(dialogUrl).done(function(data) {
                    $dialog.empty().append(data);

                    // Initialize buddy dialog BBCode editor
                    if (typeof window.initBuddyDialog === 'function') {
                        window.initBuddyDialog();
                    }

                    // Reposition after content loads - check if dialog is still initialized
                    try {
                        if ($dialog.hasClass('ui-dialog-content')) {
                            $dialog.dialog('option', 'position', $dialog.dialog('option', 'position'));
                        }
                    } catch(e) {
                        // Silently ignore repositioning errors
                    }
                }).fail(function() {
                    try {
                        $dialog.dialog('close');
                    } catch(e) {}
                });
            };

            // Handle buddy request button clicks in galaxy (using event delegation for dynamically loaded content)
            $(document).on('click', '.buddyrequest, .sendBuddyRequestLink', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                // Extract player info from data attributes
                var playerId = $(this).data('playerid');
                var playerName = $(this).data('playername');

                if (playerId && playerName) {
                    sendBuddyRequestDialog(playerId, playerName);
                }
                return false;
            });

            // Handle ignore player button clicks
            $(document).on('click', '.ignorePlayerLink', function(e) {
                e.preventDefault();
                var playerId = $(this).data('playerid');
                var playerName = $(this).data('playername');

                if (playerId && playerName) {
                    // Confirm before ignoring
                    if (confirm(@json(__('t_ingame.buddy.ignore_confirm')) + ' ' + playerName + '?')) {
                        $.ajax({
                            url: '{{ route('buddies.ignore') }}',
                            type: 'POST',
                            data: {
                                ignored_user_id: playerId,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    fadeBox(@json(__('t_ingame.buddy.ignore_success')), false);
                                } else {
                                    fadeBox(response.message || @json(__('t_ingame.buddy.ignore_failed')), true);
                                }
                            },
                            error: function(xhr) {
                                var errorMessage = @json(__('t_ingame.buddy.ignore_failed'));
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                                fadeBox(errorMessage, true);
                            }
                        });
                    }
                }
                return false;
            });
        </script>
    </div>
    @endif
@endsection
