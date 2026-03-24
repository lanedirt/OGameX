<?php

return [
    // -------------------------------------------------------------------------
    // Overview page
    // -------------------------------------------------------------------------

    'overview' => [
        // Planet stats panel (typewriter animation)
        'diameter'             => 'Diameter',
        'temperature'          => 'Temperature',
        'position'             => 'Position',
        'points'               => 'Points',
        'honour_points'        => 'Honour points',
        'score_place'          => 'Place',
        'score_of'             => 'of',

        // Page / section headings
        'page_title'           => 'Overview',
        'buildings'            => 'Buildings',
        'research'             => 'Research',

        // Planet header buttons
        'switch_to_moon'       => 'Switch to moon',
        'switch_to_planet'     => 'Switch to planet',
        'abandon_rename'       => 'Abandon/Rename',
        'abandon_rename_title' => 'Abandon/Rename Planet',
    ],

    // -------------------------------------------------------------------------
    // Planet relocation / planet move
    // -------------------------------------------------------------------------

    'planet_move' => [
        'resettle_title' => 'Resettle Planet',
        'cancel_confirm' => 'Are you sure that you wish to cancel this planet relocation? The reserved position will be released.',
        'cancel_success' => 'The planet relocation was successfully cancelled.',
        'blockers_title' => 'The following things are currently standing in the way of your planet relocation:',
        'no_blockers'    => 'Nothing can get in the way of the planet\'s planned relocation now.',
        'cooldown_title' => 'Time until next possible relocation',
        'to_galaxy'      => 'To galaxy',
        'relocate'       => 'Relocate',
        'cancel'         => 'cancel',
        'explanation'    => 'The relocation allows you to move your planets to another position in a distant system of your choosing.<br /><br />The actual relocation first takes place 24 hours after activation. In this time, you can use your planets as normal. A countdown shows you how much time remains prior to the relocation.<br /><br />Once the countdown has run down and the planet is to be moved, none of your fleets that are stationed there can be active. At this time, there should also be nothing in construction, nothing being repaired and nothing researched. If there is a construction task, a repair task or a fleet still active upon the countdown\'s expiry, the relocation will be cancelled.<br /><br />If the relocation is successful, you will be charged 240.000 Dark Matter. The planets, the buildings and the stored resources including moon will be moved immediately. Your fleets travel to the new coordinates automatically with the speed of the slowest ship. The jump gate to a relocated moon is deactivated for 24 hours.',
    ],

    // -------------------------------------------------------------------------
    // Shared UI strings (buttons, dialog labels)
    // -------------------------------------------------------------------------

    'shared' => [
        'caution' => 'Caution',
        'yes'     => 'yes',
        'no'      => 'No',
        'error'   => 'Error',
    ],

    // -------------------------------------------------------------------------
    // Shared building page strings (resources, facilities, research, shipyard, defense)
    // -------------------------------------------------------------------------

    'buildings' => [
        // Building icon status tooltips
        'under_construction'     => 'Under construction',
        'vacation_mode_error'    => 'Error, player is in vacation mode',
        'requirements_not_met'   => 'Requirements are not met!',
        'wrong_class'            => 'Wrong character class!',
        'no_moon_building'       => "You can't construct that building on a moon!",
        'not_enough_resources'   => 'Not enough resources!',
        'queue_full'             => 'Queue is full',
        'not_enough_fields'      => 'Not enough fields!',
        'shipyard_busy'          => 'The shipyard is still busy',
        'research_in_progress'   => 'Research is currently being carried out!',
        'research_lab_expanding' => 'Research Lab is being expanded.',
        'shipyard_upgrading'     => 'Shipyard is being upgraded.',
        'nanite_upgrading'       => 'Nanite Factory is being upgraded.',
        'max_amount_reached'     => 'Maximum number reached!',
        // Expand upgrade button (named params: :title, :level)
        'expand_button'          => 'Expand :title on level :level',
        // JS loca object strings
        'loca_notice'            => 'Reference',
        'loca_demolish'          => 'Really downgrade TECHNOLOGY_NAME by one level?',
        'loca_lifeform_cap'      => 'One or more associated bonuses is already maxed out. Do you want to continue construction anyway?',
        'last_inquiry_error'     => 'Your last action could not be processed. Please try again.',
        'planet_move_warning'    => 'Caution! This mission may still be running once the relocation period starts and if this is the case, the process will be canceled. Do you really want to continue with this job?',
    ],

    // -------------------------------------------------------------------------
    // Resources page (mines / storage buildings)
    // -------------------------------------------------------------------------

    'resources_page' => [
        'page_title'    => 'Resources',
        'settings_link' => 'Resource settings',
        'section_title' => 'Resource buildings',
    ],

    // -------------------------------------------------------------------------
    // Facilities page
    // -------------------------------------------------------------------------

    'facilities_page' => [
        'page_title'     => 'Facilities',
        'section_title'  => 'Facility buildings',
        'use_jump_gate'  => 'Use Jump Gate',
        'jump_gate'      => 'Jump Gate',
        'alliance_depot' => 'Alliance Depot',
        'burn_confirm'   => 'Are you sure you want to burn up this wreck field? This action cannot be undone.',
    ],

    // -------------------------------------------------------------------------
    // Research page
    // -------------------------------------------------------------------------

    'research_page' => [
        'basic'    => 'Basic research',
        'drive'    => 'Drive research',
        'advanced' => 'Advanced researches',
        'combat'   => 'Combat research',
    ],

    // -------------------------------------------------------------------------
    // Shipyard page
    // -------------------------------------------------------------------------

    'shipyard_page' => [
        'battleships' => 'Battleships',
        'civil_ships' => 'Civil ships',
    ],

    // -------------------------------------------------------------------------
    // Defense page
    // -------------------------------------------------------------------------

    'defense_page' => [
        'page_title'    => 'Defense',
        'section_title' => 'Defensive structures',
    ],

    // -------------------------------------------------------------------------
    // Resource settings page
    // -------------------------------------------------------------------------

    'resource_settings' => [
        'production_factor'  => 'Production factor',
        'recalculate'        => 'Recalculate',
        'metal'              => 'Metal',
        'crystal'            => 'Crystal',
        'deuterium'          => 'Deuterium',
        'energy'             => 'Energy',
        'basic_income'       => 'Basic Income',
        'level'              => 'Level',
        'number'             => 'Number:',
        'items'              => 'Items',
        'geologist'          => 'Geologist',
        'mine_production'    => 'mine production',
        'engineer'           => 'Engineer',
        'energy_production'  => 'energy production',
        'character_class'    => 'Character Class',
        'commanding_staff'   => 'Commanding Staff',
        'storage_capacity'   => 'Storage capacity',
        'total_per_hour'     => 'Total per hour:',
        'total_per_day'      => 'Total per day',
        'total_per_week'     => 'Total per week:',
    ],

    // -------------------------------------------------------------------------
    // Destroy rockets dialog (facilities page)
    // -------------------------------------------------------------------------

    'facilities_destroy' => [
        'silo_description'  => 'Missile silos are used to construct, store and launch interplanetary and anti-ballistic missiles. With each level of the silo, five interplanetary missiles or ten anti-ballistic missiles can be stored. One Interplanetary missile uses the same space as two Anti-Ballistic missiles. Storage of both Interplanetary missiles and Anti-Ballistic missiles in the same silo is allowed.',
        'silo_capacity'     => 'A missile silo on level :level can hold :ipm interplanetary missiles or :abm anti-ballistic missiles.',
        'type'              => 'Type',
        'number'            => 'Number',
        'tear_down'         => 'tear down',
        'proceed'           => 'Proceed',
        'enter_minimum'     => 'Please enter at least one missile to destroy',
        'not_enough_abm'    => 'You do not have that many Anti-Ballistic Missiles',
        'not_enough_ipm'    => 'You do not have that many Interplanetary Missiles',
        'destroyed_success' => 'Missiles destroyed successfully',
        'destroy_failed'    => 'Failed to destroy missiles',
        'error'             => 'An error occurred. Please try again.',
    ],

    // -------------------------------------------------------------------------
    // Fleet pages (dispatch + movement)
    // -------------------------------------------------------------------------

    'fleet' => [
        // Page / step headers
        'dispatch_1_title'         => 'Fleet Dispatch I',
        'dispatch_2_title'         => 'Fleet Dispatch II',
        'dispatch_3_title'         => 'Fleet Dispatch III',
        'movement_title'           => 'Fleet movement',
        'to_movement'              => 'To fleet movement',

        // Status bar
        'fleets'                   => 'Fleets',
        'expeditions'              => 'Expeditions',
        'reload'                   => 'Reload',
        'clock'                    => 'Clock',
        'load_dots'                => 'load...',
        'never'                    => 'Never',

        // Fleet slot info
        'tooltip_slots'            => 'Used/Total fleet slots',
        'no_free_slots'            => 'No fleet slots available',
        'tooltip_exp_slots'        => 'Used/Total expedition slots',
        'market_slots'             => 'Offers',
        'tooltip_market_slots'     => 'Used/Total trading fleets',

        // Warning / impossible states
        'fleet_dispatch'           => 'Fleet dispatch',
        'dispatch_impossible'      => 'Fleet dispatch impossible',
        'no_ships'                 => 'There are no ships on this planet.',
        'in_combat'                => 'The fleet is currently in combat.',
        'vacation_error'           => 'No fleets can be sent from vacation mode!',
        'not_enough_deuterium'     => 'Not enough deuterium!',
        'no_target'                => 'You have to select a valid target.',
        'cannot_send_to_target'    => 'Fleets can not be sent to this target.',
        'cannot_start_mission'     => 'You cannot start this mission.',

        // Status bar labels (no trailing colon — add : in template where needed)
        'mission_label'            => 'Mission',
        'target_label'             => 'Target',
        'player_name_label'        => "Player's Name",
        'no_selection'             => 'Nothing has been selected',
        'no_mission_selected'      => 'No mission selected!',

        // Step 1 – ship selection
        'combat_ships'             => 'Combat ships',
        'civil_ships'              => 'Civil ships',
        'standard_fleets'          => 'Standard fleets',
        'edit_standard_fleets'     => 'Edit standard fleets',
        'select_all_ships'         => 'Select all ships',
        'reset_choice'             => 'Reset choice',
        'api_data'                 => 'This data can be entered into a compatible combat simulator:',
        'tactical_retreat'         => 'Tactical retreat',
        'tactical_retreat_tooltip' => 'Show Deuterium usage per tactical retreat',
        'continue'                 => 'Continue',
        'back'                     => 'Back',

        // Step 2 – destination
        'origin'                   => 'Origin',
        'destination'              => 'Destination',
        'planet'                   => 'Planet',
        'moon'                     => 'Moon',
        'coordinates'              => 'Coordinates',
        'distance'                 => 'Distance',
        'debris_field'             => 'Debris field',
        'debris_field_lower'       => 'debris field',
        'shortcuts'                => 'Shortcuts',
        'combat_forces'            => 'Combat forces',
        'player_label'             => 'Player',
        'player_name'              => "Player's Name",

        // Step 3 – mission selection
        'select_mission'           => 'Select mission for target',
        'bashing_disabled'         => 'Attack missions have been deactivated as a result of too many attacks on the target.',

        // Mission names
        'mission_expedition'       => 'Expedition',
        'mission_colonise'         => 'Colonisation',
        'mission_recycle'          => 'Recycle Debris Field',
        'mission_transport'        => 'Transport',
        'mission_deploy'           => 'Deployment',
        'mission_espionage'        => 'Espionage',
        'mission_acs_defend'       => 'ACS Defend',
        'mission_attack'           => 'Attack',
        'mission_acs_attack'       => 'ACS Attack',
        'mission_destroy_moon'     => 'Moon Destruction',

        // Mission descriptions
        'desc_attack'              => 'Attacks the fleet and defense of your opponent.',
        'desc_acs_attack'          => 'Honourable battles can become dishonourable battles if strong players enter through ACS. The attacker\'s sum of total military points in comparison to the defender\'s sum of total military points is the decisive factor here.',
        'desc_transport'           => 'Transports your resources to other planets.',
        'desc_deploy'              => 'Sends your fleet permanently to another planet of your empire.',
        'desc_acs_defend'          => 'Defend the planet of your team-mate.',
        'desc_espionage'           => 'Spy the worlds of foreign emperors.',
        'desc_colonise'            => 'Colonizes a new planet.',
        'desc_recycle'             => 'Send your recyclers to a debris field to collect the resources floating around there.',
        'desc_destroy_moon'        => 'Destroys the moon of your enemy.',
        'desc_expedition'          => 'Send your ships to the furthest reaches of space to complete exciting quests.',

        // ACS Attack – federation overlay
        'fleet_union'              => 'Fleet union',
        'union_created'            => 'Fleet union created successfully.',
        'union_edited'             => 'Fleet union successfully edited.',
        'err_union_max_fleets'     => 'A maximum of 16 fleets can attack.',
        'err_union_max_players'    => 'A maximum of 5 players can attack.',
        'err_union_too_slow'        => 'You are too slow to join this fleet.',
        'err_union_target_mismatch' => 'Your fleet must target the same location as the fleet union.',
        'union_name'               => 'Union name',
        'buddy_list'               => 'Buddy list',
        'buddy_list_loading'       => 'Loading...',
        'buddy_list_empty'         => 'No buddies available',
        'buddy_list_error'         => 'Failed to load buddies',
        'search_user'              => 'Search user',
        'search'                   => 'Search',
        'union_user'               => 'Union user',
        'invite'                   => 'Invite',
        'kick'                     => 'Kick',
        'ok'                       => 'Ok',
        'own_fleet'                => 'Own fleet',

        // Briefing section (no trailing colons — add : in template where needed)
        'briefing'                 => 'Briefing',
        'load_resources'           => 'Load resources',
        'load_all_resources'       => 'Load all resources',
        'all_resources'            => 'all resources',
        'flight_duration'          => 'Duration of flight (one way)',
        'federation_duration'      => 'Flight Duration (fleet union)',
        'arrival'                  => 'Arrival',
        'return_trip'              => 'Return',
        'speed'                    => 'Speed:',
        'max_abbr'                 => 'max.',
        'hour_abbr'                => 'h',
        'deuterium_consumption'    => 'Deuterium consumption',
        'empty_cargobays'          => 'Empty cargobays',
        'hold_time'                => 'Hold time',
        'expedition_duration'      => 'Duration of expedition',
        'cargo_bay'                => 'cargo bay',
        'cargo_space'              => 'Available space / Max. cargo space',
        'send_fleet'               => 'Send fleet',
        'retreat_on_defender'      => 'Return upon retreat by defenders',
        'retreat_tooltip'          => 'If this option is activated, your fleet will also withdraw without a fight if your opponent flees.',
        'plunder_food'             => 'Plunder food',

        // Resources labels (for loca object)
        'metal'                    => 'Metal',
        'crystal'                  => 'Crystal',
        'deuterium'                => 'Deuterium',

        // Movement page
        'fleet_details'            => 'Fleet details',
        'ships'                    => 'Ships',
        'shipment'                 => 'Shipment',
        'recall'                   => 'Recall',
        'start_time'               => 'Start time',
        'time_of_arrival'          => 'Time of arrival',
        'deep_space'               => 'Deep space',

        // Target / player status indicators
        'uninhabited_planet'       => 'Uninhabited planet',
        'no_debris_field'          => 'No debris field',
        'player_vacation'          => 'Player in vacation mode',
        'admin_gm'                 => 'Admin or GM',
        'noob_protection'          => 'Noob protection',
        'player_too_strong'        => 'This planet can not be attacked as the player is too strong!',
        'no_moon'                  => 'No moon available.',
        'no_recycler'              => 'No recycler available.',
        'no_events'                => 'There are currently no events running.',
        'planet_already_reserved'  => 'This planet has already been reserved for a relocation.',
        'max_planet_warning'       => 'Attention! No further planets may be colonised at the moment. Two levels of astrotechnology research are necessary for each new colony. Do you still want to send your fleet?',

        // Galaxy / network
        'empty_systems'            => 'Empty Systems',
        'inactive_systems'         => 'Inactive Systems',
        'network_on'               => 'On',
        'network_off'              => 'Off',

        // Error codes (used in errorCodeMap)
        'err_generic'              => 'An error has occurred',
        'err_no_moon'              => 'Error, there is no moon',
        'err_newbie_protection'    => "Error, player can't be approached because of newbie protection",
        'err_too_strong'           => 'Player is too strong to be attacked',
        'err_vacation_mode'        => 'Error, player is in vacation mode',
        'err_own_vacation'         => 'No fleets can be sent from vacation mode!',
        'err_not_enough_ships'     => 'Error, not enough ships available, send maximum number:',
        'err_no_ships'             => 'Error, no ships available',
        'err_no_slots'             => 'Error, no free fleet slots available',
        'err_no_deuterium'         => "Error, you don't have enough deuterium",
        'err_no_planet'            => 'Error, there is no planet there',
        'err_no_cargo'             => 'Error, not enough cargo capacity',
        'err_multi_alarm'          => 'Multi-alarm',
        'err_attack_ban'           => 'Attack ban',
    ],

    // -------------------------------------------------------------------------
    // Galaxy page
    // -------------------------------------------------------------------------

    'galaxy' => [
        // Vacation mode
        'vacation_error'               => 'You cannot use the galaxy view whilst in vacation mode!',

        // Navigation / header
        'system'                       => 'System',
        'go'                           => 'Go!',

        // System action buttons
        'system_phalanx'               => 'System Phalanx',
        'system_espionage'             => 'System Espionage',
        'discoveries'                  => 'Discoveries',
        'discoveries_tooltip'          => 'Launch a discovery mission to all possible locations',

        // Header stats row labels
        'probes_short'                 => 'Esp.Probe',
        'recycler_short'               => 'Recy.',
        'ipm_short'                    => 'IPM.',
        'used_slots'                   => 'Used slots',

        // Table header columns
        'planet_col'                   => 'Planet',
        'name_col'                     => 'Name',
        'moon_col'                     => 'Moon',
        'debris_short'                 => 'DF',
        'player_status'                => 'Player (Status)',
        'alliance'                     => 'Alliance',
        'action'                       => 'Action',

        // Expedition / deep space row
        'planets_colonized'            => 'Planets colonized',
        'expedition_fleet'             => 'Expedition Fleet',
        'admiral_needed'               => 'You need an Admiral to use this feature.',
        'send'                         => 'send',

        // Legend tooltip
        'legend'                       => 'Legend',
        'status_admin_abbr'            => 'A',
        'legend_admin'                 => 'Administrator',
        'status_strong_abbr'           => 's',
        'legend_strong'                => 'stronger player',
        'status_noob_abbr'             => 'n',
        'legend_noob'                  => 'weaker player (newbie)',
        'status_outlaw_abbr'           => 'o',
        'legend_outlaw'                => 'Outlaw (temporary)',
        'status_vacation_abbr'         => 'v',
        'vacation_mode'                => 'Vacation Mode',
        'status_banned_abbr'           => 'b',
        'legend_banned'                => 'banned',
        'status_inactive_abbr'         => 'i',
        'legend_inactive_7'            => '7 days inactive',
        'status_longinactive_abbr'     => 'I',
        'legend_inactive_28'           => '28 days inactive',
        'status_honorable_abbr'        => 'hp',
        'legend_honorable'             => 'Honorable target',

        // loca JS object (unique galaxy strings)
        'phalanx_restricted'           => 'The system phalanx can only be used by the alliance class Researcher!',
        'astro_required'               => 'You have to research Astrophysics first.',
        'galaxy_nav'                   => 'Galaxy',
        'activity'                     => 'Activity',
        'no_action'                    => 'No actions available.',
        'time_minute_abbr'             => 'm',
        'moon_diameter_km'             => 'Diameter of moon in km',
        'km'                           => 'km',
        'pathfinders_needed'           => 'Pathfinders needed',
        'recyclers_needed'             => 'Recyclers needed',
        'mine_debris'                  => 'Mine',
        'phalanx_no_deut'              => 'Not enough deuterium to deploy phalanx.',
        'use_phalanx'                  => 'Use phalanx',
        'colonize_error'               => 'It is not possible to colonize a planet without a colony ship.',
        'ranking'                      => 'Ranking',
        'espionage_report'             => 'Espionage report',
        'missile_attack'               => 'Missile Attack',
        'rank'                         => 'Rank',
        'alliance_member'              => 'Member',
        'alliance_class'               => 'Alliance Class',
        'espionage_not_possible'       => 'Espionage not possible',
        'espionage'                    => 'Espionage',
        'hire_admiral'                 => 'Hire admiral',
        'dark_matter'                  => 'Dark Matter',
        'outlaw_explanation'           => 'If you are an outlaw, you no longer have any attack protection and can be attacked by all players.',
        'honorable_target_explanation' => 'In battle against this target you can receive honour points and plunder 50% more loot.',

        // galaxyLoca JS object
        'relocate_success'             => 'The position has been reserved for you. The colony\'s relocation has begun.',
        'relocate_title'               => 'Resettle Planet',
        'relocate_question'            => 'Are you sure you want to relocate your planet to these coordinates? To finance the relocation you\'ll need :cost Dark Matter.',
        'deut_needed_relocate'         => 'You don\'t have enough Deuterium! You need 10 Units of Deuterium.',
        'fleet_attacking'              => 'Fleet is attacking!',
        'fleet_underway'               => 'Fleet is en-route',
        'discovery_send'               => 'Dispatch exploration ship',
        'discovery_success'            => 'Exploration ship dispatched',
        'discovery_unavailable'        => 'You can\'t dispatch an exploration ship to this location.',
        'discovery_underway'           => 'An Exploration Ship is already on approach to this planet.',
        'discovery_locked'             => 'You haven\'t unlocked the research to discover new lifeforms yet.',
        'discovery_title'              => 'Exploration Ship',
        'discovery_question'           => 'Do you want to dispatch an exploration ship to this planet?<br/>Metal: 5000 Crystal: 1000 Deuterium: 500',

        // Phalanx result dialog (JS strings inside Blade-rendered script block)
        'sensor_report'                => 'sensor report',
        'refresh'                      => 'Refresh',
        'arrived'                      => 'Arrived',

        // Missile attack dialog
        'target'                       => 'Target',
        'flight_duration'              => 'Flight duration',
        'ipm_full'                     => 'Interplanetary Missiles',
        'primary_target'               => 'Primary target',
        'no_primary_target'            => 'No primary target selected: random target',
        'target_has'                   => 'Target has',
        'abm_full'                     => 'Anti-Ballistic Missiles',
        'fire'                         => 'Fire',
        'valid_missile_count'          => 'Please enter a valid number of missiles',
        'not_enough_missiles'          => 'You do not have enough missiles',
        'launched_success'             => 'Missiles launched successfully!',
        'launch_failed'                => 'Failed to launch missiles',
    ],

    // -------------------------------------------------------------------------
    // Buddy system (buddy requests + player ignore — used in galaxy page)
    // -------------------------------------------------------------------------

    'buddy' => [
        'request_sent'   => 'Buddy request sent successfully!',
        'request_failed' => 'Failed to send buddy request.',
        'request_to'     => 'Buddy request to',
        'ignore_confirm' => 'Are you sure you want to ignore',
        'ignore_success' => 'Player ignored successfully!',
        'ignore_failed'  => 'Failed to ignore player.',
    ],

    // -------------------------------------------------------------------------
    // Messages page
    // -------------------------------------------------------------------------

    'messages' => [
        // Main tabs
        'tab_fleets'        => 'Fleets',
        'tab_communication' => 'Communication',
        'tab_economy'       => 'Economy',
        'tab_universe'      => 'Universe',
        'tab_system'        => 'OGame',
        'tab_favourites'    => 'Favourites',

        // Fleet subtabs
        'subtab_espionage'   => 'Espionage',
        'subtab_combat'      => 'Combat Reports',
        'subtab_expeditions' => 'Expeditions',
        'subtab_transport'   => 'Unions/Transport',
        'subtab_other'       => 'Other',

        // Communication subtabs
        'subtab_messages'         => 'Messages',
        'subtab_information'      => 'Information',
        'subtab_shared_combat'    => 'Shared Combat Reports',
        'subtab_shared_espionage' => 'Shared Espionage Reports',

        // General UI
        'news_feed'          => 'News feed',
        'loading'            => 'load...',
        'error_occurred'     => 'An error has occurred',
        'mark_favourite'     => 'mark as favourite',
        'remove_favourite'   => 'remove from favourites',
        'from'               => 'From',
        'no_messages'        => 'There are currently no messages available in this tab',
        'new_alliance_msg'   => 'New alliance message',
        'to'                 => 'To',
        'all_players'        => 'all players',
        'send'               => 'send',
        'delete_buddy_title' => 'Delete buddy',
        'report_to_operator' => 'Report this message to a game operator?',
        'too_few_chars'      => 'Too few characters! Please put in at least 2 characters.',

        // BBCode editor (localizedBBCode)
        'bbcode_bold'           => 'Bold',
        'bbcode_italic'         => 'Italic',
        'bbcode_underline'      => 'Underline',
        'bbcode_stroke'         => 'Strikethrough',
        'bbcode_sub'            => 'Subscript',
        'bbcode_sup'            => 'Superscript',
        'bbcode_font_color'     => 'Font colour',
        'bbcode_font_size'      => 'Font size',
        'bbcode_bg_color'       => 'Background colour',
        'bbcode_bg_image'       => 'Background image',
        'bbcode_tooltip'        => 'Tool-tip',
        'bbcode_align_left'     => 'Left align',
        'bbcode_align_center'   => 'Centre align',
        'bbcode_align_right'    => 'Right align',
        'bbcode_align_justify'  => 'Justify',
        'bbcode_block'          => 'Break',
        'bbcode_code'           => 'Code',
        'bbcode_spoiler'        => 'Spoiler',
        'bbcode_moreopts'       => 'More Options',
        'bbcode_list'           => 'List',
        'bbcode_hr'             => 'Horizontal line',
        'bbcode_picture'        => 'Image',
        'bbcode_link'           => 'Link',
        'bbcode_email'          => 'Email',
        'bbcode_player'         => 'Player',
        'bbcode_item'           => 'Item',
        'bbcode_coordinates'    => 'Coordinates',
        'bbcode_preview'        => 'Preview',
        'bbcode_text_ph'        => 'Text...',
        'bbcode_player_ph'      => 'Player ID or name',
        'bbcode_item_ph'        => 'Item ID',
        'bbcode_coord_ph'       => 'Galaxy:system:position',
        'bbcode_chars_left'     => 'Characters remaining',
        'bbcode_ok'             => 'Ok',
        'bbcode_cancel'         => 'Cancel',
        'bbcode_repeat_x'       => 'Repeat horizontally',
        'bbcode_repeat_y'       => 'Repeat vertically',

        // Espionage report
        'spy_player'          => 'Player',
        'spy_activity'        => 'Activity',
        'spy_minutes_ago'     => 'minutes ago',
        'spy_class'           => 'Class',
        'spy_unknown'         => 'Unknown',
        'spy_alliance_class'  => 'Alliance Class',
        'spy_no_alliance_class' => 'No alliance class selected',
        'spy_resources'       => 'Resources',
        'spy_loot'            => 'Loot',
        'spy_counter_esp'     => 'Chance of counter-espionage',
        'spy_no_info'         => 'We were unable to retrieve any reliable information of this type from the scan.',
        'spy_debris_field'    => 'debris field',
        'spy_no_activity'     => 'Your espionage does not show abnormalities in the atmosphere of the planet. There appears to have been no activity on the planet within the last hour.',
        'spy_fleets'          => 'Fleets',
        'spy_defense'         => 'Defense',
        'spy_research'        => 'Research',
        'spy_building'        => 'Building',

        // Battle report (brief)
        'battle_attacker'    => 'Attacker',
        'battle_defender'    => 'Defender',
        'battle_resources'   => 'Resources',
        'battle_loot'        => 'Loot',
        'battle_debris_new'  => 'Debris field (newly created)',
        'battle_repaired'    => 'Actually repaired',
        'battle_moon_chance' => 'Moon Chance',

        // Battle report (full)
        'battle_report'          => 'Combat Report',
        'battle_planet'          => 'Planet',
        'battle_fleet_command'   => 'Fleet Command',
        'battle_from'            => 'From',
        'battle_tactical_retreat' => 'Tactical retreat',
        'battle_total_loot'      => 'Total loot',
        'battle_debris'          => 'Debris (new)',
        'battle_recycler'        => 'Recycler',
        'battle_mined_after'     => 'Mined after combat',
        'battle_reaper'          => 'Reaper',
        'battle_debris_left'     => 'Debris fields (left)',
        'battle_honour_points'   => 'Honour points',
        'battle_dishonourable'   => 'Dishonourable fight',
        'battle_vs'              => 'vs',
        'battle_honourable'      => 'Honourable fight',
        'battle_class'           => 'Class',
        'battle_weapons'         => 'Weapons',
        'battle_shields'         => 'Shields',
        'battle_armour'          => 'Armour',
        'battle_combat_ships'    => 'Combat ships',
        'battle_civil_ships'     => 'Civil ships',
        'battle_defences'        => 'Defences',
        'battle_repaired_def'    => 'Repaired defences',
        'battle_share'           => 'share message',
        'battle_attack'          => 'Attack',
        'battle_espionage'       => 'Espionage',
        'battle_delete'          => 'delete',
        'battle_favourite'       => 'mark as favourite',
        'battle_hamill'          => 'A Light Fighter destroyed one Deathstar before the battle began!',
        'battle_retreat_tooltip'  => 'Please note that Deathstars, Espionage Probes, Solar Satellites and any fleet on a ACS Defence mission cannot flee. Tactical retreats are also deactivated in honourable battles. A retreat may also have been manually deactivated or prevented by a lack of deuterium. Bandits and players with more than 500,000 points never retreat.',
        'battle_no_flee'         => 'The defending fleet did not flee.',
        'battle_rounds'          => 'Rounds',
        'battle_start'           => 'Start',
        'battle_player_from'     => 'from',
        'battle_attacker_fires'  => 'The :attacker fires a total of :hits shots at the :defender with a total strength of :strength. The :defender2\'s shields absorb :absorbed points of damage.',
        'battle_defender_fires'  => 'The :defender fires a total of :hits shots at the :attacker with a total strength of :strength. The :attacker2\'s shields absorb :absorbed points of damage.',
    ],

    // -------------------------------------------------------------------------
    // Alliance page
    // -------------------------------------------------------------------------

    'alliance' => [
        // Page / navigation
        'page_title'                    => 'Alliance',
        'tab_overview'                  => 'Overview',
        'tab_management'                => 'Management',
        'tab_communication'             => 'Communication',
        'tab_applications'              => 'Applications',
        'tab_classes'                   => 'Alliance Classes',
        'tab_create'                    => 'Create alliance',
        'tab_search'                    => 'Search alliance',
        'tab_apply'                     => 'apply',

        // Overview – alliance info table
        'your_alliance'                 => 'Your alliance',
        'name'                          => 'Name',
        'tag'                           => 'Tag',
        'created'                       => 'Created',
        'member'                        => 'Member',
        'your_rank'                     => 'Your Rank',
        'homepage'                      => 'Homepage',
        'logo'                          => 'Alliance logo',
        'open_page'                     => 'Open alliance page',
        'highscore'                     => 'Alliance highscore',
        'leave_wait_warning'            => 'If you leave the alliance, you will need to wait 3 days before joining or creating another alliance.',
        'leave_btn'                     => 'Leave alliance',

        // Overview – member list
        'member_list'                   => 'Member List',
        'no_members'                    => 'No members found',
        'assign_rank_btn'               => 'Assign rank',
        'kick_tooltip'                  => 'Kick alliance member',
        'write_msg_tooltip'             => 'Write message',
        'col_name'                      => 'Name',
        'col_rank'                      => 'Rank',
        'col_coords'                    => 'Coords',
        'col_joined'                    => 'Joined',
        'col_online'                    => 'Online',
        'col_function'                  => 'Function',

        // Overview – text sections
        'internal_area'                 => 'Internal Area',
        'external_area'                 => 'External Area',

        // Management – privileges
        'configure_privileges'          => 'Configure privileges',
        'col_rank_name'                 => 'Rank name',
        'col_applications_group'        => 'Applications',
        'col_member_group'              => 'Member',
        'col_alliance_group'            => 'Alliance',
        'delete_rank'                   => 'Delete rank',
        'save_btn'                      => 'Save',
        'rights_warning_html'           => '<strong>Warning!</strong> You can only give permissions that you have yourself.',
        'rights_warning_loca'           => '[b]Warning![/b] You can only give permissions that you have yourself.',
        'rights_legend'                 => 'Rights legend',
        'create_rank_btn'               => 'Create new rank',
        'rank_name_placeholder'         => 'Rank name',
        'no_ranks'                      => 'No ranks found',

        // Management – permissions (icon titles and legend)
        'perm_see_applications'         => 'Show applications',
        'perm_edit_applications'        => 'Process applications',
        'perm_see_members'              => 'Show member list',
        'perm_kick_user'                => 'Kick user',
        'perm_see_online'               => 'See online status',
        'perm_send_circular'            => 'Write circular message',
        'perm_disband'                  => 'Disband alliance',
        'perm_manage'                   => 'Manage alliance',
        'perm_right_hand'               => 'Right hand',
        'perm_right_hand_long'          => '`Right Hand` (necessary to transfer founder rank)',
        'perm_manage_classes'           => 'Manage alliance class',

        // Management – texts section
        'manage_texts'                  => 'Manage texts',
        'internal_text'                 => 'Internal text',
        'external_text'                 => 'External text',
        'application_text'              => 'Application text',

        // Management – options/settings
        'options'                       => 'Options',
        'alliance_logo_label'           => 'Alliance logo',
        'applications_field'            => 'Applications',
        'status_open'                   => 'Possible (alliance open)',
        'status_closed'                 => 'Impossible (alliance closed)',
        'rename_founder'                => 'Rename founder title as',
        'rename_newcomer'               => 'Rename Newcomer rank',
        'no_settings_perm'              => 'You do not have permission to manage alliance settings.',

        // Management – change tag/name
        'change_tag_name'               => 'Change alliance tag/name',
        'change_tag'                    => 'Change alliance tag',
        'change_name'                   => 'Change alliance name',
        'former_tag'                    => 'Former alliance tag:',
        'new_tag'                       => 'New alliance tag:',
        'former_name'                   => 'Former alliance name:',
        'new_name'                      => 'New alliance name:',
        'former_tag_short'              => 'Former alliance tag',
        'new_tag_short'                 => 'New alliance tag',
        'former_name_short'             => 'Former alliance name',
        'new_name_short'                => 'New alliance name',
        'no_tagname_perm'               => 'You do not have permission to change alliance tag/name.',

        // Management – disband / pass on
        'delete_pass_on'                => 'Delete alliance/Pass alliance on',
        'delete_btn'                    => 'Delete this alliance',
        'no_delete_perm'                => 'You do not have permission to delete the alliance.',
        'handover'                      => 'Handover alliance',
        'takeover_btn'                  => 'Take over alliance',
        'loca_continue'                 => 'Continue',
        'loca_change_founder'           => 'Transfer the founder title to:',
        'loca_no_transfer_error'        => 'None of the members have the required `right hand` right. You cannot hand over the alliance.',
        'loca_founder_inactive_error'   => 'The founder is not inactive long enough in order to take over the alliance.',

        // Management – leave alliance section (non-founders)
        'leave_section_title'           => 'Leave alliance',
        'leave_consequences'            => 'If you leave the alliance, you will lose all your rank permissions and alliance benefits.',

        // Applications tab
        'no_applications'               => 'No applications found',
        'accept_btn'                    => 'accept',
        'deny_btn'                      => 'Deny applicant',
        'report_btn'                    => 'Report application',
        'app_date'                      => 'Application date',
        'action_col'                    => 'Action',
        'answer_btn'                    => 'answer',
        'reason_label'                  => 'Reason',

        // Apply page
        'apply_title'                   => 'Apply to Alliance',
        'apply_heading'                 => 'Application to',
        'send_application_btn'          => 'Send application',
        'chars_remaining'               => 'Characters remaining',
        'msg_too_long'                  => 'Message is too long (max 2000 characters)',

        // Broadcast
        'addressee'                     => 'To',
        'all_players'                   => 'all players',
        'only_rank'                     => 'only rank:',
        'send_btn'                      => 'Send',

        // Info popup
        'info_title'                    => 'Alliance Information',
        'apply_confirm'                 => 'Do you want to apply to this alliance?',
        'redirect_confirm'              => 'By following this link, you will leave OGame. Do you wish to continue?',

        // Classes tab
        'class_selection_header'        => 'Class Selection',
        'select_class_title'            => 'Select alliance class',
        'select_class_note'             => 'Select an alliance class to receive special bonuses. You can change the alliance class in the alliance menu, provided you have the requisite permissions.',
        'class_warriors'                => 'Warriors (Alliance)',
        'class_traders'                 => 'Traders (Alliance)',
        'class_researchers'             => 'Researchers (Alliance)',
        'class_label'                   => 'Alliance Class',
        'buy_for'                       => 'Buy for',
        'no_dark_matter'                => 'There is not enough dark matter available',
        'loca_deactivate'               => 'Deactivate',
        'loca_activate_dm'              => 'Do you want to activate the alliance class #allianceClassName# for #darkmatter# Dark Matter? In doing so, you will lose your current alliance class.',
        'loca_activate_item'            => 'Do you want to activate the alliance class #allianceClassName#? In doing so, you will lose your current alliance class.',
        'loca_deactivate_note'          => 'Do you really want to deactivate the alliance class #allianceClassName#? Reactivation requires an alliance class change item for 500,000 Dark Matter.',
        'loca_class_change_append'      => '<br><br>Current alliance class: #currentAllianceClassName#<br><br>Last changed on: #lastAllianceClassChange#',
        'loca_no_dm'                    => 'Not enough Dark Matter available! Do you want to buy some now?',
        'loca_reference'                => 'Reference',
        'loca_language'                 => 'Language:',
        'loca_loading'                  => 'load...',
        'warrior_bonus_1'               => '+10% speed for ships flying between alliance members',
        'warrior_bonus_2'               => '+1 combat research levels',
        'warrior_bonus_3'               => '+1 espionage research levels',
        'warrior_bonus_4'               => 'The espionage system can be used to scan whole systems.',
        'trader_bonus_1'                => '+10% speed for transporters',
        'trader_bonus_2'                => '+5% mine production',
        'trader_bonus_3'                => '+5% energy production',
        'trader_bonus_4'                => '+10% planet storage capacity',
        'trader_bonus_5'                => '+10% moon storage capacity',
        'researcher_bonus_1'            => '+5% larger planets on colonisation',
        'researcher_bonus_2'            => '+10% speed to expedition destination',
        'researcher_bonus_3'            => 'The system phalanx can be used to scan fleet movements in whole systems.',
        'class_not_implemented'         => 'Alliance class system not yet implemented',

        // Create alliance form
        'create_tag_label'              => 'Alliance Tag (3-8 characters)',
        'create_name_label'             => 'Alliance name (3-30 characters)',
        'create_btn'                    => 'Create alliance',
        'loca_ally_tag_chars'           => 'Alliance-Tag (3-30 characters)',
        'loca_ally_name_chars'          => 'Alliance-Name (3-8 characters)',
        'loca_ally_name_label'          => 'Alliance name (3-30 characters)',
        'loca_ally_tag_label'           => 'Alliance Tag (3-8 characters)',
        'validation_min_chars'          => 'Not enough characters',
        'validation_special'            => 'Contains invalid characters.',
        'validation_underscore'         => 'Your name may not start or end with an underscore.',
        'validation_hyphen'             => 'Your name may not start or finish with a hyphen.',
        'validation_space'              => 'Your name may not start or end with a space.',
        'validation_max_underscores'    => 'Your name may not contain more than 3 underscores in total.',
        'validation_max_hyphens'        => 'Your name may not contain more than 3 hyphens.',
        'validation_max_spaces'         => 'Your name may not include more than 3 spaces in total.',
        'validation_consec_underscores' => 'You may not use two or more underscores one after the other.',
        'validation_consec_hyphens'     => 'You may not use two or more hyphens consecutively.',
        'validation_consec_spaces'      => 'You may not use two or more spaces one after the other.',

        // JS confirm dialogs
        'confirm_leave'                 => 'Are you sure you want to leave the alliance?',
        'confirm_kick'                  => 'Are you sure you want to kick :username from the alliance?',
        'confirm_deny'                  => 'Are you sure you want to deny this application?',
        'confirm_deny_title'            => 'Deny application',
        'confirm_disband'               => 'Really delete alliance?',
        'confirm_pass_on'               => 'Are you sure you want to pass on your alliance?',
        'confirm_takeover'              => 'Are you sure that you want to take over this alliance?',
        'confirm_abandon'               => 'Abandon this alliance?',
        'confirm_takeover_long'         => 'Take over this alliance?',

        // Controller / AJAX success & error messages
        'msg_already_in'                => 'You are already in an alliance',
        'msg_not_in_alliance'           => 'You are not in an alliance',
        'msg_not_found'                 => 'Alliance not found',
        'msg_id_required'               => 'Alliance ID is required',
        'msg_closed'                    => 'This alliance is closed for applications',
        'msg_created'                   => 'Alliance created successfully',
        'msg_applied'                   => 'Application submitted successfully',
        'msg_accepted'                  => 'Application accepted',
        'msg_rejected'                  => 'Application rejected',
        'msg_kicked'                    => 'Member kicked from alliance',
        'msg_kicked_success'            => 'Member kicked successfully',
        'msg_left'                      => 'You have left the alliance',
        'msg_rank_assigned'             => 'Rank assigned',
        'msg_rank_assigned_to'          => 'Rank assigned successfully to :name',
        'msg_ranks_assigned'            => 'Ranks assigned successfully',
        'msg_rank_perms_updated'        => 'Rank permissions updated',
        'msg_texts_updated'             => 'Alliance texts updated',
        'msg_text_updated'              => 'Alliance text updated',
        'msg_settings_updated'          => 'Alliance settings updated',
        'msg_tag_updated'               => 'Alliance tag updated',
        'msg_name_updated'              => 'Alliance name updated',
        'msg_tag_name_updated'          => 'Alliance tag and name updated',
        'msg_disbanded'                 => 'Alliance disbanded',
        'msg_broadcast_sent'            => 'Broadcast message sent successfully',
        'msg_rank_created'              => 'Rank created successfully',
        'msg_apply_success'             => 'Application submitted successfully',
        'msg_apply_error'               => 'Failed to submit application',
        'msg_leave_error'               => 'Failed to leave alliance',
        'msg_assign_error'              => 'Failed to assign ranks',
        'msg_kick_error'                => 'Failed to kick member',
        'msg_invalid_action'            => 'Invalid action',
        'msg_error'                     => 'An error occurred',
    ],

    // -------------------------------------------------------------------
    // Techtree module
    // -------------------------------------------------------------------
    'techtree' => [
        // Navigation tabs
        'tab_techtree'                          => 'Techtree',
        'tab_applications'                      => 'Applications',
        'tab_techinfo'                          => 'Techinfo',
        'tab_technology'                        => 'Technology',

        // Common
        'page_title'                            => 'Technology',
        'no_requirements'                       => 'No requirements available',
        'is_requirement_for'                    => 'is a requirement for',
        'level'                                 => 'Level',

        // Shared table columns
        'col_level'                             => 'Level',
        'col_difference'                        => 'Difference',
        'col_diff_per_level'                    => 'Difference/Level',
        'col_protected'                         => 'Protected',
        'col_protected_percent'                 => 'Protected (Percent)',

        // Production table
        'production_energy_balance'             => 'Energy Balance',
        'production_per_hour'                   => 'Production/h',
        'production_deuterium_consumption'      => 'Deuterium consumption',

        // Properties table (ships/defense)
        'properties_technical_data'             => 'Technical data',
        'properties_structural_integrity'       => 'Structural Integrity',
        'properties_shield_strength'            => 'Shield Strength',
        'properties_attack_strength'            => 'Attack Strength',
        'properties_speed'                      => 'Speed',
        'properties_cargo_capacity'             => 'Cargo Capacity',
        'properties_fuel_usage'                 => 'Fuel usage (Deuterium)',

        // Property tooltip
        'tooltip_basic_value'                   => 'Basic value',

        // Rapidfire
        'rapidfire_from'                        => 'Rapidfire from',
        'rapidfire_against'                     => 'Rapidfire against',

        // Storage table
        'storage_capacity'                      => 'Storage cap.',

        // Plasma table
        'plasma_metal_bonus'                    => 'Metal bonus %',
        'plasma_crystal_bonus'                  => 'Crystal bonus %',
        'plasma_deuterium_bonus'                => 'Deuterium bonus %',

        // Astrophysics table
        'astrophysics_max_colonies'             => 'Maximum colonies',
        'astrophysics_max_expeditions'          => 'Maximum expeditions',
        'astrophysics_note_1'                   => 'Positions 3 and 13 can be populated from level 4 onwards.',
        'astrophysics_note_2'                   => 'Positions 2 and 14 can be populated from level 6 onwards.',
        'astrophysics_note_3'                   => 'Positions 1 and 15 can be populated from level 8 onwards.',
    ],

    // -------------------------------------------------------------------
    // Options (user settings) module
    // -------------------------------------------------------------------
    'options' => [
        // Page title
        'page_title'                                => 'Options',

        // Tabs
        'tab_userdata'                              => 'User data',
        'tab_general'                               => 'General',
        'tab_display'                               => 'Display',
        'tab_extended'                              => 'Extended',

        // Tab 1 – Player name
        'section_playername'                        => 'Players Name',
        'your_player_name'                          => 'Your player name:',
        'new_player_name'                           => 'New player name:',
        'username_change_once_week'                 => 'You can change your username once per week.',
        'username_change_hint'                      => 'To do so, click on your name or the settings at the top of the screen.',

        // Tab 1 – Password
        'section_password'                          => 'Change password',
        'old_password'                              => 'Enter old password:',
        'new_password'                              => 'New password (at least 4 characters):',
        'repeat_password'                           => 'Repeat the new password:',
        'password_check'                            => 'Password check:',
        'password_strength_low'                     => 'Low',
        'password_strength_medium'                  => 'Medium',
        'password_strength_high'                    => 'High',
        'password_properties_title'                 => 'The password should contain the following properties',
        'password_min_max'                          => 'min. 4 characters, max. 20 characters',
        'password_mixed_case'                       => 'Upper and lower case',
        'password_special_chars'                    => 'Special characters (e.g. !?:_., )',
        'password_numbers'                          => 'Numbers',
        'password_length_hint'                      => 'Your password needs to have at least <strong>4 characters</strong> and may not be longer than <strong>20 characters</strong>.',

        // Tab 1 – Email
        'section_email'                             => 'Email address',
        'current_email'                             => 'Current email address:',
        'send_validation_link'                      => 'Send validation link',
        'email_sent_success'                        => 'Email has been sent successfully!',
        'email_sent_error'                          => 'Error! Account is already validated or the email could not be sent!',
        'email_too_many_requests'                   => "You've already requested too many emails!",
        'new_email'                                 => 'New email address:',
        'new_email_confirm'                         => 'New email address (to confirmation):',
        'enter_password_confirm'                    => 'Enter password (as confirmation):',
        'email_warning'                             => 'Warning! After a successful account validation, a renewed change of email address is only possible after a period of <b>7 days</b>.',

        // Tab 2 – General
        'section_spy_probes'                        => 'Spy probes',
        'spy_probes_amount'                         => 'Number of espionage probes:',
        'section_chat'                              => 'Chat',
        'disable_chat_bar'                          => 'Deactivate chat bar:',
        'section_warnings'                          => 'Warnings',
        'disable_outlaw_warning'                    => 'Deactivate Outlaw-Warning on attacks on opponents 5-times stronger:',

        // Tab 3 – Display > General
        'section_general_display'                   => 'General',
        'show_mobile_version'                       => 'Show mobile version:',
        'show_alt_dropdowns'                        => 'Show alternative drop downs:',
        'activate_autofocus'                        => 'Activate autofocus in the highscores:',
        'always_show_events'                        => 'Always show events:',
        'events_hide'                               => 'Hide',
        'events_above'                              => 'Above the content',
        'events_below'                              => 'Below the content',

        // Tab 3 – Display > Planets
        'section_planets'                           => 'Your planets',
        'sort_planets_by'                           => 'Sort planets by:',
        'sort_emergence'                            => 'Order of emergence',
        'sort_coordinates'                          => 'Coordinates',
        'sort_alphabet'                             => 'Alphabet',
        'sort_size'                                 => 'Size',
        'sort_used_fields'                          => 'Used fields',
        'sort_sequence'                             => 'Sorting sequence:',
        'sort_order_up'                             => 'up',
        'sort_order_down'                           => 'down',

        // Tab 3 – Display > Overview
        'section_overview_display'                  => 'Overview',
        'highlight_planet_info'                     => 'Highlight planet information:',
        'animated_detail_display'                   => 'Animated detail display:',
        'animated_overview'                         => 'Animated overview:',

        // Tab 3 – Display > Overlays
        'section_overlays'                          => 'Overlays',
        'overlays_hint'                             => 'The following settings allow the corresponding overlays to open as an additional browser window instead of within the game.',
        'popup_notes'                               => 'Notes in an extra window:',
        'popup_combat_reports'                      => 'Combat reports in an extra window:',

        // Tab 3 – Display > Messages
        'section_messages_display'                  => 'Messages',
        'hide_report_pictures'                      => 'Hide pictures in reports:',
        'msgs_per_page'                             => 'Amount of displayed messages per page:',
        'auctioneer_notifications'                  => 'Auctioneer notification:',
        'economy_notifications'                     => 'Create economy messages:',

        // Tab 3 – Display > Galaxy
        'section_galaxy_display'                    => 'Galaxy',
        'detailed_activity'                         => 'Detailed activity display:',
        'preserve_galaxy_system'                    => 'Preserve galaxy/system with planet change:',

        // Tab 4 – Extended > Vacation Mode
        'section_vacation'                          => 'Vacation Mode',
        'vacation_active'                           => 'You are currently in vacation mode.',
        'vacation_can_deactivate_after'             => 'You can deactivate it after:',
        'vacation_cannot_activate'                  => 'Vacation mode can not be activated (Active fleets)',
        'vacation_description_1'                    => 'Vacation mode is designed to protect you during long absences from the game. You can only activate it when none of your fleets are in transit. Building and research orders will be put on hold.',
        'vacation_description_2'                    => 'Once vacation mode is activated, it will protect you from new attacks. Attacks that have already started will, however, continue and your production will be set to zero. Vacation mode does not prevent your account from being deleted if it has been inactive for 35+ days and the account has no purchased DM.',
        'vacation_description_3'                    => 'Vacation mode lasts a minimum of 48 hours. Only after this time expires will you be able to deactivate it.',
        'vacation_tooltip_min_days'                 => 'The vacation lasts a minimum of 2 days.',
        'vacation_deactivate_btn'                   => 'Deactivate',
        'vacation_activate_btn'                     => 'Activate',

        // Tab 4 – Extended > Account
        'section_account'                           => 'Your Account',
        'delete_account'                            => 'Delete account',
        'delete_account_hint'                       => 'Check here to have your account marked for automatic deletion after 7 days.',

        // Submit
        'use_settings'                              => 'Use settings',

        // JS validationEngine rules
        'validation_not_enough_chars'               => 'Not enough characters',
        'validation_pw_too_short'                   => 'The entered password is too short (min. 4 characters)',
        'validation_pw_too_long'                    => 'The entered password is too long (max. 20 characters)',
        'validation_invalid_email'                  => 'You need to enter a valid email address!',
        'validation_special_chars'                  => 'Contains invalid characters.',
        'validation_no_begin_end_underscore'        => 'Your name may not start or end with an underscore.',
        'validation_no_begin_end_hyphen'            => 'Your name may not start or finish with a hyphen.',
        'validation_no_begin_end_whitespace'        => 'Your name may not start or end with a space.',
        'validation_max_three_underscores'          => 'Your name may not contain more than 3 underscores in total.',
        'validation_max_three_hyphens'              => 'Your name may not contain more than 3 hyphens.',
        'validation_max_three_spaces'               => 'Your name may not include more than 3 spaces in total.',
        'validation_no_consecutive_underscores'     => 'You may not use two or more underscores one after the other.',
        'validation_no_consecutive_hyphens'         => 'You may not use two or more hyphens consecutively.',
        'validation_no_consecutive_spaces'          => 'You may not use two or more spaces one after the other.',

        // JS preferenceLoca object
        'js_change_name_title'                      => 'New player name',
        'js_change_name_question'                   => 'Are you sure you want to change your player name to %newName%?',
        'js_planet_move_question'                   => 'Caution! This mission may still be running once the relocation period starts and if this is the case, the process will be cancelled. Do you really want to continue with this job?',
        'js_tab_disabled'                           => 'To use this option you have to be validated and cannot be in vacation mode!',
        'js_vacation_question'                      => 'Do you want to activate vacation mode? You can only end your vacation after 2 days.',

        // Controller messages
        'msg_settings_saved'                        => 'Settings saved',
        'msg_vacation_activated'                    => 'Vacation mode has been activated. It will protect you from new attacks for a minimum of 48 hours.',
        'msg_vacation_deactivated'                  => 'Vacation mode has been deactivated.',
        'msg_vacation_min_duration'                 => 'You can only deactivate vacation mode after the minimum duration of 48 hours has passed.',
        'msg_vacation_fleets_in_transit'            => 'You cannot activate vacation mode while you have fleets in transit.',
        'msg_probes_min_one'                        => 'Espionage probes amount must be at least 1',
    ],

    // -------------------------------------------------------------------------
    // Layout (main.blade.php) — header, menu, resource bar, footer, JS loca
    // -------------------------------------------------------------------------
    'layout' => [
        // Header bar
        'player'                    => 'Player',
        'change_player_name'        => 'Change player name',
        'highscore'                 => 'Highscore',
        'notes'                     => 'Notes',
        'notes_overlay_title'       => 'My notes',
        'buddies'                   => 'Buddies',
        'search'                    => 'Search',
        'search_overlay_title'      => 'Search Universe',
        'options'                   => 'Options',
        'support'                   => 'Support',
        'log_out'                   => 'Log out',
        'unread_messages'           => 'unread message(s)',
        'loading'                   => 'load...',
        'no_fleet_movement'         => 'No fleet movement',
        'under_attack'              => 'You are under attack!',

        // Character class
        'class_none'                => 'No class selected',
        'class_selected'            => 'Your class: :name',
        'class_click_select'        => 'Click to select a character class',

        // Resource bar
        'res_available'             => 'Available',
        'res_storage_capacity'      => 'Storage capacity',
        'res_current_production'    => 'Current production',
        'res_den_capacity'          => 'Den Capacity',
        'res_consumption'           => 'Consumption',
        'res_purchase_dm'           => 'Purchase Dark Matter',
        'res_metal'                 => 'Metal',
        'res_crystal'               => 'Crystal',
        'res_deuterium'             => 'Deuterium',
        'res_energy'                => 'Energy',
        'res_dark_matter'           => 'Dark Matter',

        // Menu sidebar — item labels
        'menu_overview'             => 'Overview',
        'menu_resources'            => 'Resources',
        'menu_facilities'           => 'Facilities',
        'menu_merchant'             => 'Merchant',
        'menu_research'             => 'Research',
        'menu_shipyard'             => 'Shipyard',
        'menu_defense'              => 'Defense',
        'menu_fleet'                => 'Fleet',
        'menu_galaxy'               => 'Galaxy',
        'menu_alliance'             => 'Alliance',
        'menu_officers'             => 'Recruit Officers',
        'menu_shop'                 => 'Shop',
        'menu_directives'           => 'Directives',

        // Menu sidebar — icon tooltip titles
        'menu_rewards_title'        => 'Rewards',
        'menu_resource_settings_title' => 'Resource settings',
        'menu_jump_gate'            => 'Jump Gate',
        'menu_resource_market_title' => 'Resource Market',
        'menu_technology_title'     => 'Technology',
        'menu_fleet_movement_title' => 'Fleet movement',
        'menu_inventory_title'      => 'Inventory',

        // Planet sidebar
        'planets'                   => 'Planets',

        // Chat bar
        'contacts_online'           => ':count Contact(s) online',

        // Scroll button
        'back_to_top'               => 'Back to top',

        // Footer
        'all_rights_reserved'       => 'All rights reserved.',
        'patch_notes'               => 'Patch notes',
        'server_settings'           => 'Server Settings',
        'help'                      => 'Help',
        'rules'                     => 'Rules',
        'legal'                     => 'Legal',
        'board'                     => 'Board',

        // JS — jsloca
        'js_internal_error'         => "A previously unknown error has occurred. Unfortunately your last action couldn't be executed!",
        'js_notify_info'            => 'Info',
        'js_notify_success'         => 'Success',
        'js_notify_warning'         => 'Warning',
        'js_combatsim_planning'     => 'Planning',
        'js_combatsim_pending'      => 'Simulation running...',
        'js_combatsim_done'         => 'Complete',
        'js_msg_restore'            => 'restore',
        'js_msg_delete'             => 'delete',
        'js_copied'                 => 'Copied to clipboard',
        'js_report_operator'        => 'Report this message to a game operator?',

        // JS — LocalizationStrings
        'js_time_done'              => 'done',
        'js_question'               => 'Question',
        'js_ok'                     => 'Ok',
        'js_outlaw_warning'         => 'You are about to attack a stronger player. If you do this, your attack defenses will be shut down for 7 days and all players will be able to attack you without punishment. Are you sure you want to continue?',
        'js_last_slot_moon'         => 'This building will use the last available building slot. Expand your Lunar Base to receive more space. Are you sure you want to build this building?',
        'js_last_slot_planet'       => 'This building will use the last available building slot. Expand your Terraformer or buy a Planet Field item to obtain more slots. Are you sure you want to build this building?',
        'js_forced_vacation'        => 'Some game features are unavailable until your account is validated.',
        'js_more_details'           => 'More details',
        'js_less_details'           => 'Less detail',
        'js_planet_lock'            => 'Lock arrangement',
        'js_planet_unlock'          => 'Unlock arrangement',
        'js_activate_item_question' => 'Would you like to replace the existing item? The old bonus will be lost in the process.',
        'js_activate_item_header'   => 'Replace item?',

        // JS — chatLoca
        'chat_text_empty'           => 'Where is the message?',
        'chat_text_too_long'        => 'The message is too long.',
        'chat_same_user'            => 'You cannot write to yourself.',
        'chat_ignored_user'         => 'You have ignored this player.',
        'chat_not_activated'        => "This function is only available after your accounts activation.",
        'chat_new_chats'            => '#+# unread message(s)',
        'chat_more_users'           => 'show more',

        // JS — eventboxLoca
        'eventbox_mission'          => 'Mission',
        'eventbox_missions'         => 'Missions',
        'eventbox_next'             => 'Next',
        'eventbox_type'             => 'Type',
        'eventbox_own'              => 'own',
        'eventbox_friendly'         => 'friendly',
        'eventbox_hostile'          => 'hostile',

        // JS — planetMoveLoca
        'planet_move_ask_title'     => 'Resettle Planet',
        'planet_move_ask_cancel'    => 'Are you sure that you wish to cancel this planet relocation? The normal waiting time will thereby be maintained.',
        'planet_move_success'       => 'The planet relocation was successfully cancelled.',

        // JS — locaPremium
        'premium_building_half'     => 'Do you want to reduce the construction time by 50% of the total construction time () for <b>750 Dark Matter<\/b>?',
        'premium_building_full'     => 'Do you want to immediately complete the construction order for <b>750 Dark Matter<\/b>?',
        'premium_ships_half'        => 'Do you want to reduce the construction time by 50% of the total construction time () for <b>750 Dark Matter<\/b>?',
        'premium_ships_full'        => 'Do you want to immediately complete the construction order for <b>750 Dark Matter<\/b>?',
        'premium_research_half'     => 'Do you want to reduce the research time by 50% of the total research time () for <b>750 Dark Matter<\/b>?',
        'premium_research_full'     => 'Do you want to immediately complete the research order for <b>750 Dark Matter<\/b>?',

        // JS — loca object
        'loca_error_not_enough_dm'  => 'Not enough Dark Matter available! Do you want to buy some now?',
        'loca_notice'               => 'Reference',
        'loca_planet_giveup'        => 'Are you sure you want to abandon the planet %planetName% %planetCoordinates%?',
        'loca_moon_giveup'          => 'Are you sure you want to abandon the moon %planetName% %planetCoordinates%?',
    ],

    // ── Highscore ───────────────────────────────────────────────────────────
    'highscore' => [
        'player_highscore'      => 'Player highscore',
        'alliance_highscore'    => 'Alliance highscore',
        'own_position'          => 'Own position',
        'own_position_hidden'   => 'Own position (-)',
        'points'                => 'Points',
        'economy'               => 'Economy',
        'research'              => 'Research',
        'military'              => 'Military',
        'military_built'        => 'Military points built',
        'military_destroyed'    => 'Military points destroyed',
        'military_lost'         => 'Military points lost',
        'honour_points'         => 'Honour points',
        'position'              => 'Position',
        'player_name_honour'    => "Player's Name (Honour points)",
        'action'                => 'Action',
        'alliance'              => 'Alliance',
        'member'                => 'Member',
        'average_points'        => 'Average points',
        'no_alliances_found'    => 'No alliances found',
        'write_message'         => 'Write message',
        'buddy_request'         => 'Buddy request',
        'buddy_request_to'      => 'Buddy request to',
        'total_ships'           => 'Total ships',
        'buddy_request_sent'    => 'Buddy request sent successfully!',
        'buddy_request_failed'  => 'Failed to send buddy request.',
        'are_you_sure_ignore'   => 'Are you sure you want to ignore',
        'player_ignored'        => 'Player ignored successfully!',
        'player_ignored_failed' => 'Failed to ignore player.',
    ],

    // ── Premium / Officers ──────────────────────────────────────────────────
    'premium' => [
        'recruit_officers'           => 'Recruit Officers',
        'your_officers'              => 'Your officers',
        'intro_text'                 => 'With your officers you can lead your empire to a size beyond your wildest dreams - all you need is some Dark Matter and your workers and advisers will work even harder!',
        'info_dark_matter'           => 'More information about: Dark Matter',
        'info_commander'             => 'More information about: Commander',
        'info_admiral'               => 'More information about: Admiral',
        'info_engineer'              => 'More information about: Engineer',
        'info_geologist'             => 'More information about: Geologist',
        'info_technocrat'            => 'More information about: Technocrat',
        'info_commanding_staff'      => 'More information about: Commanding Staff',
        'hire_commander_tooltip'     => 'Hire commander|+40 favorites, building queue, shortcuts, transport scanner, advertisement-free* <span style=\'font-size: 10px; line-height: 10px\'>(*excludes: game related references)</span>',
        'hire_admiral_tooltip'       => "Hire admiral|Max. fleet slots +2,\nMax. expeditions +1,\nImproved fleet escape rate,\nCombat simulation save slots +20",
        'hire_engineer_tooltip'      => 'Hire engineer|Halves losses to defenses, +10% energy production',
        'hire_geologist_tooltip'     => 'Hire geologist|+10% mine production',
        'hire_technocrat_tooltip'    => 'Hire technocrat|+2 espionage levels, 25% less research time',
        'remaining_officers'         => ':current of :max',
        'benefit_fleet_slots_title'  => 'You can dispatch more fleets at the same time.',
        'benefit_fleet_slots'        => 'Max. fleet slots +1',
        'benefit_energy_title'       => 'Your power stations and solar satellites produce 2% more energy.',
        'benefit_energy'             => '+2% energy production',
        'benefit_mines_title'        => 'Your mines produce 2% more.',
        'benefit_mines'              => '+2% mine production',
        'benefit_espionage_title'    => '1 level will be added to your espionage research.',
        'benefit_espionage'          => '+1 espionage levels',
    ],

    // ── Shop ────────────────────────────────────────────────────────────────
    'shop' => [
        'page_title'               => 'Shop',
        'tooltip_shop'             => 'You can buy items here.',
        'tooltip_inventory'        => 'You can get an overview of your purchased items here.',
        'btn_shop'                 => 'Shop',
        'btn_inventory'            => 'Inventory',
        'category_special_offers'  => 'Special offers',
        'category_all'             => 'all',
        'category_resources'       => 'Resources',
        'category_buddy_items'     => 'Buddy Items',
        'category_construction'    => 'Construction',
        'btn_get_more_resources'   => 'Get more resources',
        'btn_purchase_dark_matter' => 'Purchase Dark Matter',
        'feature_coming_soon'      => 'Feature coming soon.',
        // Item tiers
        'tier_gold'                => 'Gold',
        'tier_silver'              => 'Silver',
        'tier_bronze'              => 'Bronze',
        // Tooltip labels inside item cards
        'tooltip_duration'         => 'Duration',
        'duration_now'             => 'now',
        'tooltip_price'            => 'Price',
        'tooltip_in_inventory'     => 'In Inventory',
        'dark_matter'              => 'Dark Matter',
        'dm_abbreviation'          => 'DM',
        'item_duration'            => 'Duration',
        'now'                      => 'now',
        'item_price'               => 'Price',
        'item_in_inventory'        => 'In Inventory',
        // JS loca keys (consumed by inventory.js)
        'loca_extend'              => 'Extend',
        'loca_activate'            => 'Activate',
        'loca_buy_activate'        => 'Buy and activate',
        'loca_buy_extend'          => 'Buy and extend',
        'loca_buy_dm'              => 'You don\'t have enough Dark Matter. Would you like to purchase some now?',
    ],

    // -------------------------------------------------------------------------
    // Search overlay
    // -------------------------------------------------------------------------

    'search' => [
        'input_hint'              => 'Put in player, alliance or planet name',
        'search_btn'              => 'Search',
        'tab_players'             => 'Player names',
        'tab_alliances'           => 'Alliances/Tags',
        'tab_planets'             => 'Planet names',
        'no_search_term'          => 'No search term entered',
        'searching'               => 'Searching...',
        'search_failed'           => 'Search failed. Please try again.',
        'no_results'              => 'No results found',
        'player_name'             => 'Player Name',
        'planet_name'             => 'Planet Name',
        'coordinates'             => 'Coordinates',
        'tag'                     => 'Tag',
        'alliance_name'           => 'Alliance name',
        'member'                  => 'Member',
        'points'                  => 'Points',
        'action'                  => 'Action',
        'apply_for_alliance'      => 'Apply for this alliance',
    ],

    // -------------------------------------------------------------------------
    // Notes overlay
    // -------------------------------------------------------------------------

    'notes' => [
        'no_notes_found'          => 'No notes found',
    ],

    // -------------------------------------------------------------------------
    // Planet abandon / rename overlay
    // -------------------------------------------------------------------------

    'planet_abandon' => [
        // Page description
        'description'                   => 'Using this menu you can change planet names and moons or completely abandon them.',

        // Rename section
        'rename_heading'                => 'Rename',
        'new_planet_name'               => 'New planet name',
        'new_moon_name'                 => 'New name of the moon',
        'rename_btn'                    => 'Rename',

        // Tooltips (HTML content – escaped automatically by {{ }} in title attributes)
        'tooltip_rules_title'           => 'Rules',
        'tooltip_rename_planet'         => 'You can rename your planet here.<br /><br />The planet name has to be between <span style="font-weight: bold;">2 and 20 characters</span> long.<br />Planet names may comprise of lower and upper case letters as well as numbers.<br />They may contain hyphens, underscores and spaces - however these may not be placed as follows:<br />- at the beginning or at the end of the name<br />- directly next to one another<br />- more than three times in the name',
        'tooltip_rename_moon'           => 'You can rename your moon here.<br /><br />The moon name has to be between <span style="font-weight: bold;">2 and 20 characters</span> long.<br />Moon names may comprise of lower and upper case letters as well as numbers.<br />They may contain hyphens, underscores and spaces - however these may not be placed as follows:<br />- at the beginning or at the end of the name<br />- directly next to one another<br />- more than three times in the name',

        // Abandon section headings
        'abandon_home_planet'           => 'Abandon home planet',
        'abandon_moon'                  => 'Abandon Moon',
        'abandon_colony'                => 'Abandon Colony',
        'abandon_home_planet_btn'       => 'Abandon Home Planet',
        'abandon_moon_btn'              => 'Abandon moon',
        'abandon_colony_btn'            => 'Abandon Colony',

        // Abandon warnings
        'home_planet_warning'           => 'If you abandon your home planet, immediately upon your next login you will be directed to the planet that you colonised next.',
        'items_lost_moon'               => 'If you have activated items on a moon, they will be lost if you abandon the moon.',
        'items_lost_planet'             => 'If you have activated items on a planet, they will be lost if you abandon the planet.',

        // Abandon confirm form
        'confirm_password'              => 'Please confirm deletion of :type [:coordinates] by putting in your password',
        'confirm_btn'                   => 'Confirm',
        'type_moon'                     => 'moon',
        'type_planet'                   => 'planet',

        // Validation messages (JS)
        'validation_min_chars'          => 'Not enough characters',
        'validation_pw_min'             => 'The entered password is too short (min. 4 characters)',
        'validation_pw_max'             => 'The entered password is too long (max. 20 characters)',
        'validation_email'              => 'You need to enter a valid email address!',
        'validation_special'            => 'Contains invalid characters.',
        'validation_underscore'         => 'Your name may not start or end with an underscore.',
        'validation_hyphen'             => 'Your name may not start or finish with a hyphen.',
        'validation_space'              => 'Your name may not start or end with a space.',
        'validation_max_underscores'    => 'Your name may not contain more than 3 underscores in total.',
        'validation_max_hyphens'        => 'Your name may not contain more than 3 hyphens.',
        'validation_max_spaces'         => 'Your name may not include more than 3 spaces in total.',
        'validation_consec_underscores' => 'You may not use two or more underscores one after the other.',
        'validation_consec_hyphens'     => 'You may not use two or more hyphens consecutively.',
        'validation_consec_spaces'      => 'You may not use two or more spaces one after the other.',

        // Controller messages
        'msg_invalid_planet_name'       => 'The new planet name is invalid. Please try again.',
        'msg_invalid_moon_name'         => 'The new moon name is invalid. Please try again.',
        'msg_planet_renamed'            => 'Planet renamed successfully.',
        'msg_moon_renamed'              => 'Moon renamed successfully.',
        'msg_wrong_password'            => 'Wrong password!',
        'msg_confirm_title'             => 'Confirm',
        'msg_confirm_deletion'          => 'If you confirm the deletion of the :type [:coordinates] (:name), all buildings, ships and defense systems that are located on that :type will be removed from your account. If you have items active on your :type, these will also be lost when you give up the :type. This process cannot be reversed!',
        'msg_reference'                 => 'Reference',
        'msg_abandoned'                 => ':type has been abandoned successfully!',
        'msg_type_moon'                 => 'Moon',
        'msg_type_planet'               => 'Planet',
        'msg_yes'                       => 'Yes',
        'msg_no'                        => 'No',
        'msg_ok'                        => 'Ok',
    ],
];
