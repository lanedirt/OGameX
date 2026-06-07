<?php

return [
    // -------------------------------------------------------------------------
    // Overview page
    // -------------------------------------------------------------------------

    'overview' => [
        // Planet stats panel (typewriter animation)
        'diameter'             => 'Durchmesser',
        'temperature'          => 'Temperatur',
        'position'             => 'Position',
        'points'               => 'Punkte',
        'honour_points'        => 'Ehrenpunkte',
        'score_place'          => 'Platz',
        'score_of'             => 'von',

        // Page / section headings
        'page_title'           => 'Übersicht',
        'buildings'            => 'Gebäude',
        'research'             => 'Forschung',

        // Planet header buttons
        'switch_to_moon'       => 'Zum Mond wechseln',
        'switch_to_planet'     => 'Zum Planeten wechseln',
        'abandon_rename'       => 'Aufgeben/Umbenennen',
        'abandon_rename_title'  => 'Planet aufgeben/umbenennen',
        'abandon_rename_modal'  => ':planet_name aufgeben/umbenennen',

        // Default planet names (used at registration)
        'homeworld'            => 'Heimatplanet',
        'colony'               => 'Kolonie',
        'moon'                 => 'Mond',
    ],

    // -------------------------------------------------------------------------
    // Planet relocation / planet move
    // -------------------------------------------------------------------------

    'planet_move' => [
        'resettle_title' => 'Umsiedeln',
        'cancel_confirm' => 'Bist du sicher, dass du diese Planetenumsiedlung abbrechen möchtest? Die reservierte Position wird freigegeben.',
        'cancel_success' => 'Die Planetenumsiedlung wurde erfolgreich abgebrochen.',
        'blockers_title' => 'Die folgenden Dinge stehen deiner Planetenumsiedlung derzeit im Weg:',
        'no_blockers'    => 'Nichts kann der geplanten Umsiedlung des Planeten mehr im Weg stehen.',
        'cooldown_title' => 'Zeit bis zur nächsten möglichen Umsiedlung',
        'to_galaxy'      => 'Zur Galaxie',
        'relocate'       => 'Umsiedeln',
        'cancel'         => 'Abbrechen',
        'explanation'    => 'Die Umsiedlung ermöglicht es dir, deine Planeten an eine andere Position in einem entfernten System deiner Wahl zu verlegen.<br /><br />Die eigentliche Umsiedlung findet erst 24 Stunden nach der Aktivierung statt. In dieser Zeit kannst du deine Planeten normal nutzen. Ein Countdown zeigt dir an, wie viel Zeit bis zur Umsiedlung verbleibt.<br /><br />Sobald der Countdown abgelaufen ist und der Planet umgezogen werden soll, dürfen keine deiner stationierten Flotten aktiv sein. Zu diesem Zeitpunkt sollte auch nichts im Bau, nichts in Reparatur und nichts erforscht werden. Wenn beim Ablauf des Countdowns noch ein Bauauftrag, ein Reparaturauftrag oder eine Flotte aktiv ist, wird die Umsiedlung abgebrochen.<br /><br />Bei einer erfolgreichen Umsiedlung werden dir 240.000 Dunkle Materie berechnet. Die Planeten, Gebäude und gelagerten Rohstoffe inklusive Mond werden sofort verlegt. Deine Flotten reisen automatisch zu den neuen Koordinaten mit der Geschwindigkeit des langsamsten Schiffs. Das Sprungtor eines umgesiedelten Mondes wird für 24 Stunden deaktiviert.',
        'err_position_not_empty'      => 'Die Zielposition ist nicht frei.',
        'err_already_in_progress'     => 'Eine Planetenumsiedlung ist bereits im Gange.',
        'err_on_cooldown'             => 'Die Umsiedlung befindet sich in der Abklingzeit. Bitte warte, bevor du erneut umsiedeln kannst.',
        'err_insufficient_dm'         => 'Nicht genügend Dunkle Materie. Du benötigst :amount DM.',
        'err_buildings_in_progress'   => 'Umsiedlung nicht möglich, solange Gebäude im Bau sind.',
        'err_research_in_progress'    => 'Umsiedlung nicht möglich, solange Forschung läuft.',
        'err_units_in_progress'       => 'Umsiedlung nicht möglich, solange Einheiten gebaut werden.',
        'err_fleets_active'           => 'Umsiedlung nicht möglich, solange Flottenmissionen aktiv sind.',
        'err_no_active_relocation'    => 'Keine aktive Planetenumsiedlung gefunden.',
    ],

    // -------------------------------------------------------------------------
    // Shared UI strings (buttons, dialog labels)
    // -------------------------------------------------------------------------

    'shared' => [
        'caution'        => 'Achtung',
        'yes'            => 'Ja',
        'no'             => 'Nein',
        'error'          => 'Fehler',
        'dark_matter'    => 'Dunkle Materie',
        'duration'       => 'Dauer',
        'error_occurred' => 'Ein Fehler ist aufgetreten.',
        'level'          => 'Stufe',
        'ok'             => 'OK',
    ],

    // -------------------------------------------------------------------------
    // Shared building page strings (resources, facilities, research, shipyard, defense)
    // -------------------------------------------------------------------------

    'buildings' => [
        // Building icon status tooltips
        'under_construction'     => 'Im Bau',
        'vacation_mode_error'    => 'Fehler, Spieler ist im Urlaubsmodus',
        'requirements_not_met'   => 'Voraussetzungen sind nicht erfüllt!',
        'wrong_class'            => 'Falsche Charakterklasse!',
        'no_moon_building'       => 'Dieses Gebäude kann nicht auf einem Mond errichtet werden!',
        'not_enough_resources'   => 'Nicht genügend Rohstoffe!',
        'queue_full'             => 'Bauliste ist voll',
        'not_enough_fields'      => 'Nicht genügend Felder!',
        'shipyard_busy'          => 'Die Raumschiffswerft ist noch beschäftigt',
        'research_in_progress'   => 'Es wird gerade geforscht!',
        'research_lab_expanding' => 'Forschungslabor wird ausgebaut.',
        'shipyard_upgrading'     => 'Raumschiffswerft wird ausgebaut.',
        'nanite_upgrading'       => 'Nanitenfabrik wird ausgebaut.',
        'max_amount_reached'     => 'Maximale Anzahl erreicht!',
        // Expand upgrade button (named params: :title, :level)
        'expand_button'          => ':title auf Stufe :level ausbauen',
        // JS loca object strings
        'loca_notice'            => 'Hinweis',
        'loca_demolish'          => 'TECHNOLOGY_NAME wirklich um eine Stufe zurückbauen?',
        'loca_lifeform_cap'      => 'Ein oder mehrere zugehörige Boni sind bereits auf dem Maximum. Möchtest du den Bau trotzdem fortsetzen?',
        'last_inquiry_error'     => 'Deine letzte Aktion konnte nicht verarbeitet werden. Bitte versuche es erneut.',
        'planet_move_warning'    => 'Achtung! Diese Mission läuft möglicherweise noch, wenn die Umsiedlung beginnt, und wird in diesem Fall abgebrochen. Möchtest du wirklich mit diesem Auftrag fortfahren?',
        'building_started'       => 'Bau erfolgreich gestartet.',
        'invalid_token'          => 'Ungültiges Token.',
        'downgrade_started'      => 'Rückbau gestartet.',
        'construction_canceled'  => 'Bau abgebrochen.',
        'added_to_queue'         => 'Zur Bauliste hinzugefügt.',
        'invalid_queue_item'     => 'Ungültige Baulisten-ID',
    ],

    // -------------------------------------------------------------------------
    // Resources page (mines / storage buildings)
    // -------------------------------------------------------------------------

    'resources_page' => [
        'page_title'    => 'Versorgung',
        'settings_link' => 'Versorgungseinstellungen',
        'section_title' => 'Rohstoffgebäude',
    ],

    // -------------------------------------------------------------------------
    // Facilities page
    // -------------------------------------------------------------------------

    'facilities_page' => [
        'page_title'     => 'Anlagen',
        'section_title'  => 'Anlagengebäude',
        'use_jump_gate'  => 'Sprungtor benutzen',
        'jump_gate'      => 'Sprungtor',
        'alliance_depot' => 'Allianzdepot',
        'burn_confirm'   => 'Bist du sicher, dass du dieses Trümmerfeld verbrennen möchtest? Diese Aktion kann nicht rückgängig gemacht werden.',
    ],

    // -------------------------------------------------------------------------
    // Research page
    // -------------------------------------------------------------------------

    'research_page' => [
        'basic'    => 'Grundlagenforschung',
        'drive'    => 'Antriebsforschung',
        'advanced' => 'Erweiterte Forschungen',
        'combat'   => 'Kampfforschung',
    ],

    // -------------------------------------------------------------------------
    // Shipyard page
    // -------------------------------------------------------------------------

    'shipyard_page' => [
        'battleships'           => 'Kampfschiffe',
        'civil_ships'           => 'Zivile Schiffe',
        'no_units_idle'         => 'Derzeit werden keine Einheiten gebaut.',
        'no_units_idle_tooltip' => 'Klicken, um zur Raumschiffswerft zu gelangen.',
        'to_shipyard'           => 'Zur Raumschiffswerft',
    ],

    // -------------------------------------------------------------------------
    // Defense page
    // -------------------------------------------------------------------------

    'defense_page' => [
        'page_title'    => 'Verteidigung',
        'section_title' => 'Verteidigungsanlagen',
    ],

    // -------------------------------------------------------------------------
    // Resource settings page
    // -------------------------------------------------------------------------

    'resource_settings' => [
        'production_factor'  => 'Produktionsfaktor',
        'recalculate'        => 'Neu berechnen',
        'metal'              => 'Metall',
        'crystal'            => 'Kristall',
        'deuterium'          => 'Deuterium',
        'energy'             => 'Energie',
        'basic_income'       => 'Grundeinkommen',
        'level'              => 'Stufe',
        'number'             => 'Anzahl:',
        'items'              => 'Items',
        'geologist'          => 'Geologe',
        'mine_production'    => 'Minenertrag',
        'engineer'           => 'Ingenieur',
        'energy_production'  => 'Energieproduktion',
        'character_class'    => 'Klasse',
        'commanding_staff'   => 'Kommandostab',
        'storage_capacity'   => 'Lagerkapazität',
        'total_per_hour'     => 'Gesamt pro Stunde:',
        'total_per_day'      => 'Gesamt pro Tag',
        'total_per_week'     => 'Gesamt pro Woche:',
    ],

    // -------------------------------------------------------------------------
    // Destroy rockets dialog (facilities page)
    // -------------------------------------------------------------------------

    'facilities_destroy' => [
        'silo_description'  => 'Raketensilos werden zum Bau, zur Lagerung und zum Abschuss von Interplanetarraketen und Abfangraketen verwendet. Mit jeder Stufe des Silos können fünf Interplanetarraketen oder zehn Abfangraketen gelagert werden. Eine Interplanetarrakete benötigt den gleichen Platz wie zwei Abfangraketen. Die Lagerung von Interplanetarraketen und Abfangraketen im selben Silo ist erlaubt.',
        'silo_capacity'     => 'Ein Raketensilo auf Stufe :level kann :ipm Interplanetarraketen oder :abm Abfangraketen aufnehmen.',
        'type'              => 'Typ',
        'number'            => 'Anzahl',
        'tear_down'         => 'Abreißen',
        'proceed'           => 'Ausführen',
        'enter_minimum'     => 'Bitte gib mindestens eine Rakete zum Zerstören ein',
        'not_enough_abm'    => 'Du hast nicht so viele Abfangraketen',
        'not_enough_ipm'    => 'Du hast nicht so viele Interplanetarraketen',
        'destroyed_success' => 'Raketen erfolgreich zerstört',
        'destroy_failed'    => 'Zerstörung der Raketen fehlgeschlagen',
        'error'             => 'Ein Fehler ist aufgetreten. Bitte versuche es erneut.',
    ],

    // -------------------------------------------------------------------------
    // Fleet pages (dispatch + movement)
    // -------------------------------------------------------------------------

    'fleet' => [
        // Page / step headers
        'dispatch_1_title'         => 'Flottenversand I',
        'dispatch_2_title'         => 'Flottenversand II',
        'dispatch_3_title'         => 'Flottenversand III',
        'movement_title'           => 'Flottenbewegung',
        'to_movement'              => 'Zur Flottenbewegung',

        // Status bar
        'fleets'                   => 'Flotten',
        'expeditions'              => 'Expeditionen',
        'reload'                   => 'Aktualisieren',
        'clock'                    => 'Uhr',
        'load_dots'                => 'laden...',
        'never'                    => 'Nie',

        // Fleet slot info
        'tooltip_slots'            => 'Belegte/Gesamte Flottenslots',
        'no_free_slots'            => 'Keine Flottenslots verfügbar',
        'tooltip_exp_slots'        => 'Belegte/Gesamte Expeditionsslots',
        'market_slots'             => 'Angebote',
        'tooltip_market_slots'     => 'Belegte/Gesamte Handelsflotten',

        // Warning / impossible states
        'fleet_dispatch'           => 'Flottenversand',
        'dispatch_impossible'      => 'Kein Flottenversand möglich',
        'no_ships'                 => 'Es befinden sich keine Schiffe auf diesem Planeten.',
        'in_combat'                => 'Die Flotte befindet sich gerade im Kampf.',
        'vacation_error'           => 'Im Urlaubsmodus können keine Flotten gesendet werden!',
        'not_enough_deuterium'     => 'Nicht genügend Deuterium!',
        'no_target'                => 'Du musst ein gültiges Ziel auswählen.',
        'cannot_send_to_target'    => 'Flotten können nicht zu diesem Ziel gesendet werden.',
        'cannot_start_mission'     => 'Du kannst diese Mission nicht starten.',

        // Status bar labels (no trailing colon — add : in template where needed)
        'mission_label'            => 'Auftrag',
        'target_label'             => 'Ziel',
        'player_name_label'        => 'Spielername',
        'no_selection'             => 'Nichts ausgewählt',
        'no_mission_selected'      => 'Keine Mission ausgewählt!',

        // Step 1 – ship selection
        'combat_ships'             => 'Kampfschiffe',
        'civil_ships'              => 'Zivile Schiffe',
        'standard_fleets'          => 'Standardflotten',
        'edit_standard_fleets'     => 'Standardflotten bearbeiten',
        'select_all_ships'         => 'Alle Schiffe auswählen',
        'reset_choice'             => 'Zurücksetzen',
        'api_data'                 => 'Diese Daten können in einen kompatiblen Kampfsimulator eingegeben werden:',
        'tactical_retreat'         => 'Taktischer Rückzug',
        'tactical_retreat_tooltip' => 'Deuteriumverbrauch pro taktischem Rückzug anzeigen',
        'continue'                 => 'Weiter',
        'back'                     => 'Zurück',

        // Step 2 – destination
        'origin'                   => 'Abflugort',
        'destination'              => 'Zielort',
        'planet'                   => 'Planet',
        'moon'                     => 'Mond',
        'coordinates'              => 'Koordinaten',
        'distance'                 => 'Entfernung',
        'debris_field'             => 'Trümmerfeld',
        'debris_field_lower'       => 'Trümmerfeld',
        'shortcuts'                => 'Shortlinks',
        'combat_forces'            => 'Kampfverbände',
        'player_label'             => 'Spieler',
        'player_name'              => 'Spielername',

        // Step 3 – mission selection
        'select_mission'           => 'Mission für das Ziel wählen',
        'bashing_disabled'         => 'Angriffsmissionen wurden aufgrund zu vieler Angriffe auf das Ziel deaktiviert.',

        // Mission names
        'mission_expedition'       => 'Expedition',
        'mission_colonise'         => 'Kolonisieren',
        'mission_recycle'          => 'Trümmerfeld abbauen',
        'mission_transport'        => 'Transport',
        'mission_deploy'           => 'Stationieren',
        'mission_espionage'        => 'Spionage',
        'mission_acs_defend'       => 'Halten',
        'mission_attack'           => 'Angreifen',
        'mission_acs_attack'       => 'Verbandsangriff',
        'mission_destroy_moon'     => 'Zerstören',

        // Mission descriptions
        'desc_attack'              => 'Greift die Flotte und Verteidigung deines Gegners an.',
        'desc_acs_attack'          => 'Ehrenvolle Kämpfe können zu unehrenvollen Kämpfen werden, wenn starke Spieler durch den Verbandsangriff eingreifen. Die Summe der militärischen Punkte des Angreifers im Vergleich zur Summe der militärischen Punkte des Verteidigers ist hier der entscheidende Faktor.',
        'desc_transport'           => 'Transportiert deine Rohstoffe zu anderen Planeten.',
        'desc_deploy'              => 'Sendet deine Flotte dauerhaft zu einem anderen Planeten deines Imperiums.',
        'desc_acs_defend'          => 'Verteidige den Planeten deines Teamkameraden.',
        'desc_espionage'           => 'Spioniere die Welten fremder Imperatoren aus.',
        'desc_colonise'            => 'Kolonisiert einen neuen Planeten.',
        'desc_recycle'             => 'Sende deine Recycler zu einem Trümmerfeld, um die dort treibenden Rohstoffe einzusammeln.',
        'desc_destroy_moon'        => 'Zerstört den Mond deines Feindes.',
        'desc_expedition'          => 'Schicke deine Schiffe in die entlegensten Bereiche des Weltraums, um spannende Abenteuer zu erleben.',

        // ACS Attack – federation overlay
        'fleet_union'              => 'Flottenverband',
        'union_created'            => 'Flottenverband erfolgreich erstellt.',
        'union_edited'             => 'Flottenverband erfolgreich bearbeitet.',
        'err_union_max_fleets'     => 'Maximal 16 Flotten können angreifen.',
        'err_union_max_players'    => 'Maximal 5 Spieler können angreifen.',
        'err_union_too_slow'        => 'Du bist zu langsam, um diesem Verband beizutreten.',
        'err_union_target_mismatch' => 'Deine Flotte muss dasselbe Ziel wie der Flottenverband haben.',
        'union_name'               => 'Verbandsname',
        'buddy_list'               => 'Buddyliste',
        'buddy_list_loading'       => 'Laden...',
        'buddy_list_empty'         => 'Keine Buddys verfügbar',
        'buddy_list_error'         => 'Buddys konnten nicht geladen werden',
        'search_user'              => 'Spieler suchen',
        'search'                   => 'Suchen',
        'union_user'               => 'Verbandsspieler',
        'invite'                   => 'Einladen',
        'kick'                     => 'Entfernen',
        'ok'                       => 'Ok',
        'own_fleet'                => 'Eigene Flotte',

        // Briefing section (no trailing colons — add : in template where needed)
        'briefing'                 => 'Briefing',
        'load_resources'           => 'Rohstoffe einladen',
        'load_all_resources'       => 'Alle Rohstoffe einladen',
        'all_resources'            => 'Alle Rohstoffe',
        'flight_duration'          => 'Flugdauer (einfach)',
        'federation_duration'      => 'Flugdauer (Flottenverband)',
        'arrival'                  => 'Ankunft',
        'return_trip'              => 'Rückkehr',
        'speed'                    => 'Geschwindigkeit:',
        'max_abbr'                 => 'max.',
        'hour_abbr'                => 'h',
        'deuterium_consumption'    => 'Treibstoffverbrauch',
        'empty_cargobays'          => 'Freier Laderaum',
        'hold_time'                => 'Haltezeit',
        'expedition_duration'      => 'Expeditionsdauer',
        'cargo_bay'                => 'Laderaum',
        'cargo_space'              => 'Freier Laderaum / max. Laderaum',
        'send_fleet'               => 'Flotte versenden',
        'retreat_on_defender'      => 'Rückzug bei Flucht des Verteidigers',
        'retreat_tooltip'          => 'Wenn diese Option aktiviert ist, wird deine Flotte ebenfalls ohne Kampf abziehen, falls dein Gegner flieht.',
        'plunder_food'             => 'Plündere Nahrung',

        // Resources labels (for loca object)
        'metal'                    => 'Metall',
        'crystal'                  => 'Kristall',
        'deuterium'                => 'Deuterium',

        // Movement page
        'fleet_details'            => 'Flottendetails',
        'ships'                    => 'Schiffe',
        'shipment'                 => 'Ladung',
        'recall'                   => 'Zurückrufen',
        'start_time'               => 'Startzeit',
        'time_of_arrival'          => 'Ankunftszeit',
        'deep_space'               => 'Tiefer Weltraum',

        // Target / player status indicators
        'uninhabited_planet'       => 'Unbewohnter Planet',
        'no_debris_field'          => 'Kein Trümmerfeld',
        'player_vacation'          => 'Spieler im Urlaubsmodus',
        'admin_gm'                 => 'Admin oder GM',
        'noob_protection'          => 'Anfängerschutz',
        'player_too_strong'        => 'Dieser Planet kann nicht angegriffen werden, da der Spieler zu stark ist!',
        'no_moon'                  => 'Kein Mond verfügbar.',
        'no_recycler'              => 'Kein Recycler verfügbar.',
        'no_events'                => 'Derzeit finden keine Ereignisse statt.',
        'planet_already_reserved'  => 'Dieser Planet wurde bereits für eine Umsiedlung reserviert.',
        'max_planet_warning'       => 'Achtung! Momentan können keine weiteren Planeten kolonisiert werden. Für jede neue Kolonie sind zwei Stufen Astrophysik-Forschung erforderlich. Möchtest du deine Flotte trotzdem senden?',

        // Galaxy / network
        'empty_systems'            => 'Leere Systeme',
        'inactive_systems'         => 'Inaktive Systeme',
        'network_on'               => 'An',
        'network_off'              => 'Aus',

        // Error codes (used in errorCodeMap)
        'err_generic'              => 'Ein Fehler ist aufgetreten',
        'err_no_moon'              => 'Fehler, es gibt keinen Mond',
        'err_newbie_protection'    => 'Fehler, Spieler kann wegen Anfängerschutz nicht angegriffen werden',
        'err_too_strong'           => 'Der Spieler ist zu stark, um angegriffen zu werden',
        'err_vacation_mode'        => 'Fehler, Spieler ist im Urlaubsmodus',
        'err_own_vacation'         => 'Im Urlaubsmodus können keine Flotten gesendet werden!',
        'err_not_enough_ships'     => 'Fehler, nicht genügend Schiffe verfügbar, sende maximale Anzahl:',
        'err_no_ships'             => 'Fehler, keine Schiffe verfügbar',
        'err_no_slots'             => 'Fehler, keine freien Flottenslots verfügbar',
        'err_no_deuterium'         => 'Fehler, du hast nicht genügend Deuterium',
        'err_no_planet'            => 'Fehler, dort befindet sich kein Planet',
        'err_no_cargo'             => 'Fehler, nicht genügend Ladekapazität',
        'err_multi_alarm'          => 'Multi-Alarm',
        'err_attack_ban'                  => 'Angriffsverbot',

        // Fleet movement labels
        'enemy_fleet'                     => 'Feindlich',
        'friendly_fleet'                  => 'Freundlich',

        // Fleet slot / admiral
        'admiral_slot_bonus'              => 'Admiralbonus: zusätzlicher Flottenslot',
        'general_slot_bonus'              => 'Bonus-Flottenslot',

        // Bash protection
        'bash_warning'                    => 'Warnung: Das Angriffslimit wurde erreicht! Weitere Angriffe können zu einer Sperrung führen.',

        // Fleet templates
        'add_new_template'                => 'Flottenvorlage speichern',

        // Tactical retreat
        'tactical_retreat_label'          => 'Taktischer Rückzug',
        'tactical_retreat_full_tooltip'   => 'Taktischen Rückzug aktivieren: Deine Flotte wird sich zurückziehen, wenn das Kampfverhältnis ungünstig ist. Erfordert den Admiral für das 3:1-Verhältnis.',
        'tactical_retreat_admiral_tooltip'=> 'Taktischer Rückzug bei 3:1-Verhältnis (erfordert Admiral)',
        'fleet_sent_success'             => 'Deine Flotte wurde erfolgreich gesendet.',
    ],

    // -------------------------------------------------------------------------
    // Galaxy page
    // -------------------------------------------------------------------------

    'galaxy' => [
        // Vacation mode
        'vacation_error'               => 'Du kannst die Galaxieansicht im Urlaubsmodus nicht nutzen!',

        // Navigation / header
        'system'                       => 'Sonnensystem',
        'go'                           => 'Los!',

        // System action buttons
        'system_phalanx'               => 'System-Phalanx',
        'system_espionage'             => 'System-Spionage',
        'discoveries'                  => 'Entdeckungen',
        'discoveries_tooltip'          => 'Starte eine Entdeckungsmission zu allen möglichen Positionen',

        // Header stats row labels
        'probes_short'                 => 'Spi.Sonde',
        'recycler_short'               => 'Recy.',
        'ipm_short'                    => 'IPR.',
        'used_slots'                   => 'Belegte Slots',

        // Table header columns
        'planet_col'                   => 'Planet',
        'name_col'                     => 'Name',
        'moon_col'                     => 'Mond',
        'debris_short'                 => 'TF',
        'player_status'                => 'Spieler (Status)',
        'alliance'                     => 'Allianz',
        'action'                       => 'Aktion',

        // Expedition / deep space row
        'planets_colonized'            => 'Kolonisierte Planeten',
        'expedition_fleet'             => 'Expeditionsflotte',
        'admiral_needed'               => 'Du benötigst einen Admiral, um diese Funktion zu nutzen.',
        'send'                         => 'Senden',

        // Legend tooltip
        'legend'                       => 'Legende',
        'status_admin_abbr'            => 'A',
        'legend_admin'                 => 'Administrator',
        'status_strong_abbr'           => 's',
        'legend_strong'                => 'Starker Spieler',
        'status_noob_abbr'             => 'n',
        'legend_noob'                  => 'Schwacher Spieler (Noob)',
        'status_outlaw_abbr'           => 'o',
        'legend_outlaw'                => 'Vogelfreier (temporär)',
        'status_vacation_abbr'         => 'v',
        'vacation_mode'                => 'Urlaubsmodus',
        'status_banned_abbr'           => 'b',
        'legend_banned'                => 'Gesperrt',
        'status_inactive_abbr'         => 'i',
        'legend_inactive_7'            => '7 Tage inaktiv',
        'status_longinactive_abbr'     => 'I',
        'legend_inactive_28'           => '28 Tage inaktiv',
        'status_honorable_abbr'        => 'hp',
        'legend_honorable'             => 'Ehrenvolles Ziel',

        // loca JS object (unique galaxy strings)
        'phalanx_restricted'           => 'Die System-Phalanx kann nur von der Allianzklasse Forscher genutzt werden!',
        'astro_required'               => 'Du musst zuerst Astrophysik erforschen.',
        'galaxy_nav'                   => 'Galaxie',
        'activity'                     => 'Aktivität',
        'no_action'                    => 'Keine Aktionen verfügbar.',
        'time_minute_abbr'             => 'min',
        'moon_diameter_km'             => 'Monddurchmesser in km',
        'km'                           => 'km',
        'pathfinders_needed'           => 'Benötigte Pathfinder',
        'recyclers_needed'             => 'Benötigte Recycler',
        'mine_debris'                  => 'Abbauen',
        'phalanx_no_deut'              => 'Nicht genügend Deuterium für die Phalanx.',
        'use_phalanx'                  => 'Phalanx benutzen',
        'colonize_error'               => 'Es ist nicht möglich, einen Planeten ohne Kolonieschiff zu kolonisieren.',
        'ranking'                      => 'Rangliste',
        'espionage_report'             => 'Spionagebericht',
        'missile_attack'               => 'Raketenangriff',
        'rank'                         => 'Rang',
        'alliance_member'              => 'Mitglied',
        'alliance_class'               => 'Allianzklasse',
        'espionage_not_possible'       => 'Spionage nicht möglich',
        'espionage'                    => 'Spionage',
        'hire_admiral'                 => 'Admiral anheuern',
        'dark_matter'                  => 'Dunkle Materie',
        'outlaw_explanation'           => 'Wenn du vogelfrei bist, hast du keinen Angriffsschutz mehr und kannst von allen Spielern angegriffen werden.',
        'honorable_target_explanation' => 'Im Kampf gegen dieses Ziel kannst du Ehrenpunkte erhalten und 50% mehr Beute plündern.',

        // galaxyLoca JS object
        'relocate_success'             => 'Die Position wurde für dich reserviert. Die Umsiedlung der Kolonie hat begonnen.',
        'relocate_title'               => 'Umsiedeln',
        'relocate_question'            => 'Bist du sicher, dass du deinen Planeten zu diesen Koordinaten umsiedeln möchtest? Zur Finanzierung der Umsiedlung benötigst du :cost Dunkle Materie.',
        'deut_needed_relocate'         => 'Du hast nicht genügend Deuterium! Du benötigst 10 Einheiten Deuterium.',
        'fleet_attacking'              => 'Flotte greift an!',
        'fleet_underway'               => 'Flotte ist unterwegs',
        'discovery_send'               => 'Erkundungsschiff entsenden',
        'discovery_success'            => 'Erkundungsschiff entsandt',
        'discovery_unavailable'        => 'Du kannst kein Erkundungsschiff zu dieser Position entsenden.',
        'discovery_underway'           => 'Ein Erkundungsschiff ist bereits auf dem Weg zu diesem Planeten.',
        'discovery_locked'             => 'Du hast die Forschung zur Entdeckung neuer Lebensformen noch nicht freigeschaltet.',
        'discovery_title'              => 'Erkundungsschiff',
        'discovery_question'           => 'Möchtest du ein Erkundungsschiff zu diesem Planeten entsenden?<br/>Metall: 5000 Kristall: 1000 Deuterium: 500',

        // Phalanx result dialog (JS strings inside Blade-rendered script block)
        'sensor_report'                => 'Sensorbericht',
        'sensor_report_from'           => 'Sensorbericht von',
        'refresh'                      => 'Aktualisieren',
        'arrived'                      => 'Angekommen',

        // Missile attack dialog
        'target'                       => 'Ziel',
        'flight_duration'              => 'Flugdauer',
        'ipm_full'                     => 'Interplanetarraketen',
        'primary_target'               => 'Primärziel',
        'no_primary_target'            => 'Kein Primärziel ausgewählt: zufälliges Ziel',
        'target_has'                   => 'Ziel hat',
        'abm_full'                     => 'Abfangraketen',
        'fire'                         => 'Feuer',
        'valid_missile_count'          => 'Bitte gib eine gültige Anzahl an Raketen ein',
        'not_enough_missiles'          => 'Du hast nicht genügend Raketen',
        'launched_success'             => 'Raketen erfolgreich abgeschossen!',
        'launch_failed'                => 'Raketenstart fehlgeschlagen',
        'alliance_page'                => 'Allianzinformationen',
        'apply'                        => 'Bewerben',
        'contact_support'              => 'Support kontaktieren',
    ],

    // -------------------------------------------------------------------------
    // Buddy system (buddy requests + player ignore — used in galaxy page)
    // -------------------------------------------------------------------------

    'buddy' => [
        'request_sent'   => 'Buddyanfrage erfolgreich gesendet!',
        'request_failed' => 'Buddyanfrage konnte nicht gesendet werden.',
        'request_to'     => 'Buddyanfrage an',
        'ignore_confirm' => 'Bist du sicher, dass du ignorieren möchtest',
        'ignore_success' => 'Spieler erfolgreich ignoriert!',
        'ignore_failed'  => 'Spieler konnte nicht ignoriert werden.',
    ],

    // -------------------------------------------------------------------------
    // Messages page
    // -------------------------------------------------------------------------

    'messages' => [
        // Main tabs
        'tab_fleets'        => 'Flotten',
        'tab_communication' => 'Kommunikation',
        'tab_economy'       => 'Ökonomie',
        'tab_universe'      => 'Spielwelt',
        'tab_system'        => 'OGame',
        'tab_favourites'    => 'Favoriten',

        // Fleet subtabs
        'subtab_espionage'   => 'Spionage',
        'subtab_combat'      => 'Kampfberichte',
        'subtab_expeditions' => 'Expeditionen',
        'subtab_transport'   => 'Verbände/Transport',
        'subtab_other'       => 'Sonstige',

        // Communication subtabs
        'subtab_messages'         => 'Nachrichten',
        'subtab_information'      => 'Informationen',
        'subtab_shared_combat'    => 'Geteilte Kampfberichte',
        'subtab_shared_espionage' => 'Geteilte Spionageberichte',

        // General UI
        'news_feed'          => 'Newsfeed',
        'loading'            => 'laden...',
        'error_occurred'     => 'Ein Fehler ist aufgetreten',
        'mark_favourite'     => 'Als Favorit markieren',
        'remove_favourite'   => 'Aus Favoriten entfernen',
        'from'               => 'Von',
        'no_messages'        => 'Derzeit sind keine Nachrichten in diesem Tab verfügbar',
        'new_alliance_msg'   => 'Neue Allianznachricht',
        'to'                 => 'An',
        'all_players'        => 'Alle Spieler',
        'send'               => 'Senden',
        'delete_buddy_title' => 'Buddy löschen',
        'report_to_operator' => 'Diese Nachricht an einen Spielleiter melden?',
        'too_few_chars'      => 'Zu wenige Zeichen! Bitte gib mindestens 2 Zeichen ein.',

        // BBCode editor (localizedBBCode)
        'bbcode_bold'           => 'Fett',
        'bbcode_italic'         => 'Kursiv',
        'bbcode_underline'      => 'Unterstrichen',
        'bbcode_stroke'         => 'Durchgestrichen',
        'bbcode_sub'            => 'Tiefgestellt',
        'bbcode_sup'            => 'Hochgestellt',
        'bbcode_font_color'     => 'Schriftfarbe',
        'bbcode_font_size'      => 'Schriftgröße',
        'bbcode_bg_color'       => 'Hintergrundfarbe',
        'bbcode_bg_image'       => 'Hintergrundbild',
        'bbcode_tooltip'        => 'Tooltip',
        'bbcode_align_left'     => 'Linksbündig',
        'bbcode_align_center'   => 'Zentriert',
        'bbcode_align_right'    => 'Rechtsbündig',
        'bbcode_align_justify'  => 'Blocksatz',
        'bbcode_block'          => 'Absatz',
        'bbcode_code'           => 'Code',
        'bbcode_spoiler'        => 'Spoiler',
        'bbcode_moreopts'       => 'Weitere Optionen',
        'bbcode_list'           => 'Liste',
        'bbcode_hr'             => 'Horizontale Linie',
        'bbcode_picture'        => 'Bild',
        'bbcode_link'           => 'Link',
        'bbcode_email'          => 'E-Mail',
        'bbcode_player'         => 'Spieler',
        'bbcode_item'           => 'Gegenstand',
        'bbcode_coordinates'    => 'Koordinaten',
        'bbcode_preview'        => 'Vorschau',
        'bbcode_text_ph'        => 'Text...',
        'bbcode_player_ph'      => 'Spieler-ID oder Name',
        'bbcode_item_ph'        => 'Gegenstands-ID',
        'bbcode_coord_ph'       => 'Galaxie:System:Position',
        'bbcode_chars_left'     => 'Verbleibende Zeichen',
        'bbcode_ok'             => 'Ok',
        'bbcode_cancel'         => 'Abbrechen',
        'bbcode_repeat_x'       => 'Horizontal wiederholen',
        'bbcode_repeat_y'       => 'Vertikal wiederholen',

        // Espionage report
        'spy_player'          => 'Spieler',
        'spy_activity'        => 'Aktivität',
        'spy_minutes_ago'     => 'Minuten zuvor',
        'spy_class'           => 'Klasse',
        'spy_unknown'         => 'Unbekannt',
        'spy_alliance_class'  => 'Allianzklasse',
        'spy_no_alliance_class' => 'Keine Allianzklasse ausgewählt',
        'spy_resources'       => 'Rohstoffe',
        'spy_loot'            => 'Beute',
        'spy_counter_esp'     => 'Gegenspionagewahrscheinlichkeit',
        'spy_no_info'         => 'Wir konnten keine zuverlässigen Informationen dieser Art aus dem Scan gewinnen.',
        'spy_debris_field'    => 'Trümmerfeld',
        'spy_no_activity'     => 'Deine Spionage zeigt keine Auffälligkeiten in der Atmosphäre des Planeten. Es scheint in der letzten Stunde keine Aktivität auf dem Planeten gegeben zu haben.',
        'spy_fleets'          => 'Flotten',
        'spy_defense'         => 'Verteidigung',
        'spy_research'        => 'Forschung',
        'spy_building'        => 'Gebäude',

        // Battle report (brief)
        'battle_attacker'    => 'Angreifer',
        'battle_defender'    => 'Verteidiger',
        'battle_resources'   => 'Rohstoffe',
        'battle_loot'        => 'Beute',
        'battle_debris_new'       => 'Trümmerfeld (neu entstanden)',
        'battle_wreckage_created'  => 'Wrack entstanden',
        'battle_attacker_wreckage' => 'Wrack des Angreifers',
        'battle_repaired'    => 'Tatsächlich repariert',
        'battle_moon_chance' => 'Mondchance',

        // Battle report (full)
        'battle_report'          => 'Kampfbericht',
        'battle_planet'          => 'Planet',
        'battle_fleet_command'   => 'Flottenkommando',
        'battle_from'            => 'Von',
        'battle_tactical_retreat' => 'Taktischer Rückzug',
        'battle_total_loot'      => 'Gesamtbeute',
        'battle_debris'          => 'Trümmerfeld (neu)',
        'battle_recycler'        => 'Recycler',
        'battle_mined_after'     => 'Nach dem Kampf abgebaut',
        'battle_reaper'          => 'Reaper',
        'battle_debris_left'     => 'Trümmerfeld (übrig)',
        'battle_honour_points'   => 'Ehrenpunkte',
        'battle_dishonourable'   => 'Unehrenvoller Kampf',
        'battle_vs'              => 'vs',
        'battle_honourable'      => 'Ehrenvoller Kampf',
        'battle_class'           => 'Klasse',
        'battle_weapons'         => 'Waffen',
        'battle_shields'         => 'Schilde',
        'battle_armour'          => 'Panzerung',
        'battle_combat_ships'    => 'Kampfschiffe',
        'battle_civil_ships'     => 'Zivile Schiffe',
        'battle_defences'        => 'Verteidigungsanlagen',
        'battle_repaired_def'    => 'Reparierte Verteidigungsanlagen',
        'battle_share'           => 'Nachricht teilen',
        'battle_attack'          => 'Angreifen',
        'battle_espionage'       => 'Spionage',
        'battle_delete'          => 'Löschen',
        'battle_favourite'       => 'Als Favorit markieren',
        'battle_hamill'          => 'Ein Leichter Jäger hat vor Kampfbeginn einen Todesstern zerstört!',
        'battle_retreat_tooltip'  => 'Bitte beachte, dass Todessterne, Spionagesonden, Solarsatelliten und Flotten auf ACS-Verteidigungsmission nicht fliehen können. Taktische Rückzüge sind in ehrenvollen Kämpfen ebenfalls deaktiviert. Ein Rückzug kann auch manuell deaktiviert oder durch Deuteriummangel verhindert worden sein. Banditen und Spieler mit mehr als 500.000 Punkten fliehen niemals.',
        'battle_no_flee'         => 'Die verteidigende Flotte ist nicht geflohen.',
        'battle_rounds'          => 'Runden',
        'battle_start'           => 'Start',
        'battle_player_from'     => 'von',
        'battle_attacker_fires'  => 'Der :attacker feuert insgesamt :hits Schüsse auf den :defender mit einer Gesamtstärke von :strength. Die Schilde des :defender2 absorbieren :absorbed Schadenspunkte.',
        'battle_defender_fires'  => 'Der :defender feuert insgesamt :hits Schüsse auf den :attacker mit einer Gesamtstärke von :strength. Die Schilde des :attacker2 absorbieren :absorbed Schadenspunkte.',
    ],

    // -------------------------------------------------------------------------
    // Alliance page
    // -------------------------------------------------------------------------

    'alliance' => [
        // Page / navigation
        'page_title'                    => 'Allianz',
        'tab_overview'                  => 'Übersicht',
        'tab_management'                => 'Verwaltung',
        'tab_communication'             => 'Kommunikation',
        'tab_applications'              => 'Bewerbungen',
        'tab_classes'                   => 'Allianzklassen',
        'tab_create'                    => 'Allianz gründen',
        'tab_search'                    => 'Allianz suchen',
        'tab_apply'                     => 'Bewerben',

        // Overview – alliance info table
        'your_alliance'                 => 'Deine Allianz',
        'name'                          => 'Name',
        'tag'                           => 'Tag',
        'created'                       => 'Gegründet',
        'member'                        => 'Mitglied',
        'your_rank'                     => 'Dein Rang',
        'homepage'                      => 'Homepage',
        'logo'                          => 'Allianzlogo',
        'open_page'                     => 'Allianzseite öffnen',
        'highscore'                     => 'Allianz-Highscore',
        'leave_wait_warning'            => 'Wenn du die Allianz verlässt, musst du 3 Tage warten, bevor du einer anderen Allianz beitreten oder eine neue gründen kannst.',
        'leave_btn'                     => 'Allianz verlassen',

        // Overview – member list
        'member_list'                   => 'Mitgliederliste',
        'no_members'                    => 'Keine Mitglieder gefunden',
        'assign_rank_btn'               => 'Rang zuweisen',
        'kick_tooltip'                  => 'Allianzmitglied entfernen',
        'write_msg_tooltip'             => 'Nachricht schreiben',
        'col_name'                      => 'Name',
        'col_rank'                      => 'Rang',
        'col_coords'                    => 'Koordinaten',
        'col_joined'                    => 'Beigetreten',
        'col_online'                    => 'Online',
        'col_function'                  => 'Funktion',

        // Overview – text sections
        'internal_area'                 => 'Interner Bereich',
        'external_area'                 => 'Externer Bereich',

        // Management – privileges
        'configure_privileges'          => 'Rechte konfigurieren',
        'col_rank_name'                 => 'Rangname',
        'col_applications_group'        => 'Bewerbungen',
        'col_member_group'              => 'Mitglied',
        'col_alliance_group'            => 'Allianz',
        'delete_rank'                   => 'Rang löschen',
        'save_btn'                      => 'Speichern',
        'rights_warning_html'           => '<strong>Warnung!</strong> Du kannst nur Rechte vergeben, die du selbst besitzt.',
        'rights_warning_loca'           => '[b]Warnung![/b] Du kannst nur Rechte vergeben, die du selbst besitzt.',
        'rights_legend'                 => 'Rechtelegende',
        'create_rank_btn'               => 'Neuen Rang erstellen',
        'rank_name_placeholder'         => 'Rangname',
        'no_ranks'                      => 'Keine Ränge gefunden',

        // Management – permissions (icon titles and legend)
        'perm_see_applications'         => 'Bewerbungen anzeigen',
        'perm_edit_applications'        => 'Bewerbungen bearbeiten',
        'perm_see_members'              => 'Mitgliederliste anzeigen',
        'perm_kick_user'                => 'Benutzer entfernen',
        'perm_see_online'               => 'Online-Status sehen',
        'perm_send_circular'            => 'Rundschreiben verfassen',
        'perm_disband'                  => 'Allianz auflösen',
        'perm_manage'                   => 'Allianz verwalten',
        'perm_right_hand'               => 'Rechte Hand',
        'perm_right_hand_long'          => '`Rechte Hand` (erforderlich, um den Gründertitel zu übertragen)',
        'perm_manage_classes'           => 'Allianzklasse verwalten',

        // Management – texts section
        'manage_texts'                  => 'Texte verwalten',
        'internal_text'                 => 'Interner Text',
        'external_text'                 => 'Externer Text',
        'application_text'              => 'Bewerbungstext',

        // Management – options/settings
        'options'                       => 'Optionen',
        'alliance_logo_label'           => 'Allianzlogo',
        'applications_field'            => 'Bewerbungen',
        'status_open'                   => 'Möglich (Allianz offen)',
        'status_closed'                 => 'Nicht möglich (Allianz geschlossen)',
        'rename_founder'                => 'Gründertitel umbenennen in',
        'rename_newcomer'               => 'Neuling-Rang umbenennen',
        'no_settings_perm'              => 'Du hast keine Berechtigung, die Allianzeinstellungen zu verwalten.',

        // Management – change tag/name
        'change_tag_name'               => 'Allianztag/-name ändern',
        'change_tag'                    => 'Allianztag ändern',
        'change_name'                   => 'Allianzname ändern',
        'former_tag'                    => 'Bisheriger Allianztag:',
        'new_tag'                       => 'Neuer Allianztag:',
        'former_name'                   => 'Bisheriger Allianzname:',
        'new_name'                      => 'Neuer Allianzname:',
        'former_tag_short'              => 'Bisheriger Allianztag',
        'new_tag_short'                 => 'Neuer Allianztag',
        'former_name_short'             => 'Bisheriger Allianzname',
        'new_name_short'                => 'Neuer Allianzname',
        'no_tagname_perm'               => 'Du hast keine Berechtigung, den Allianztag/-namen zu ändern.',

        // Management – disband / pass on
        'delete_pass_on'                => 'Allianz löschen/Allianz übergeben',
        'delete_btn'                    => 'Diese Allianz löschen',
        'no_delete_perm'                => 'Du hast keine Berechtigung, die Allianz zu löschen.',
        'handover'                      => 'Allianz übergeben',
        'takeover_btn'                  => 'Allianz übernehmen',
        'loca_continue'                 => 'Weiter',
        'loca_change_founder'           => 'Den Gründertitel übertragen an:',
        'loca_no_transfer_error'        => 'Keines der Mitglieder hat das erforderliche Recht `Rechte Hand`. Du kannst die Allianz nicht übergeben.',
        'loca_founder_inactive_error'   => 'Der Gründer ist nicht lange genug inaktiv, um die Allianz zu übernehmen.',

        // Management – leave alliance section (non-founders)
        'leave_section_title'           => 'Allianz verlassen',
        'leave_consequences'            => 'Wenn du die Allianz verlässt, verlierst du alle deine Rangberechtigungen und Allianzvorteile.',

        // Applications tab
        'no_applications'               => 'Keine Bewerbungen gefunden',
        'accept_btn'                    => 'Annehmen',
        'deny_btn'                      => 'Bewerber ablehnen',
        'report_btn'                    => 'Bewerbung melden',
        'app_date'                      => 'Bewerbungsdatum',
        'action_col'                    => 'Aktion',
        'answer_btn'                    => 'Antworten',
        'reason_label'                  => 'Grund',

        // Apply page
        'apply_title'                   => 'Bei Allianz bewerben',
        'apply_heading'                 => 'Bewerbung an',
        'send_application_btn'          => 'Bewerbung absenden',
        'chars_remaining'               => 'Verbleibende Zeichen',
        'msg_too_long'                  => 'Nachricht ist zu lang (max. 2000 Zeichen)',

        // Broadcast
        'addressee'                     => 'An',
        'all_players'                   => 'Alle Spieler',
        'only_rank'                     => 'Nur Rang:',
        'send_btn'                      => 'Senden',

        // Info popup
        'info_title'                    => 'Allianzinformationen',
        'apply_confirm'                 => 'Möchtest du dich bei dieser Allianz bewerben?',
        'redirect_confirm'              => 'Wenn du diesem Link folgst, verlässt du OGame. Möchtest du fortfahren?',

        // Classes tab
        'class_selection_header'        => 'Klassenauswahl',
        'select_class_title'            => 'Allianzklasse wählen',
        'select_class_note'             => 'Wähle eine Allianzklasse, um besondere Boni zu erhalten. Du kannst die Allianzklasse im Allianzmenü ändern, sofern du die erforderlichen Berechtigungen besitzt.',
        'class_warriors'                => 'Krieger (Allianz)',
        'class_traders'                 => 'Händler (Allianz)',
        'class_researchers'             => 'Forscher (Allianz)',
        'class_label'                   => 'Allianzklasse',
        'buy_for'                       => 'Kaufen für',
        'no_dark_matter'                => 'Nicht genügend Dunkle Materie verfügbar',
        'loca_deactivate'               => 'Deaktivieren',
        'loca_activate_dm'              => 'Möchtest du die Allianzklasse #allianceClassName# für #darkmatter# Dunkle Materie aktivieren? Dabei verlierst du deine aktuelle Allianzklasse.',
        'loca_activate_item'            => 'Möchtest du die Allianzklasse #allianceClassName# aktivieren? Dabei verlierst du deine aktuelle Allianzklasse.',
        'loca_deactivate_note'          => 'Möchtest du die Allianzklasse #allianceClassName# wirklich deaktivieren? Zur Reaktivierung wird ein Allianzklassenwechsel-Item für 500.000 Dunkle Materie benötigt.',
        'loca_class_change_append'      => '<br><br>Aktuelle Allianzklasse: #currentAllianceClassName#<br><br>Zuletzt geändert am: #lastAllianceClassChange#',
        'loca_no_dm'                    => 'Nicht genügend Dunkle Materie verfügbar! Möchtest du jetzt welche kaufen?',
        'loca_reference'                => 'Hinweis',
        'loca_language'                 => 'Sprache:',
        'loca_loading'                  => 'laden...',
        'warrior_bonus_1'               => '+10% Geschwindigkeit für Schiffe, die zwischen Allianzmitgliedern fliegen',
        'warrior_bonus_2'               => '+1 Kampfforschungsstufen',
        'warrior_bonus_3'               => '+1 Spionageforschungsstufen',
        'warrior_bonus_4'               => 'Das Spionagesystem kann zum Scannen ganzer Systeme verwendet werden.',
        'trader_bonus_1'                => '+10% Geschwindigkeit für Transporter',
        'trader_bonus_2'                => '+5% Minenertrag',
        'trader_bonus_3'                => '+5% Energieproduktion',
        'trader_bonus_4'                => '+10% Planetenlagerkapazität',
        'trader_bonus_5'                => '+10% Mondlagerkapazität',
        'researcher_bonus_1'            => '+5% größere Planeten bei der Kolonisierung',
        'researcher_bonus_2'            => '+10% Geschwindigkeit zum Expeditionsziel',
        'researcher_bonus_3'            => 'Die System-Phalanx kann zum Scannen von Flottenbewegungen in ganzen Systemen verwendet werden.',
        'class_not_implemented'         => 'Allianzklassensystem noch nicht implementiert',

        // Create alliance form
        'create_tag_label'              => 'Allianztag (3-8 Zeichen)',
        'create_name_label'             => 'Allianzname (3-30 Zeichen)',
        'create_btn'                    => 'Allianz gründen',
        'loca_ally_tag_chars'           => 'Allianztag (3-30 Zeichen)',
        'loca_ally_name_chars'          => 'Allianzname (3-8 Zeichen)',
        'loca_ally_name_label'          => 'Allianzname (3-30 Zeichen)',
        'loca_ally_tag_label'           => 'Allianztag (3-8 Zeichen)',
        'validation_min_chars'          => 'Nicht genügend Zeichen',
        'validation_special'            => 'Enthält ungültige Zeichen.',
        'validation_underscore'         => 'Dein Name darf nicht mit einem Unterstrich beginnen oder enden.',
        'validation_hyphen'             => 'Dein Name darf nicht mit einem Bindestrich beginnen oder enden.',
        'validation_space'              => 'Dein Name darf nicht mit einem Leerzeichen beginnen oder enden.',
        'validation_max_underscores'    => 'Dein Name darf nicht mehr als 3 Unterstriche enthalten.',
        'validation_max_hyphens'        => 'Dein Name darf nicht mehr als 3 Bindestriche enthalten.',
        'validation_max_spaces'         => 'Dein Name darf nicht mehr als 3 Leerzeichen enthalten.',
        'validation_consec_underscores' => 'Du darfst nicht zwei oder mehr Unterstriche hintereinander verwenden.',
        'validation_consec_hyphens'     => 'Du darfst nicht zwei oder mehr Bindestriche hintereinander verwenden.',
        'validation_consec_spaces'      => 'Du darfst nicht zwei oder mehr Leerzeichen hintereinander verwenden.',

        // JS confirm dialogs
        'confirm_leave'                 => 'Bist du sicher, dass du die Allianz verlassen möchtest?',
        'confirm_kick'                  => 'Bist du sicher, dass du :username aus der Allianz entfernen möchtest?',
        'confirm_deny'                  => 'Bist du sicher, dass du diese Bewerbung ablehnen möchtest?',
        'confirm_deny_title'            => 'Bewerbung ablehnen',
        'confirm_disband'               => 'Allianz wirklich löschen?',
        'confirm_pass_on'               => 'Bist du sicher, dass du deine Allianz übergeben möchtest?',
        'confirm_takeover'              => 'Bist du sicher, dass du diese Allianz übernehmen möchtest?',
        'confirm_abandon'               => 'Diese Allianz aufgeben?',
        'confirm_takeover_long'         => 'Diese Allianz übernehmen?',

        // Controller / AJAX success & error messages
        'msg_already_in'                => 'Du bist bereits in einer Allianz',
        'msg_not_in_alliance'           => 'Du bist in keiner Allianz',
        'msg_not_found'                 => 'Allianz nicht gefunden',
        'msg_id_required'               => 'Allianz-ID ist erforderlich',
        'msg_closed'                    => 'Diese Allianz ist für Bewerbungen geschlossen',
        'msg_created'                   => 'Allianz erfolgreich gegründet',
        'msg_applied'                   => 'Bewerbung erfolgreich eingereicht',
        'msg_accepted'                  => 'Bewerbung angenommen',
        'msg_rejected'                  => 'Bewerbung abgelehnt',
        'msg_kicked'                    => 'Mitglied aus Allianz entfernt',
        'msg_kicked_success'            => 'Mitglied erfolgreich entfernt',
        'msg_left'                      => 'Du hast die Allianz verlassen',
        'msg_rank_assigned'             => 'Rang zugewiesen',
        'msg_rank_assigned_to'          => 'Rang erfolgreich an :name zugewiesen',
        'msg_ranks_assigned'            => 'Ränge erfolgreich zugewiesen',
        'msg_rank_perms_updated'        => 'Rangberechtigungen aktualisiert',
        'msg_texts_updated'             => 'Allianztexte aktualisiert',
        'msg_text_updated'              => 'Allianztext aktualisiert',
        'msg_settings_updated'          => 'Allianzeinstellungen aktualisiert',
        'msg_tag_updated'               => 'Allianztag aktualisiert',
        'msg_name_updated'              => 'Allianzname aktualisiert',
        'msg_tag_name_updated'          => 'Allianztag und -name aktualisiert',
        'msg_disbanded'                 => 'Allianz aufgelöst',
        'msg_broadcast_sent'            => 'Rundschreiben erfolgreich gesendet',
        'msg_rank_created'              => 'Rang erfolgreich erstellt',
        'msg_apply_success'             => 'Bewerbung erfolgreich eingereicht',
        'msg_apply_error'               => 'Bewerbung konnte nicht eingereicht werden',
        'msg_leave_error'               => 'Allianz konnte nicht verlassen werden',
        'msg_assign_error'              => 'Ränge konnten nicht zugewiesen werden',
        'msg_kick_error'                => 'Mitglied konnte nicht entfernt werden',
        'msg_invalid_action'            => 'Ungültige Aktion',
        'msg_error'                     => 'Ein Fehler ist aufgetreten',
        'rank_founder_default'          => 'Gründer',
        'rank_newcomer_default'         => 'Neuling',
    ],

    // -------------------------------------------------------------------
    // Techtree module
    // -------------------------------------------------------------------
    'techtree' => [
        // Navigation tabs
        'tab_techtree'                          => 'Technologiebaum',
        'tab_applications'                      => 'Verwendung',
        'tab_techinfo'                          => 'Technologieinfo',
        'tab_technology'                        => 'Technologie',

        // Common
        'page_title'                            => 'Technologie',
        'no_requirements'                       => 'Keine Voraussetzungen verfügbar',
        'is_requirement_for'                    => 'ist Voraussetzung für',
        'level'                                 => 'Stufe',

        // Shared table columns
        'col_level'                             => 'Stufe',
        'col_difference'                        => 'Differenz',
        'col_diff_per_level'                    => 'Differenz/Stufe',
        'col_protected'                         => 'Geschützt',
        'col_protected_percent'                 => 'Geschützt (Prozent)',

        // Production table
        'production_energy_balance'             => 'Energiebilanz',
        'production_per_hour'                   => 'Produktion/h',
        'production_deuterium_consumption'      => 'Deuteriumverbrauch',

        // Properties table (ships/defense)
        'properties_technical_data'             => 'Technische Daten',
        'properties_structural_integrity'       => 'Strukturpunkte',
        'properties_shield_strength'            => 'Schildstärke',
        'properties_attack_strength'            => 'Angriffsstärke',
        'properties_speed'                      => 'Geschwindigkeit',
        'properties_cargo_capacity'             => 'Ladekapazität',
        'properties_fuel_usage'                 => 'Treibstoffverbrauch (Deuterium)',

        // Property tooltip
        'tooltip_basic_value'                   => 'Grundwert',

        // Rapidfire
        'rapidfire_from'                        => 'Schnellfeuer von',
        'rapidfire_against'                     => 'Schnellfeuer gegen',

        // Storage table
        'storage_capacity'                      => 'Lagerkapazität',

        // Plasma table
        'plasma_metal_bonus'                    => 'Metallbonus %',
        'plasma_crystal_bonus'                  => 'Kristallbonus %',
        'plasma_deuterium_bonus'                => 'Deuteriumbonus %',

        // Astrophysics table
        'astrophysics_max_colonies'             => 'Maximale Kolonien',
        'astrophysics_max_expeditions'          => 'Maximale Expeditionen',
        'astrophysics_note_1'                   => 'Die Positionen 3 und 13 können ab Stufe 4 besiedelt werden.',
        'astrophysics_note_2'                   => 'Die Positionen 2 und 14 können ab Stufe 6 besiedelt werden.',
        'astrophysics_note_3'                   => 'Die Positionen 1 und 15 können ab Stufe 8 besiedelt werden.',
    ],

    // -------------------------------------------------------------------
    // Options (user settings) module
    // -------------------------------------------------------------------
    'options' => [
        // Page title
        'page_title'                                => 'Einstellungen',

        // Tabs
        'tab_userdata'                              => 'Benutzerdaten',
        'tab_general'                               => 'Allgemein',
        'tab_display'                               => 'Anzeige',
        'tab_extended'                              => 'Erweitert',

        // Tab 1 – Player name
        'section_playername'                        => 'Spielername',
        'your_player_name'                          => 'Dein Spielername:',
        'new_player_name'                           => 'Neuer Spielername:',
        'username_change_once_week'                 => 'Du kannst deinen Benutzernamen einmal pro Woche ändern.',
        'username_change_hint'                      => 'Klicke dazu auf deinen Namen oder die Einstellungen oben auf dem Bildschirm.',

        // Tab 1 – Password
        'section_password'                          => 'Passwort ändern',
        'old_password'                              => 'Altes Passwort eingeben:',
        'new_password'                              => 'Neues Passwort (mindestens 4 Zeichen):',
        'repeat_password'                           => 'Neues Passwort wiederholen:',
        'password_check'                            => 'Passwortprüfung:',
        'password_strength_low'                     => 'Niedrig',
        'password_strength_medium'                  => 'Mittel',
        'password_strength_high'                    => 'Hoch',
        'password_properties_title'                 => 'Das Passwort sollte folgende Eigenschaften aufweisen',
        'password_min_max'                          => 'Min. 4 Zeichen, max. 128 Zeichen',
        'password_mixed_case'                       => 'Groß- und Kleinschreibung',
        'password_special_chars'                    => 'Sonderzeichen (z.B. !?:_., )',
        'password_numbers'                          => 'Zahlen',
        'password_length_hint'                      => 'Dein Passwort muss mindestens <strong>4 Zeichen</strong> lang sein und darf nicht länger als <strong>128 Zeichen</strong> sein.',

        // Tab 1 – Email
        'section_email'                             => 'E-Mail-Adresse',
        'current_email'                             => 'Aktuelle E-Mail-Adresse:',
        'send_validation_link'                      => 'Validierungslink senden',
        'email_sent_success'                        => 'E-Mail wurde erfolgreich gesendet!',
        'email_sent_error'                          => 'Fehler! Konto ist bereits validiert oder die E-Mail konnte nicht gesendet werden!',
        'email_too_many_requests'                   => 'Du hast bereits zu viele E-Mails angefordert!',
        'new_email'                                 => 'Neue E-Mail-Adresse:',
        'new_email_confirm'                         => 'Neue E-Mail-Adresse (zur Bestätigung):',
        'enter_password_confirm'                    => 'Passwort eingeben (zur Bestätigung):',
        'email_warning'                             => 'Achtung! Nach einer erfolgreichen Kontovalidierung ist eine erneute Änderung der E-Mail-Adresse erst nach einer Frist von <b>7 Tagen</b> möglich.',

        // Tab 2 – General
        'section_spy_probes'                        => 'Spionagesonden',
        'spy_probes_amount'                         => 'Anzahl der Spionagesonden:',
        'section_chat'                              => 'Chat',
        'disable_chat_bar'                          => 'Chatleiste deaktivieren:',
        'section_warnings'                          => 'Warnungen',
        'disable_outlaw_warning'                    => 'Vogelfreier-Warnung bei Angriffen auf 5-fach stärkere Gegner deaktivieren:',

        // Tab 3 – Display > General
        'section_general_display'                   => 'Allgemein',
        'language'                                  => 'Sprache:',
        'language_en'                               => 'English',
        'language_de'                               => 'Deutsch',
        'language_it'                               => 'Italiano',
        'language_nl'                               => 'Nederlands',
        'language_ar'                               => 'Español (AR)',
        'language_br'                               => 'Português (BR)',
        'language_cz'                               => 'Čeština',
        'language_dk'                               => 'Dansk',
        'language_es'                               => 'Español',
        'language_fi'                               => 'Suomi',
        'language_fr'                               => 'Français',
        'language_gr'                               => 'Ελληνικά',
        'language_hr'                               => 'Hrvatski',
        'language_hu'                               => 'Magyar',
        'language_jp'                               => '日本語',
        'language_mx'                               => 'Español (MX)',
        'language_pl'                               => 'Polski',
        'language_pt'                               => 'Português',
        'language_ro'                               => 'Română',
        'language_ru'                               => 'Русский',
        'language_se'                               => 'Svenska',
        'language_si'                               => 'Slovenščina',
        'language_sk'                               => 'Slovenčina',
        'language_tr'                               => 'Türkçe',
        'language_tw'                               => '繁體中文',
        'language_us'                               => 'English (US)',
        'language_yu'                               => 'Srpski',
        'msg_language_changed'                      => 'Spracheinstellung gespeichert.',
        'show_mobile_version'                       => 'Mobile Version anzeigen:',
        'show_alt_dropdowns'                        => 'Alternative Dropdowns anzeigen:',
        'activate_autofocus'                        => 'Autofokus im Highscore aktivieren:',
        'always_show_events'                        => 'Ereignisse immer anzeigen:',
        'events_hide'                               => 'Ausblenden',
        'events_above'                              => 'Über dem Inhalt',
        'events_below'                              => 'Unter dem Inhalt',

        // Tab 3 – Display > Planets
        'section_planets'                           => 'Deine Planeten',
        'sort_planets_by'                           => 'Planeten sortieren nach:',
        'sort_emergence'                            => 'Reihenfolge der Entstehung',
        'sort_coordinates'                          => 'Koordinaten',
        'sort_alphabet'                             => 'Alphabet',
        'sort_size'                                 => 'Größe',
        'sort_used_fields'                          => 'Benutzte Felder',
        'sort_sequence'                             => 'Sortierreihenfolge:',
        'sort_order_up'                             => 'Aufsteigend',
        'sort_order_down'                           => 'Absteigend',

        // Tab 3 – Display > Overview
        'section_overview_display'                  => 'Übersicht',
        'highlight_planet_info'                     => 'Planeteninformationen hervorheben:',
        'animated_detail_display'                   => 'Animierte Detailanzeige:',
        'animated_overview'                         => 'Animierte Übersicht:',

        // Tab 3 – Display > Overlays
        'section_overlays'                          => 'Overlays',
        'overlays_hint'                             => 'Die folgenden Einstellungen ermöglichen es, die entsprechenden Overlays in einem zusätzlichen Browserfenster anstatt innerhalb des Spiels zu öffnen.',
        'popup_notes'                               => 'Notizen in einem Extra-Fenster:',
        'popup_combat_reports'                      => 'Kampfberichte in einem Extra-Fenster:',

        // Tab 3 – Display > Messages
        'section_messages_display'                  => 'Nachrichten',
        'hide_report_pictures'                      => 'Bilder in Berichten ausblenden:',
        'msgs_per_page'                             => 'Anzahl angezeigter Nachrichten pro Seite:',
        'auctioneer_notifications'                  => 'Auktionator-Benachrichtigungen:',
        'economy_notifications'                     => 'Wirtschaftsnachrichten erstellen:',

        // Tab 3 – Display > Galaxy
        'section_galaxy_display'                    => 'Galaxie',
        'detailed_activity'                         => 'Detaillierte Aktivitätsanzeige:',
        'preserve_galaxy_system'                    => 'Galaxie/System bei Planetenwechsel beibehalten:',

        // Tab 4 – Extended > Vacation Mode
        'section_vacation'                          => 'Urlaubsmodus',
        'vacation_active'                           => 'Du befindest dich derzeit im Urlaubsmodus.',
        'vacation_can_deactivate_after'             => 'Du kannst ihn deaktivieren nach:',
        'vacation_cannot_activate'                  => 'Urlaubsmodus kann nicht aktiviert werden (Aktive Flotten)',
        'vacation_description_1'                    => 'Der Urlaubsmodus soll dich bei längerer Abwesenheit vom Spiel schützen. Du kannst ihn nur aktivieren, wenn keine deiner Flotten unterwegs ist. Bau- und Forschungsaufträge werden angehalten.',
        'vacation_description_2'                    => 'Sobald der Urlaubsmodus aktiviert ist, bist du vor neuen Angriffen geschützt. Bereits gestartete Angriffe werden jedoch fortgesetzt und deine Produktion wird auf null gesetzt. Der Urlaubsmodus verhindert nicht die Löschung deines Kontos, wenn es 35+ Tage inaktiv war und kein gekaufter DM vorhanden ist.',
        'vacation_description_3'                    => 'Der Urlaubsmodus dauert mindestens 48 Stunden. Erst nach Ablauf dieser Zeit kannst du ihn deaktivieren.',
        'vacation_tooltip_min_days'                 => 'Der Urlaub dauert mindestens 2 Tage.',
        'vacation_deactivate_btn'                   => 'Deaktivieren',
        'vacation_activate_btn'                     => 'Aktivieren',

        // Tab 4 – Extended > Account
        'section_account'                           => 'Dein Konto',
        'delete_account'                            => 'Konto löschen',
        'delete_account_hint'                       => 'Hier markieren, damit dein Konto nach 7 Tagen automatisch gelöscht wird.',

        // Submit
        'use_settings'                              => 'Einstellungen übernehmen',

        // JS validationEngine rules
        'validation_not_enough_chars'               => 'Nicht genügend Zeichen',
        'validation_pw_too_short'                   => 'Das eingegebene Passwort ist zu kurz (min. 4 Zeichen)',
        'validation_pw_too_long'                    => 'Das eingegebene Passwort ist zu lang (max. 20 Zeichen)',
        'validation_invalid_email'                  => 'Du musst eine gültige E-Mail-Adresse eingeben!',
        'validation_special_chars'                  => 'Enthält ungültige Zeichen.',
        'validation_no_begin_end_underscore'        => 'Dein Name darf nicht mit einem Unterstrich beginnen oder enden.',
        'validation_no_begin_end_hyphen'            => 'Dein Name darf nicht mit einem Bindestrich beginnen oder enden.',
        'validation_no_begin_end_whitespace'        => 'Dein Name darf nicht mit einem Leerzeichen beginnen oder enden.',
        'validation_max_three_underscores'          => 'Dein Name darf nicht mehr als 3 Unterstriche enthalten.',
        'validation_max_three_hyphens'              => 'Dein Name darf nicht mehr als 3 Bindestriche enthalten.',
        'validation_max_three_spaces'               => 'Dein Name darf nicht mehr als 3 Leerzeichen enthalten.',
        'validation_no_consecutive_underscores'     => 'Du darfst nicht zwei oder mehr Unterstriche hintereinander verwenden.',
        'validation_no_consecutive_hyphens'         => 'Du darfst nicht zwei oder mehr Bindestriche hintereinander verwenden.',
        'validation_no_consecutive_spaces'          => 'Du darfst nicht zwei oder mehr Leerzeichen hintereinander verwenden.',

        // JS preferenceLoca object
        'js_change_name_title'                      => 'Neuer Spielername',
        'js_change_name_question'                   => 'Bist du sicher, dass du deinen Spielernamen in %newName% ändern möchtest?',
        'js_planet_move_question'                   => 'Achtung! Diese Mission läuft möglicherweise noch, wenn die Umsiedlung beginnt, und wird in diesem Fall abgebrochen. Möchtest du wirklich mit diesem Auftrag fortfahren?',
        'js_tab_disabled'                           => 'Um diese Option zu nutzen, muss dein Konto validiert sein und du darfst dich nicht im Urlaubsmodus befinden!',
        'js_vacation_question'                      => 'Möchtest du den Urlaubsmodus aktivieren? Du kannst deinen Urlaub erst nach 2 Tagen beenden.',

        // Controller messages
        'msg_settings_saved'                        => 'Einstellungen gespeichert',
        'msg_password_incorrect'                    => 'Das eingegebene aktuelle Passwort ist falsch.',
        'msg_password_mismatch'                     => 'Die neuen Passwörter stimmen nicht überein.',
        'msg_password_length_invalid'               => 'Das neue Passwort muss zwischen 4 und 128 Zeichen lang sein.',
        'msg_vacation_activated'                    => 'Der Urlaubsmodus wurde aktiviert. Er schützt dich für mindestens 48 Stunden vor neuen Angriffen.',
        'msg_vacation_deactivated'                  => 'Der Urlaubsmodus wurde deaktiviert.',
        'msg_vacation_min_duration'                 => 'Du kannst den Urlaubsmodus erst nach der Mindestdauer von 48 Stunden deaktivieren.',
        'msg_vacation_fleets_in_transit'            => 'Du kannst den Urlaubsmodus nicht aktivieren, solange Flotten unterwegs sind.',
        'msg_probes_min_one'                        => 'Die Anzahl der Spionagesonden muss mindestens 1 betragen',
    ],

    // -------------------------------------------------------------------------
    // Layout (main.blade.php) — header, menu, resource bar, footer, JS loca
    // -------------------------------------------------------------------------
    'layout' => [
        // Header bar
        'player'                    => 'Spieler',
        'change_player_name'        => 'Spielernamen ändern',
        'highscore'                 => 'Highscore',
        'notes'                     => 'Notizen',
        'notes_overlay_title'       => 'Meine Notizen',
        'buddies'                   => 'Buddys',
        'search'                    => 'Suche',
        'search_overlay_title'      => 'Universum durchsuchen',
        'options'                   => 'Einstellungen',
        'support'                   => 'Support',
        'log_out'                   => 'Logout',
        'unread_messages'           => 'ungelesene Nachricht(en)',
        'loading'                   => 'laden...',
        'no_fleet_movement'         => 'Keine Flottenbewegung',
        'under_attack'              => 'Du wirst angegriffen!',

        // Character class
        'class_none'                => 'Keine Klasse ausgewählt',
        'class_selected'            => 'Deine Klasse: :name',
        'class_click_select'        => 'Klicke, um eine Charakterklasse auszuwählen',

        // Resource bar
        'res_available'             => 'Dein Bestand',
        'res_storage_capacity'      => 'Lagerkapazität',
        'res_current_production'    => 'Aktuelle Produktion',
        'res_den_capacity'          => 'Versteckkapazität',
        'res_consumption'           => 'Verbrauch',
        'res_purchase_dm'           => 'Dunkle Materie kaufen',
        'res_metal'                 => 'Metall',
        'res_crystal'               => 'Kristall',
        'res_deuterium'             => 'Deuterium',
        'res_energy'                => 'Energie',
        'res_dark_matter'           => 'Dunkle Materie',

        // Menu sidebar — item labels
        'menu_overview'             => 'Übersicht',
        'menu_resources'            => 'Versorgung',
        'menu_facilities'           => 'Anlagen',
        'menu_merchant'             => 'Händler',
        'menu_research'             => 'Forschung',
        'menu_shipyard'             => 'Schiffswerft',
        'menu_defense'              => 'Verteidigung',
        'menu_fleet'                => 'Flotte',
        'menu_galaxy'               => 'Galaxie',
        'menu_alliance'             => 'Allianz',
        'menu_officers'             => 'Offizierskasino',
        'menu_shop'                 => 'Shop',
        'menu_directives'           => 'Direktiven',

        // Menu sidebar — icon tooltip titles
        'menu_rewards_title'        => 'Belohnungen',
        'menu_resource_settings_title' => 'Versorgungseinstellungen',
        'menu_jump_gate'            => 'Sprungtor',
        'menu_resource_market_title' => 'Rohstoffmarkt',
        'menu_technology_title'     => 'Technologie',
        'menu_fleet_movement_title' => 'Flottenbewegung',
        'menu_inventory_title'      => 'Inventar',

        // Planet sidebar
        'planets'                   => 'Planeten',

        // Chat bar
        'contacts_online'           => ':count Kontakt(e) online',

        // Scroll button
        'back_to_top'               => 'Nach oben',

        // Footer
        'all_rights_reserved'       => 'Alle Rechte vorbehalten.',
        'patch_notes'               => 'Patchnotizen',
        'server_settings'           => 'Servereinstellungen',
        'help'                      => 'Hilfe',
        'rules'                     => 'Regeln',
        'legal'                     => 'Impressum',
        'board'                     => 'Forum',

        // JS — jsloca
        'js_internal_error'         => 'Ein bisher unbekannter Fehler ist aufgetreten. Leider konnte deine letzte Aktion nicht ausgeführt werden!',
        'js_notify_info'            => 'Info',
        'js_notify_success'         => 'Erfolg',
        'js_notify_warning'         => 'Warnung',
        'js_combatsim_planning'     => 'Planung',
        'js_combatsim_pending'      => 'Simulation läuft...',
        'js_combatsim_done'         => 'Fertig',
        'js_msg_restore'            => 'Wiederherstellen',
        'js_msg_delete'             => 'Löschen',
        'js_copied'                 => 'In die Zwischenablage kopiert',
        'js_report_operator'        => 'Diese Nachricht an einen Spielleiter melden?',

        // JS — LocalizationStrings
        'js_time_done'              => 'Fertig',
        'js_question'               => 'Frage',
        'js_ok'                     => 'Ok',
        'js_outlaw_warning'         => 'Du bist dabei, einen stärkeren Spieler anzugreifen. Wenn du dies tust, wird dein Angriffsschutz für 7 Tage aufgehoben und alle Spieler können dich ohne Strafe angreifen. Bist du sicher, dass du fortfahren möchtest?',
        'js_last_slot_moon'         => 'Dieses Gebäude wird den letzten verfügbaren Bauplatz belegen. Erweitere deine Mondbasis, um mehr Platz zu erhalten. Bist du sicher, dass du dieses Gebäude bauen möchtest?',
        'js_last_slot_planet'       => 'Dieses Gebäude wird den letzten verfügbaren Bauplatz belegen. Erweitere deinen Terraformer oder kaufe ein Planetenfeld-Item, um mehr Plätze zu erhalten. Bist du sicher, dass du dieses Gebäude bauen möchtest?',
        'js_forced_vacation'        => 'Einige Spielfunktionen sind nicht verfügbar, bis dein Konto validiert ist.',
        'js_more_details'           => 'Mehr Details',
        'js_less_details'           => 'Weniger Details',
        'js_planet_lock'            => 'Anordnung sperren',
        'js_planet_unlock'          => 'Anordnung entsperren',
        'js_activate_item_question' => 'Möchtest du den bestehenden Gegenstand ersetzen? Der alte Bonus geht dabei verloren.',
        'js_activate_item_header'   => 'Gegenstand ersetzen?',

        // Welcome dialog
        'welcome_title'             => 'Willkommen bei OGame!',
        'welcome_body'              => 'Damit du schnell loslegen kannst, haben wir dir den Namen Commodore Nebula zugewiesen. Du kannst ihn jederzeit ändern, indem du auf den Benutzernamen klickst.<br/>Das Flottenkommando hat dir Informationen zu deinen ersten Schritten im Posteingang hinterlassen.<br/><br/>Viel Spaß beim Spielen!',

        // Time unit abbreviations (short)
        'time_short_year'            => 'J',
        'time_short_month'           => 'M',
        'time_short_week'            => 'W',
        'time_short_day'             => 'T',
        'time_short_hour'            => 'Std',
        'time_short_minute'          => 'Min',
        'time_short_second'          => 'Sek',

        // Time unit names (long)
        'time_long_day'              => 'Tag',
        'time_long_hour'             => 'Stunde',
        'time_long_minute'           => 'Minute',
        'time_long_second'           => 'Sekunde',

        // Number formatting
        'decimal_point'             => ',',
        'thousand_separator'        => '.',
        'unit_mega'                 => 'Mio',
        'unit_kilo'                 => 'K',
        'unit_milliard'             => 'Mrd',

        // JS — chatLoca
        'chat_text_empty'           => 'Wo ist die Nachricht?',
        'chat_text_too_long'        => 'Die Nachricht ist zu lang.',
        'chat_same_user'            => 'Du kannst dir nicht selbst schreiben.',
        'chat_ignored_user'         => 'Du hast diesen Spieler ignoriert.',
        'chat_not_activated'        => 'Diese Funktion ist erst nach der Aktivierung deines Kontos verfügbar.',
        'chat_new_chats'            => '#+# ungelesene Nachricht(en)',
        'chat_more_users'           => 'Mehr anzeigen',

        // JS — eventboxLoca
        'eventbox_mission'          => 'Mission',
        'eventbox_missions'         => 'Missionen',
        'eventbox_next'             => 'Nächste',
        'eventbox_type'             => 'Typ',
        'eventbox_own'              => 'Eigene',
        'eventbox_friendly'         => 'Freundlich',
        'eventbox_hostile'          => 'Feindlich',

        // JS — planetMoveLoca
        'planet_move_ask_title'     => 'Umsiedeln',
        'planet_move_ask_cancel'    => 'Bist du sicher, dass du diese Planetenumsiedlung abbrechen möchtest? Die normale Wartezeit bleibt dabei bestehen.',
        'planet_move_success'       => 'Die Planetenumsiedlung wurde erfolgreich abgebrochen.',

        // JS — locaPremium
        'premium_building_half'     => 'Möchtest du die Bauzeit um 50% der Gesamtbauzeit () für <b>750 Dunkle Materie<\/b> reduzieren?',
        'premium_building_full'     => 'Möchtest du den Bauauftrag sofort für <b>750 Dunkle Materie<\/b> fertigstellen?',
        'premium_ships_half'        => 'Möchtest du die Bauzeit um 50% der Gesamtbauzeit () für <b>750 Dunkle Materie<\/b> reduzieren?',
        'premium_ships_full'        => 'Möchtest du den Bauauftrag sofort für <b>750 Dunkle Materie<\/b> fertigstellen?',
        'premium_research_half'     => 'Möchtest du die Forschungszeit um 50% der Gesamtforschungszeit () für <b>750 Dunkle Materie<\/b> reduzieren?',
        'premium_research_full'     => 'Möchtest du den Forschungsauftrag sofort für <b>750 Dunkle Materie<\/b> fertigstellen?',

        // JS — loca object
        'loca_error_not_enough_dm'  => 'Nicht genügend Dunkle Materie verfügbar! Möchtest du jetzt welche kaufen?',
        'loca_notice'               => 'Hinweis',
        'loca_planet_giveup'        => 'Bist du sicher, dass du den Planeten %planetName% %planetCoordinates% aufgeben möchtest?',
        'loca_moon_giveup'          => 'Bist du sicher, dass du den Mond %planetName% %planetCoordinates% aufgeben möchtest?',
        'no_ships_in_wreck'         => 'Keine Schiffe im Wrack.',
        'no_wreck_available'        => 'Kein Wrack verfügbar.',
    ],

    // -- Highscore -----------------------------------------------------------
    'highscore' => [
        'player_highscore'      => 'Spieler-Highscore',
        'alliance_highscore'    => 'Allianz-Highscore',
        'own_position'          => 'Eigene Position',
        'own_position_hidden'   => 'Eigene Position (-)',
        'points'                => 'Punkte',
        'economy'               => 'Ökonomie',
        'research'              => 'Forschung',
        'military'              => 'Militär',
        'military_built'        => 'Militärpunkte gebaut',
        'military_destroyed'    => 'Militärpunkte zerstört',
        'military_lost'         => 'Militärpunkte verloren',
        'honour_points'         => 'Ehrenpunkte',
        'position'              => 'Position',
        'player_name_honour'    => 'Spielername (Ehrenpunkte)',
        'action'                => 'Aktion',
        'alliance'              => 'Allianz',
        'member'                => 'Mitglied',
        'average_points'        => 'Durchschnittspunkte',
        'no_alliances_found'    => 'Keine Allianzen gefunden',
        'write_message'         => 'Nachricht schreiben',
        'buddy_request'         => 'Buddyanfrage',
        'buddy_request_to'      => 'Buddyanfrage an',
        'total_ships'           => 'Gesamte Schiffe',
        'buddy_request_sent'    => 'Buddyanfrage erfolgreich gesendet!',
        'buddy_request_failed'  => 'Buddyanfrage konnte nicht gesendet werden.',
        'are_you_sure_ignore'   => 'Bist du sicher, dass du ignorieren möchtest',
        'player_ignored'        => 'Spieler erfolgreich ignoriert!',
        'player_ignored_failed' => 'Spieler konnte nicht ignoriert werden.',
    ],

    // -- Premium / Officers --------------------------------------------------
    'premium' => [
        'recruit_officers'           => 'Offiziere anheuern',
        'your_officers'              => 'Deine Offiziere',
        'intro_text'                 => 'Mit deinen Offizieren kannst du dein Imperium zu einer Größe führen, die deine kühnsten Träume übersteigt - alles was du brauchst, ist etwas Dunkle Materie und deine Arbeiter und Berater werden noch härter arbeiten!',
        'info_dark_matter'           => 'Mehr Informationen über: Dunkle Materie',
        'info_commander'             => 'Mehr Informationen über: Commander',
        'info_admiral'               => 'Mehr Informationen über: Admiral',
        'info_engineer'              => 'Mehr Informationen über: Ingenieur',
        'info_geologist'             => 'Mehr Informationen über: Geologe',
        'info_technocrat'            => 'Mehr Informationen über: Technokrat',
        'info_commanding_staff'      => 'Mehr Informationen über: Kommandostab',
        'hire_commander_tooltip'     => 'Commander anheuern|+40 Favoriten, Bauliste, Shortcuts, Transportscanner, Werbefreiheit* <span style=\'font-size: 10px; line-height: 10px\'>(*ausgenommen: spielbezogene Hinweise)</span>',
        'hire_admiral_tooltip'       => "Admiral anheuern|Max. Flottenanzahl +2,\nMax. Expeditionen +1,\nVerbessertes Flottenfluchtverhältnis,\nKampfsimulation Speicherplätze +20",
        'hire_engineer_tooltip'      => 'Ingenieur anheuern|Halbiert Verluste an Verteidigungsanlagen, +10% Energieproduktion',
        'hire_geologist_tooltip'     => 'Geologen anheuern|+10% Minenertrag',
        'hire_technocrat_tooltip'    => 'Technokraten anheuern|+2 Spionagestufen, 25% weniger Forschungszeit',
        'remaining_officers'         => ':current von :max',
        'benefit_fleet_slots_title'  => 'Du kannst gleichzeitig mehr Flotten entsenden.',
        'benefit_fleet_slots'        => 'Max. Flottenslots +1',
        'benefit_energy_title'       => 'Deine Kraftwerke und Solarsatelliten produzieren 2% mehr Energie.',
        'benefit_energy'             => '+2% Energieproduktion',
        'benefit_mines_title'        => 'Deine Minen produzieren 2% mehr.',
        'benefit_mines'              => '+2% Minenertrag',
        'benefit_espionage_title'    => 'Eine Stufe wird zu deiner Spionageforschung hinzugefügt.',
        'benefit_espionage'          => '+1 Spionagestufen',

        // -- Detail panel / officer purchase ---------------------------------
        'dark_matter_title'          => 'Dunkle Materie',
        'dark_matter_label'          => 'Dunkle Materie',
        'no_dark_matter'             => 'Du hast keine Dunkle Materie verfügbar',
        'dark_matter_description'    => 'Dunkle Materie ist eine seltene Substanz, die nur mit großem Aufwand gelagert werden kann. Sie ermöglicht es dir, große Mengen an Energie zu erzeugen. Der Prozess zur Gewinnung von Dunkler Materie ist komplex und riskant, was sie extrem wertvoll macht.<br><b>Nur gekaufte Dunkle Materie, die noch verfügbar ist, kann vor Kontolöschung schützen!</b>',
        'dark_matter_benefits'       => 'Dunkle Materie ermöglicht es dir, Offiziere und Commander anzuheuern, Händlerangebote zu bezahlen, Planeten umzusiedeln und Gegenstände zu kaufen.',
        'your_balance'               => 'Dein Guthaben',
        'active_until'               => 'Aktiv bis :date',
        'active_for_days'            => 'Noch :days Tage aktiv',
        'not_active'                 => 'Nicht aktiv',
        'days'                       => 'Tage',
        'dm'                         => 'DM',
        'advantages'                 => 'Vorteile:',
        'buy_dark_matter'            => 'Dunkle Materie kaufen',
        'confirm_purchase'           => 'Diesen Offizier für :days Tage zum Preis von :cost Dunkler Materie anheuern?',
        'insufficient_dark_matter'   => 'Du hast nicht genügend Dunkle Materie.',
        'purchase_success'           => 'Offizier erfolgreich aktiviert!',
        'purchase_error'             => 'Ein Fehler ist aufgetreten. Bitte versuche es erneut.',

        // -- Officer titles, descriptions and benefits -----------------------
        'officer_commander_title' => 'Commander',
        'officer_commander_description' => 'Der Commander-Rang hat sich in der modernen Kriegsführung vielfach bewährt. Durch die vereinfachte Befehlsstruktur können Anweisungen schneller ausgeführt werden. Dadurch behältst du den Überblick über dein ganzes Imperium! Somit können Strategien entwickelt werden, die dem Gegner immer einen Schritt voraus sind.',
        'officer_commander_benefits'              => 'Mit dem Commander hast du einen Überblick über das gesamte Imperium, einen zusätzlichen Missionsslot und die Möglichkeit, die Reihenfolge der geplünderten Rohstoffe festzulegen.',
        'officer_commander_benefit_favourites' => '+40 Favoriten',
        'officer_commander_benefit_queue' => 'Bauliste',
        'officer_commander_benefit_scanner' => 'Transportscanner',
        'officer_commander_benefit_ads' => 'Werbefreiheit',
        'officer_commander_tooltip' => '<b>+40 Favoriten</b><p>Mit mehr Favoriten lassen sich mehr Nachrichten speichern, die dann auch geteilt werden können.</p><br/><b>Bauliste</b><p>Stelle bis zu 4 zusätzliche Gebäude- oder Forschungsaufträge auf einmal in die Schleife.</p><br/><b>Transportscanner</b><p>Es wird die Anzahl an Rohstoffen angezeigt, die Transporter auf deine Planeten bringen.</p><br/><b>Werbefreiheit</b><p>Du bekommst keine Werbung mehr für andere Spiele eingeblendet, sondern nur noch Hinweise auf Events und Aktionen, die mit OGame zu tun haben.</p><br/><b>Forschungsüberblick</b><p>Im Forschungsmenü wird die Gesamtstufe aller Forschungslabore in deinem Intergalaktischen Forschungsnetzwerk angezeigt.</p>',

        'officer_admiral_title' => 'Admiral',
        'officer_admiral_description' => 'Der Flottenadmiral ist ein kriegserfahrener Veteran und meisterhafter Stratege. Auch im heißesten Gefecht behält er im Gefechtsleitstand den Überblick und hält Kontakt zu den ihm unterstellten Admirälen. Ein weiser Herrscher kann sich auf seine Unterstützung im Kampf absolut verlassen und somit mehr Raumflotten gleichzeitig ins Gefecht führen. Er ermöglicht einen weiteren Expeditions- Slot und kann festlegen, welche Ressourcen nach einem Angriff zuerst eingeladen werden sollen. Außerdem verleiht er zwanzig weitere Speicherplätze für Kampfsimulationen.',
        'officer_admiral_benefits'                => '+1 Expeditionsslot, Möglichkeit Rohstoffprioritäten nach einem Angriff festzulegen, +20 Kampfsimulator-Speicherplätze.',
        'officer_admiral_benefit_fleet_slots' => 'Max. Flottenanzahl +2',
        'officer_admiral_benefit_expeditions' => 'Max. Expeditionen +1',
        'officer_admiral_benefit_escape' => 'Verbessertes Flottenfluchtverhältnis',
        'officer_admiral_benefit_save_slots' => 'Max. Speicherplätze +20',
        'officer_admiral_tooltip' => '<b>Max. Flottenanzahl +2</b><p>Du kannst mehr Flotten gleichzeitig verschicken.</p><br/><b>Max. Expeditionen +1</b><p>Du bekommst einen zusätzlichen Expeditions- Slot.</p><br/><b>Verbessertes Flottenfluchtverhältnis</b><p>Bis du 500.000 Punkte erreicht hast, kann deine Flotte bei einer Übermacht im Verhältnis von 3 zu 1 fliehen.</p><br/><b>Max. Speicherplätze +20</b><p>Du kannst mehr Kampfsimulationen gleichzeitig speichern.</p>',

        'officer_engineer_title' => 'Ingenieur',
        'officer_engineer_description' => 'Der Ingenieur ist ein Spezialist für Energiemanagement. In Friedenszeiten erhöht er den Wirkungsgrad der Energienetze der Kolonien. Im Fall eines Angriffs gewährleistet er die Versorgung energiekritischer Systeme in den planetaren Geschützen und verhindert Überlastungen, was zu einer deutlich verringerten Rate an Totalverlusten im Gefecht führt.',
        'officer_engineer_benefits'               => '+10% Energie auf allen Planeten, 50% der zerstörten Verteidigungsanlagen überleben den Kampf.',
        'officer_engineer_benefit_defence' => 'Halbiert Verluste an Verteidigungsanlagen',
        'officer_engineer_benefit_energy' => '+10% Energieproduktion',
        'officer_engineer_tooltip' => '<b>Halbiert Verluste an Verteidigungsanlagen</b><p>Nach einem Kampf werden die Hälfte der verlorenen Verteidigungsanlagen wiederhergestellt.</p><br/><b>+10% Energieproduktion</b><p>Deine Kraftwerke und Solarsatelliten erzeugen 10% mehr Energie.</p>',

        'officer_geologist_title' => 'Geologe',
        'officer_geologist_description' => 'Der Geologe ist ein anerkannter Experte in Astromineralogie und -kristallographie. Mithilfe seines Teams aus Metallurgen und Chemieingenieuren unterstützt er interplanetarische Regierungen bei der Erschließung neuer Rohstoffquellen und der Optimierung ihrer Raffination.',
        'officer_geologist_benefits'              => '+10% Produktion von Metall, Kristall und Deuterium auf allen Planeten.',
        'officer_geologist_benefit_mines' => '+10% Minenertrag',
        'officer_geologist_tooltip' => '<b>+10% Minenertrag</b><p>Deine Minen produzieren 10% mehr.</p>',

        'officer_technocrat_title' => 'Technokrat',
        'officer_technocrat_description' => 'Die Gilde der Technokraten sind geniale Wissenschaftler. Man findet sie immer dort, wo die Grenzen des technisch Machbaren gesprengt werden. Kein normaler Mensch knackt je den Chiffrierungscode eines Technokraten und durch ihre reine Anwesenheit inspirieren diese Genies die Forscher des Imperiums.',
        'officer_technocrat_benefits'             => '-25% Forschungszeit für alle Technologien.',
        'officer_technocrat_benefit_espionage' => '+2 Spionagestufen',
        'officer_technocrat_benefit_research' => '25% weniger Forschungszeit',
        'officer_technocrat_tooltip' => '<b>+2 Spionagestufen</b><p>Es werden 2 Stufen zu deiner Spionageforschung hinzugefügt.</p><br/><b>25% weniger Forschungszeit</b><p>Deine Forschungen benötigen 25% weniger Zeit bis zur Fertigstellung.</p>',

        'officer_all_officers_title' => 'Kommandostab',
        'officer_all_officers_description' => 'Mit dem Bundle holst du dir nicht nur einen Spezialisten, sondern gleich eine ganze Crew an Bord. Du erhältst alle Effekte der einzelnen Offiziere sowie zusätzliche Vorteile, die nur das Gesamtpaket gewährt.\nWährend der strategisch versierte Commander die Übersicht behält, kümmern sich die Offiziere um Energiemanagement, Systemversorgung, Rohstofferschließung und Raffination. Weiterhin treiben sie die Forschungen voran und bringen ihre Kriegserfahrungen in Raumschlachten ein.',
        'officer_all_officers_benefits'           => 'Alle Vorteile von Commander, Admiral, Ingenieur, Geologe und Technokrat, plus exklusive Zusatzboni, die nur mit dem Komplettpaket verfügbar sind.',
        'officer_all_officers_benefit_fleet_slots' => 'Max. Flottenanzahl +1',
        'officer_all_officers_benefit_energy' => '+2% Energieproduktion',
        'officer_all_officers_benefit_mines' => '+2% Minenertrag',
        'officer_all_officers_benefit_espionage' => '+1 Spionagestufen',
        'officer_all_officers_tooltip' => '<b>Max. Flottenanzahl +1</b><p>Du kannst mehr Flotten gleichzeitig verschicken.</p><br/><b>+2% Energieproduktion</b><p>Deine Kraftwerke und Solarsatelliten erzeugen 2% mehr Energie.</p><br/><b>+2% Minenertrag</b><p>Deine Minen produzieren 2% mehr.</p><br/><b>+1 Spionagestufen</b><p>Es werden 1 Stufen zu deiner Spionageforschung hinzugefügt.</p>',
    ],

    // -- Shop ----------------------------------------------------------------
    'shop' => [
        'page_title'               => 'Shop',
        'tooltip_shop'             => 'Hier kannst du Gegenstände kaufen.',
        'tooltip_inventory'        => 'Hier erhältst du einen Überblick über deine gekauften Gegenstände.',
        'btn_shop'                 => 'Shop',
        'btn_inventory'            => 'Inventar',
        'category_special_offers'  => 'Sonderangebote',
        'category_all'             => 'Alle',
        'category_resources'       => 'Rohstoffe',
        'category_buddy_items'     => 'Buddy-Gegenstände',
        'category_construction'    => 'Bau',
        'btn_get_more_resources'   => 'Mehr Rohstoffe erhalten',
        'btn_purchase_dark_matter' => 'Dunkle Materie kaufen',
        'feature_coming_soon'      => 'Funktion kommt bald.',
        // Item tiers
        'tier_gold'                => 'Gold',
        'tier_silver'              => 'Silber',
        'tier_bronze'              => 'Bronze',
        // Tooltip labels inside item cards
        'tooltip_duration'         => 'Dauer',
        'duration_now'             => 'sofort',
        'tooltip_price'            => 'Preis',
        'tooltip_in_inventory'     => 'Im Inventar',
        'dark_matter'              => 'Dunkle Materie',
        'dm_abbreviation'          => 'DM',
        'item_duration'            => 'Dauer',
        'now'                      => 'sofort',
        'item_price'               => 'Preis',
        'item_in_inventory'        => 'Im Inventar',
        // JS loca keys (consumed by inventory.js)
        'loca_extend'              => 'Verlängern',
        'loca_activate'            => 'Aktivieren',
        'loca_buy_activate'        => 'Kaufen und aktivieren',
        'loca_buy_extend'          => 'Kaufen und verlängern',
        'loca_buy_dm'              => 'Du hast nicht genügend Dunkle Materie. Möchtest du jetzt welche kaufen?',
    ],

    // -------------------------------------------------------------------------
    // Search overlay
    // -------------------------------------------------------------------------

    'search' => [
        'input_hint'              => 'Spieler-, Allianz- oder Planetennamen eingeben',
        'search_btn'              => 'Suchen',
        'tab_players'             => 'Spielernamen',
        'tab_alliances'           => 'Allianzen/-Tags',
        'tab_planets'             => 'Planetennamen',
        'no_search_term'          => 'Kein Suchbegriff angegeben',
        'searching'               => 'Suche...',
        'search_failed'           => 'Suche fehlgeschlagen. Bitte versuche es erneut.',
        'no_results'              => 'Keine Ergebnisse gefunden',
        'player_name'             => 'Spielername',
        'planet_name'             => 'Planetenname',
        'coordinates'             => 'Koordinaten',
        'tag'                     => 'Tag',
        'alliance_name'           => 'Allianzname',
        'member'                  => 'Mitglied',
        'points'                  => 'Punkte',
        'action'                  => 'Aktion',
        'apply_for_alliance'      => 'Bei dieser Allianz bewerben',
    ],

    // -------------------------------------------------------------------------
    // Notes overlay
    // -------------------------------------------------------------------------

    'notes' => [
        'no_notes_found'      => 'Keine Notizen gefunden',
        'add_note'            => 'Notiz hinzufügen',
        'new_note'            => 'Neue Notiz',
        'subject_label'       => 'Betreff',
        'date_label'          => 'Datum',
        'edit_note'           => 'Notiz bearbeiten',
        'select_action'       => 'Aktion wählen',
        'delete_marked'       => 'Markierte löschen',
        'delete_all'          => 'Alle löschen',
        'unsaved_warning'     => 'Du hast ungespeicherte Änderungen.',
        'save_question'       => 'Möchtest du deine Änderungen speichern?',
        'your_subject'        => 'Betreff',
        'subject_placeholder' => 'Betreff eingeben...',
        'priority_label'      => 'Priorität',
        'priority_important'  => 'Wichtig',
        'priority_normal'     => 'Normal',
        'priority_unimportant'=> 'Unwichtig',
        'your_message'        => 'Nachricht',
        'save_btn'            => 'Speichern',
    ],

    // -------------------------------------------------------------------------
    // Planet abandon / rename overlay
    // -------------------------------------------------------------------------

    'planet_abandon' => [
        // Page description
        'description'                   => 'In diesem Menü kannst du Planetennamen und Monde ändern oder sie vollständig aufgeben.',

        // Rename section
        'rename_heading'                => 'Umbenennen',
        'new_planet_name'               => 'Neuer Planetenname',
        'new_moon_name'                 => 'Neuer Mondname',
        'rename_btn'                    => 'Umbenennen',

        // Tooltips (HTML content)
        'tooltip_rules_title'           => 'Regeln',
        'tooltip_rename_planet'         => 'Du kannst deinen Planeten hier umbenennen.<br /><br />Der Planetenname muss zwischen <span style="font-weight: bold;">2 und 20 Zeichen</span> lang sein.<br />Planetennamen dürfen aus Groß- und Kleinbuchstaben sowie Zahlen bestehen.<br />Sie dürfen Bindestriche, Unterstriche und Leerzeichen enthalten - diese dürfen jedoch nicht wie folgt platziert werden:<br />- am Anfang oder Ende des Namens<br />- direkt nebeneinander<br />- mehr als dreimal im Namen',
        'tooltip_rename_moon'           => 'Du kannst deinen Mond hier umbenennen.<br /><br />Der Mondname muss zwischen <span style="font-weight: bold;">2 und 20 Zeichen</span> lang sein.<br />Mondnamen dürfen aus Groß- und Kleinbuchstaben sowie Zahlen bestehen.<br />Sie dürfen Bindestriche, Unterstriche und Leerzeichen enthalten - diese dürfen jedoch nicht wie folgt platziert werden:<br />- am Anfang oder Ende des Namens<br />- direkt nebeneinander<br />- mehr als dreimal im Namen',

        // Abandon section headings
        'abandon_home_planet'           => 'Heimatplanet aufgeben',
        'abandon_moon'                  => 'Mond aufgeben',
        'abandon_colony'                => 'Kolonie aufgeben',
        'abandon_home_planet_btn'       => 'Heimatplanet aufgeben',
        'abandon_moon_btn'              => 'Mond aufgeben',
        'abandon_colony_btn'            => 'Kolonie aufgeben',

        // Abandon warnings
        'home_planet_warning'           => 'Wenn du deinen Heimatplaneten aufgibst, wirst du bei deinem nächsten Login sofort zu dem Planeten weitergeleitet, den du als nächstes kolonisiert hast.',
        'items_lost_moon'               => 'Wenn du Gegenstände auf einem Mond aktiviert hast, gehen diese verloren, wenn du den Mond aufgibst.',
        'items_lost_planet'             => 'Wenn du Gegenstände auf einem Planeten aktiviert hast, gehen diese verloren, wenn du den Planeten aufgibst.',

        // Abandon confirm form
        'confirm_password'              => 'Bitte bestätige die Löschung von :type [:coordinates] durch Eingabe deines Passworts',
        'confirm_btn'                   => 'Bestätigen',
        'type_moon'                     => 'Mond',
        'type_planet'                   => 'Planet',

        // Validation messages (JS)
        'validation_min_chars'          => 'Nicht genügend Zeichen',
        'validation_pw_min'             => 'Das eingegebene Passwort ist zu kurz (min. 4 Zeichen)',
        'validation_pw_max'             => 'Das eingegebene Passwort ist zu lang (max. 20 Zeichen)',
        'validation_email'              => 'Du musst eine gültige E-Mail-Adresse eingeben!',
        'validation_special'            => 'Enthält ungültige Zeichen.',
        'validation_underscore'         => 'Dein Name darf nicht mit einem Unterstrich beginnen oder enden.',
        'validation_hyphen'             => 'Dein Name darf nicht mit einem Bindestrich beginnen oder enden.',
        'validation_space'              => 'Dein Name darf nicht mit einem Leerzeichen beginnen oder enden.',
        'validation_max_underscores'    => 'Dein Name darf nicht mehr als 3 Unterstriche enthalten.',
        'validation_max_hyphens'        => 'Dein Name darf nicht mehr als 3 Bindestriche enthalten.',
        'validation_max_spaces'         => 'Dein Name darf nicht mehr als 3 Leerzeichen enthalten.',
        'validation_consec_underscores' => 'Du darfst nicht zwei oder mehr Unterstriche hintereinander verwenden.',
        'validation_consec_hyphens'     => 'Du darfst nicht zwei oder mehr Bindestriche hintereinander verwenden.',
        'validation_consec_spaces'      => 'Du darfst nicht zwei oder mehr Leerzeichen hintereinander verwenden.',

        // Controller messages
        'msg_invalid_planet_name'       => 'Der neue Planetenname ist ungültig. Bitte versuche es erneut.',
        'msg_invalid_moon_name'         => 'Der neue Mondname ist ungültig. Bitte versuche es erneut.',
        'msg_planet_renamed'            => 'Planet erfolgreich umbenannt.',
        'msg_moon_renamed'              => 'Mond erfolgreich umbenannt.',
        'msg_wrong_password'            => 'Falsches Passwort!',
        'msg_confirm_title'             => 'Bestätigen',
        'msg_confirm_deletion'          => 'Wenn du die Löschung von :type [:coordinates] (:name) bestätigst, werden alle Gebäude, Schiffe und Verteidigungsanlagen auf diesem :type von deinem Konto entfernt. Wenn du Gegenstände auf deinem :type aktiviert hast, gehen diese ebenfalls verloren. Dieser Vorgang kann nicht rückgängig gemacht werden!',
        'msg_reference'                 => 'Hinweis',
        'msg_abandoned'                 => ':type wurde erfolgreich aufgegeben!',
        'msg_type_moon'                 => 'Mond',
        'msg_type_planet'               => 'Planet',
        'msg_yes'                       => 'Ja',
        'msg_no'                        => 'Nein',
        'msg_ok'                        => 'Ok',
    ],

    // -------------------------------------------------------------------------
    // AJAX object overlay (object.blade.php) — building/ship/research detail panel
    // -------------------------------------------------------------------------
    'ajax_object' => [
        'open_techtree'            => 'Technologiebaum öffnen',
        'techtree'                 => 'Technologiebaum',
        'no_requirements'          => 'Keine Voraussetzungen',
        'cancel_expansion_confirm' => 'Möchtest du den Ausbau von :name auf Stufe :level abbrechen?',
        'number'                   => 'Anzahl',
        'level'                    => 'Stufe',
        'production_duration'      => 'Produktionszeit',
        'energy_needed'            => 'Energiebedarf',
        'production'               => 'Produktion',
        'costs_per_piece'          => 'Kosten pro Einheit',
        'required_to_improve'      => 'Benötigt zum Ausbau auf Stufe',
        'metal'                    => 'Metall',
        'crystal'                  => 'Kristall',
        'deuterium'                => 'Deuterium',
        'energy'                   => 'Energie',
        'deconstruction_costs'     => 'Abrisskosten',
        'ion_technology_bonus'     => 'Ionentechnik-Bonus',
        'duration'                 => 'Dauer',
        'number_label'             => 'Menge',
        'max_btn'                  => 'Max. :amount',
        'vacation_mode'            => 'Du befindest dich derzeit im Urlaubsmodus.',
        'tear_down_btn'            => 'Abreißen',
        'wrong_character_class'    => 'Falsche Charakterklasse!',
        'shipyard_upgrading'       => 'Raumschiffswerft wird ausgebaut.',
        'shipyard_busy'            => 'Die Raumschiffswerft ist derzeit beschäftigt.',
        'not_enough_fields'        => 'Nicht genügend Planetenfelder!',
        'build'                    => 'Bauen',
        'in_queue'                 => 'In der Bauliste',
        'improve'                  => 'Ausbauen',
        'storage_capacity'         => 'Lagerkapazität',
        'gain_resources'           => 'Rohstoffe erhalten',
        'view_offers'              => 'Angebote ansehen',
        'destroy_rockets_desc'     => 'Hier kannst du gelagerte Raketen zerstören.',
        'destroy_rockets_btn'      => 'Raketen zerstören',
        'more_details'             => 'Mehr Details',
        'error'                    => 'Fehler',
        'commander_queue_info'     => 'Du benötigst einen Commander, um die Bauliste zu nutzen. Möchtest du mehr über die Vorteile des Commanders erfahren?',
        'no_rocket_silo_capacity'  => 'Nicht genügend Platz im Raketensilo.',
        'detail_now'               => 'Details',
        'start_with_dm'            => 'Mit Dunkler Materie starten',
        'err_dm_price_too_low'     => 'Der Preis in Dunkler Materie ist zu niedrig.',
        'err_resource_limit'       => 'Rohstofflimit überschritten.',
        'err_storage_capacity'     => 'Nicht genügend Lagerkapazität.',
        'err_no_dark_matter'       => 'Nicht genügend Dunkle Materie.',
    ],

    // -------------------------------------------------------------------------
    // Build queue widget (building-active, research-active, unit-active)
    // -------------------------------------------------------------------------
    'buildqueue' => [
        'building_duration'        => 'Bauzeit',
        'total_time'               => 'Gesamtzeit',
        'complete_tooltip'         => 'Diesen Bau sofort mit Dunkler Materie fertigstellen',
        'complete'                 => 'Jetzt fertigstellen',
        'halve_cost'               => ':amount',
        'halve_tooltip_building'   => 'Verbleibende Bauzeit mit Dunkler Materie halbieren',
        'halve_tooltip_research'   => 'Verbleibende Forschungszeit mit Dunkler Materie halbieren',
        'halve_time'               => 'Zeit halbieren',
        'question_complete_unit'   => 'Möchtest du diesen Einheitenbau sofort für :dm_cost Dunkle Materie fertigstellen?',
        'question_halve_unit'      => 'Möchtest du die Bauzeit um :time_reduction für :dm_cost reduzieren?',
        'question_halve_building'  => 'Möchtest du die Bauzeit für :dm_cost halbieren?',
        'question_halve_research'  => 'Möchtest du die Forschungszeit für :dm_cost halbieren?',
        'downgrade_to'             => 'Rückbau auf',
        'improve_to'               => 'Ausbau auf',
        'no_building_idle'         => 'Derzeit wird kein Gebäude gebaut.',
        'no_building_idle_tooltip' => 'Klicken, um zur Gebäudeseite zu gelangen.',
        'no_research_idle'         => 'Derzeit wird nicht geforscht.',
        'no_research_idle_tooltip' => 'Klicken, um zur Forschungsseite zu gelangen.',
    ],

    // -------------------------------------------------------------------------
    // Chat panel (chat/index.blade.php)
    // -------------------------------------------------------------------------
    'chat' => [
        'buddy_tooltip'      => 'Buddy',
        'alliance_tooltip'   => 'Allianzmitglied',
        'status_online'      => 'Online',
        'status_offline'     => 'Offline',
        'status_not_visible' => 'Status nicht sichtbar',
        'highscore_ranking'  => 'Rang: :rank',
        'alliance_label'     => 'Allianz: :alliance',
        'planet_alt'         => 'Planet',
        'no_messages_yet'    => 'Noch keine Nachrichten.',
        'submit'             => 'Senden',
        'alliance_chat'      => 'Allianz-Chat',
        'list_title'         => 'Unterhaltungen',
        'player_list'        => 'Spieler',
        'buddies'            => 'Buddys',
        'no_buddies'         => 'Noch keine Buddys.',
        'alliance'           => 'Allianz',
        'strangers'          => 'Andere Spieler',
        'no_strangers'       => 'Keine anderen Spieler.',
        'no_conversations'   => 'Noch keine Unterhaltungen.',
    ],

    // -------------------------------------------------------------------------
    // Jump gate dialog (jumpgate/dialog.blade.php)
    // -------------------------------------------------------------------------
    'jumpgate' => [
        'select_target'      => 'Ziel auswählen',
        'origin_coordinates' => 'Abflugort',
        'standard_target'    => 'Standardziel',
        'target_coordinates' => 'Zielkoordinaten',
        'not_ready'          => 'Sprungtor ist nicht bereit.',
        'cooldown_time'      => 'Abklingzeit',
        'select_ships'       => 'Schiffe auswählen',
        'select_all'         => 'Alle auswählen',
        'reset_selection'    => 'Auswahl zurücksetzen',
        'jump_btn'           => 'Springen',
        'ok_btn'             => 'OK',
        'valid_target'       => 'Bitte wähle ein gültiges Ziel.',
        'no_ships'           => 'Bitte wähle mindestens ein Schiff.',
        'jump_success'       => 'Sprung erfolgreich ausgeführt.',
        'jump_error'         => 'Sprung fehlgeschlagen.',
        'error_occurred'     => 'Ein Fehler ist aufgetreten.',
    ],

    // -------------------------------------------------------------------------
    // Server settings overlay (serversettings/overlay.blade.php)
    // -------------------------------------------------------------------------
    'serversettings_overlay' => [
        'acs_enabled'         => 'Allianz-Kampfsystem',
        'dm_bonus'            => 'Dunkle-Materie-Bonus:',
        'debris_defense'      => 'Trümmer von Verteidigung:',
        'debris_ships'        => 'Trümmer von Schiffen:',
        'debris_deuterium'    => 'Deuterium in Trümmerfeldern',
        'fleet_deut_reduction'=> 'Flotten-Deuteriumreduktion:',
        'fleet_speed_war'     => 'Flottengeschwindigkeit (Krieg):',
        'fleet_speed_holding' => 'Flottengeschwindigkeit (Halten):',
        'fleet_speed_peace'   => 'Flottengeschwindigkeit (Frieden):',
        'ignore_empty'        => 'Leere Systeme ignorieren',
        'ignore_inactive'     => 'Inaktive Systeme ignorieren',
        'num_galaxies'        => 'Anzahl der Galaxien:',
        'planet_field_bonus'  => 'Planetenfeld-Bonus:',
        'dev_speed'           => 'Wirtschaftsgeschwindigkeit:',
        'research_speed'      => 'Forschungsgeschwindigkeit:',
        'dm_regen_enabled'    => 'Dunkle-Materie-Regeneration',
        'dm_regen_amount'     => 'DM-Regenerationsmenge:',
        'dm_regen_period'     => 'DM-Regenerationsperiode:',
        'days'                => 'Tage',
    ],

    // -------------------------------------------------------------------------
    // Alliance depot dialog (alliancedepot/dialog.blade.php)
    // -------------------------------------------------------------------------
    'alliance_depot' => [
        'description'         => 'Das Allianzdepot ermöglicht es verbündeten Flotten im Orbit, sich aufzutanken, während sie deinen Planeten verteidigen. Jede Stufe stellt 10.000 Deuterium pro Stunde bereit.',
        'capacity'            => 'Kapazität',
        'no_fleets'           => 'Derzeit befinden sich keine verbündeten Flotten im Orbit.',
        'fleet_owner'         => 'Flottenbesitzer',
        'ships'               => 'Schiffe',
        'hold_time'           => 'Haltezeit',
        'extend'              => 'Verlängern (Stunden)',
        'supply_cost'         => 'Versorgungskosten (Deuterium)',
        'start_supply'        => 'Flotte versorgen',
        'please_select_fleet' => 'Bitte wähle eine Flotte aus.',
        'hours_between'       => 'Die Stunden müssen zwischen 1 und 32 liegen.',
    ],

    // -------------------------------------------------------------------------
    // Admin panel (admin/serversettings.blade.php + admin/developershortcuts.blade.php)
    // -------------------------------------------------------------------------
    'admin' => [
        // Admin-Menüleiste
        'server_admin_label'           => 'Serveradministration',
        'masquerading_as'              => 'Angemeldet als Benutzer',
        'exit_masquerade'              => 'Identitätswechsel beenden',
        'menu_dev_shortcuts'           => 'Entwickler-Schnellzugriffe',
        'menu_server_settings'         => 'Servereinstellungen',
        'menu_fleet_timing'            => 'Flotten-Timing',
        'menu_server_administration'   => 'Serververwaltung',
        'menu_rules_legal'             => 'Regeln & Impressum',

        // Page title
        'title'                        => 'Servereinstellungen',

        // Sections
        'section_basic'                => 'Grundeinstellungen',
        'section_changes_note'         => 'Hinweis: Die meisten Änderungen erfordern einen Serverneustart.',
        'section_income_note'          => 'Hinweis: Einkommenswerte werden zur Basisproduktion addiert.',
        'section_new_player'           => 'Neuspieler-Einstellungen',
        'section_dm_regen'             => 'Dunkle-Materie-Regeneration',
        'section_relocation'           => 'Planetenumsiedlung',
        'section_alliance'             => 'Allianzeinstellungen',
        'section_battle'               => 'Kampfeinstellungen',
        'section_expedition'           => 'Expeditionseinstellungen',
        'section_expedition_slots'     => 'Expeditionsslots',
        'section_expedition_weights'   => 'Expeditionsergebnis-Gewichtungen',
        'section_highscore'            => 'Highscore-Einstellungen',
        'section_galaxy'               => 'Galaxie-Einstellungen',

        // Basic settings
        'universe_name'                => 'Universums-Name',
        'economy_speed'                => 'Wirtschaftsgeschwindigkeit',
        'research_speed'               => 'Forschungsgeschwindigkeit',
        'fleet_speed_war'              => 'Flottengeschwindigkeit (Krieg)',
        'fleet_speed_holding'          => 'Flottengeschwindigkeit (Halten)',
        'fleet_speed_peaceful'         => 'Flottengeschwindigkeit (Frieden)',
        'planet_fields_bonus'          => 'Planetenfelder-Bonus',

        // Income
        'income_metal'                 => 'Metall-Grundeinkommen',
        'income_crystal'               => 'Kristall-Grundeinkommen',
        'income_deuterium'             => 'Deuterium-Grundeinkommen',
        'income_energy'                => 'Energie-Grundeinkommen',

        // New player
        'registration_planet_amount'   => 'Startplaneten',
        'dm_bonus'                     => 'Dunkle-Materie-Startbonus',

        // DM regeneration
        'dm_regen_description'         => 'Wenn aktiviert, erhalten Spieler alle X Tage Dunkle Materie.',
        'dm_regen_enabled'             => 'DM-Regeneration aktivieren',
        'dm_regen_amount'              => 'DM-Menge pro Periode',
        'dm_regen_period'              => 'Regenerationsperiode (Sekunden)',

        // Relocation
        'relocation_cost'              => 'Umsiedlungskosten (Dunkle Materie)',
        'relocation_duration'          => 'Umsiedlungsdauer (Stunden)',

        // Alliance
        'alliance_cooldown'            => 'Allianz-Beitrittsabklingzeit (Tage)',
        'alliance_cooldown_desc'       => 'Anzahl der Tage, die ein Spieler nach dem Verlassen einer Allianz warten muss, bevor er einer anderen beitreten kann.',

        // Battle
        'battle_engine'                => 'Kampf-Engine',
        'battle_engine_desc'           => 'Wähle die Kampf-Engine für Kampfberechnungen.',
        'acs'                          => 'Allianz-Kampfsystem (ACS)',
        'debris_ships'                 => 'Trümmer von Schiffen (%)',
        'debris_defense'               => 'Trümmer von Verteidigung (%)',
        'debris_deuterium'             => 'Deuterium in Trümmerfeldern',
        'moon_chance'                  => 'Mondentstehungschance (%)',
        'hamill_probability'           => 'Hamill-Wahrscheinlichkeit (%)',

        // Wreck field
        'wreck_min_resources'          => 'Wrack-Mindestressourcen',
        'wreck_min_resources_desc'     => 'Mindestgesamtressourcen in der zerstörten Flotte, damit ein Wrack entsteht.',
        'wreck_min_fleet_pct'          => 'Wrack-Mindestflottenprozentsatz (%)',
        'wreck_min_fleet_pct_desc'     => 'Mindestprozentsatz der zerstörten Angriffsflotte, damit ein Wrack entsteht.',
        'wreck_lifetime'               => 'Wrack-Lebensdauer (Sekunden)',
        'wreck_lifetime_desc'          => 'Wie lange ein Wrack bestehen bleibt, bevor es verschwindet.',
        'wreck_repair_max'             => 'Wrack max. Reparaturprozentsatz (%)',
        'wreck_repair_max_desc'        => 'Maximaler Prozentsatz zerstörter Schiffe, der aus einem Wrack repariert werden kann.',
        'wreck_repair_min'             => 'Wrack min. Reparaturprozentsatz (%)',
        'wreck_repair_min_desc'        => 'Minimaler Prozentsatz zerstörter Schiffe, der aus einem Wrack repariert werden kann.',

        // Expedition slots
        'expedition_slots_desc'        => 'Maximale Anzahl gleichzeitiger Expeditionsflotten.',
        'expedition_bonus_slots'       => 'Expeditions-Bonusslots',
        'expedition_multiplier_res'    => 'Rohstoff-Multiplikator',
        'expedition_multiplier_ships'  => 'Schiffe-Multiplikator',
        'expedition_multiplier_dm'     => 'Dunkle-Materie-Multiplikator',
        'expedition_multiplier_items'  => 'Gegenstände-Multiplikator',

        // Expedition weights
        'expedition_weights_desc'      => 'Relative Wahrscheinlichkeitsgewichtungen für Expeditionsergebnisse. Höhere Werte erhöhen die Wahrscheinlichkeit.',
        'expedition_weights_defaults'  => 'Auf Standardwerte zurücksetzen',
        'expedition_weights_values'    => 'Aktuelle Gewichtungen',
        'weight_ships'                 => 'Schiffe gefunden',
        'weight_resources'             => 'Rohstoffe gefunden',
        'weight_delay'                 => 'Verzögerung',
        'weight_speedup'               => 'Beschleunigung',
        'weight_nothing'               => 'Nichts',
        'weight_black_hole'            => 'Schwarzes Loch',
        'weight_pirates'               => 'Piraten',
        'weight_aliens'                => 'Aliens',
        'weight_dm'                    => 'Dunkle Materie',
        'weight_merchant'              => 'Händler',
        'weight_items'                 => 'Gegenstände',

        // Highscore
        'highscore_admin_visible'      => 'Admin im Highscore anzeigen',
        'highscore_admin_visible_desc' => 'Wenn aktiviert, erscheinen Admin-Konten im Highscore.',

        // Galaxy
        'galaxy_ignore_empty'          => 'Leere Systeme in Galaxieansicht ignorieren',
        'galaxy_ignore_inactive'       => 'Inaktive Systeme in Galaxieansicht ignorieren',
        'galaxy_count'                 => 'Anzahl der Galaxien',

        // Save
        'save'                         => 'Einstellungen speichern',

        // Developer shortcuts
        'dev_title'                    => 'Entwicklertools',
        'dev_masquerade'               => 'Als Benutzer maskieren',
        'dev_username'                 => 'Benutzername',
        'dev_username_placeholder'     => 'Benutzername eingeben...',
        'dev_masquerade_btn'           => 'Maskieren',
        'dev_update_planet'            => 'Planetenressourcen aktualisieren',
        'dev_set_mines'                => 'Minen setzen (max)',
        'dev_set_storages'             => 'Speicher setzen (max)',
        'dev_set_shipyard'             => 'Werft setzen (max)',
        'dev_set_research'             => 'Forschung setzen (max)',
        'dev_add_units'                => 'Einheiten hinzufügen',
        'dev_units_amount'             => 'Menge',
        'dev_light_fighter'            => 'Leichte Jäger',
        'dev_set_building'             => 'Gebäudestufe setzen',
        'dev_level_to_set'             => 'Stufe',
        'dev_set_research_level'       => 'Forschungsstufe setzen',
        'dev_class_settings'           => 'Charakterklasse',
        'dev_disable_free_class'       => 'Kostenlose Klassenwahl deaktivieren',
        'dev_enable_free_class'        => 'Kostenlose Klassenwahl aktivieren',
        'dev_reset_class'              => 'Klasse zurücksetzen',
        'dev_goto_class'               => 'Zur Klassenseite',
        'dev_reset_planet'             => 'Planet zurücksetzen',
        'dev_reset_buildings'          => 'Gebäude zurücksetzen',
        'dev_reset_research'           => 'Forschung zurücksetzen',
        'dev_reset_units'              => 'Einheiten zurücksetzen',
        'dev_reset_resources'          => 'Rohstoffe zurücksetzen',
        'dev_add_resources'            => 'Rohstoffe hinzufügen',
        'dev_resources_desc'           => 'Maximale Rohstoffe zum aktuellen Planeten hinzufügen.',
        'dev_coordinates'              => 'Koordinaten',
        'dev_galaxy'                   => 'Galaxie',
        'dev_system'                   => 'System',
        'dev_position'                 => 'Position',
        'dev_resources_label'          => 'Rohstoffe',
        'dev_update_resources_planet'  => 'Planetenressourcen aktualisieren',
        'dev_update_resources_moon'    => 'Mondressourcen aktualisieren',
        'dev_create_planet_moon'       => 'Planet / Mond erstellen',
        'dev_moon_size'                => 'Mondgröße',
        'dev_debris_amount'            => 'Trümmermenge',
        'dev_x_factor'                 => 'X-Faktor',
        'dev_create_planet'            => 'Planet erstellen',
        'dev_create_moon'              => 'Mond erstellen',
        'dev_delete_planet'            => 'Planet löschen',
        'dev_delete_moon'              => 'Mond löschen',
        'dev_create_debris'            => 'Trümmerfeld erstellen',
        'dev_debris_resources_label'   => 'Rohstoffe im Trümmerfeld',
        'dev_create_debris_btn'        => 'Trümmerfeld erstellen',
        'dev_delete_debris_btn'        => 'Trümmerfeld löschen',
        'dev_quick_shortcut_desc'      => 'Schnelle Shortcuts für Entwicklung und Tests.',
        'dev_create_expedition_debris' => 'Expeditions-Trümmerfeld erstellen',
        'dev_add_dm'                   => 'Dunkle Materie hinzufügen',
        'dev_dm_desc'                  => 'Dunkle Materie zum aktuellen Spielerkonto hinzufügen.',
        'dev_dm_amount'                => 'Menge',
        'dev_update_dm'                => 'Dunkle Materie hinzufügen',
    ],

    // -------------------------------------------------------------------------
    // Character class selection page
    // -------------------------------------------------------------------------

    'characterclass' => [
        'page_title'              => 'Klassenauswahl',
        'choose_your_class'       => 'Wähle deine Klasse',
        'choose_description'      => 'Wähle eine Klasse, um zusätzliche Vorteile zu erhalten. Du kannst deine Klasse im Klassenauswahl-Bereich oben rechts ändern.',
        'select_for_free'         => 'Freies Aktivieren',
        'buy_for'                 => 'Kaufen für',
        'deactivate'              => 'Deaktivieren',
        'confirm'                 => 'Bestätigen',
        'cancel'                  => 'Abbrechen',
        'select_title'            => 'Charakterklasse auswählen',
        'deactivate_title'        => 'Charakterklasse deaktivieren',
        'activated_free_msg'      => 'Möchtest du die Klasse :className kostenlos aktivieren?',
        'activated_paid_msg'      => 'Möchtest du die Klasse :className für :price Dunkle Materie aktivieren? Dabei verlierst du deine aktuelle Klasse.',
        'deactivate_confirm_msg'  => 'Möchtest du deine Charakterklasse wirklich deaktivieren? Zur Reaktivierung werden :price Dunkle Materie benötigt.',
        'success_selected'        => 'Charakterklasse erfolgreich ausgewählt!',
        'success_deactivated'     => 'Charakterklasse erfolgreich deaktiviert!',
        'not_enough_dm_title'     => 'Nicht genügend Dunkle Materie',
        'not_enough_dm_msg'       => 'Nicht genügend Dunkle Materie verfügbar! Möchtest du jetzt welche kaufen?',
        'buy_dm'                  => 'Dunkle Materie kaufen',
        'error_generic'           => 'Ein Fehler ist aufgetreten. Bitte versuche es erneut.',
    ],

    // -------------------------------------------------------------------------
    // Rewards page
    // -------------------------------------------------------------------------

    'rewards' => [
        'page_title'          => 'Belohnungen',
        'hint_tooltip'        => 'Belohnungen werden jeden Tag versendet und können manuell eingesammelt werden. Ab dem 7. Tag werden keine weiteren Belohnungen mehr versendet. Die erste Belohnung gibt es am 2. Tag nach der Registrierung.',
        'new_awards'          => 'Neue Auszeichnungen',
        'not_yet_reached'     => 'Noch nicht erreichte Auszeichnungen',
        'not_fulfilled'       => 'Nicht erfüllt',
        'collected_awards'    => 'Eingesammelte Auszeichnungen',
        'claim'               => 'Einsammeln',
    ],

    // -------------------------------------------------------------------------
    // Phalanx scan overlay
    // -------------------------------------------------------------------------

    'phalanx' => [
        'no_movements'      => 'Keine Flottenbewegungen an dieser Position erkannt.',
        'fleet_details'     => 'Flottendetails',
        'ships'             => 'Schiffe',
        'loading'           => 'Laden...',
        'time_label'        => 'Zeit',
        'speed_label'       => 'Geschwindigkeit',
    ],

    // -------------------------------------------------------------------------
    // Wreckage / Space Dock (facilities page)
    // -------------------------------------------------------------------------

    'wreckage' => [
        'no_wreckage'          => 'An dieser Position befindet sich kein Wrack.',
        'burns_up_in'          => 'Wrack verglüht in:',
        'leave_to_burn'        => 'Verglühen lassen',
        'leave_confirm'        => 'Das Wrack wird in die Atmosphäre des Planeten eintreten und verglühen. Bist du sicher?',
        'repair_time'          => 'Reparaturzeit:',
        'ships_being_repaired' => 'Schiffe werden repariert:',
        'repair_time_remaining'=> 'Verbleibende Reparaturzeit:',
        'no_ship_data'         => 'Keine Schiffsdaten verfügbar',
        'collect'              => 'Einsammeln',
        'start_repairs'        => 'Reparatur starten',
        'err_network_start'    => 'Netzwerkfehler beim Starten der Reparatur',
        'err_network_complete' => 'Netzwerkfehler beim Abschließen der Reparatur',
        'err_network_collect'  => 'Netzwerkfehler beim Einsammeln der Schiffe',
        'err_network_burn'     => 'Netzwerkfehler beim Verglühen des Wracks',
        'err_burn_up'          => 'Fehler beim Verglühen des Wracks',
        'wreckage_label'       => 'Wrack',
        'repairs_started'      => 'Reparatur erfolgreich gestartet!',
        'repairs_completed'    => 'Reparatur abgeschlossen und Schiffe erfolgreich eingesammelt!',
        'ships_back_service'   => 'Alle Schiffe wurden wieder in Dienst gestellt',
        'wreck_burned'         => 'Wrack erfolgreich verglüht!',
        'err_start_repairs'    => 'Fehler beim Starten der Reparatur',
        'err_complete_repairs' => 'Fehler beim Abschließen der Reparatur',
        'err_collect_ships'    => 'Fehler beim Einsammeln der Schiffe',
        'err_burn_wreck'       => 'Fehler beim Verglühen des Wracks',
        'can_be_repaired'      => 'Wracks können im Raumdock repariert werden.',
        'collect_back_service' => 'Bereits reparierte Schiffe wieder in Dienst stellen',
        'auto_return_service'  => 'Deine letzten Schiffe werden automatisch am in Dienst gestellt',
        'no_ships_for_repair'  => 'Keine Schiffe zur Reparatur verfügbar',
        'repairable_ships'     => 'Reparierbare Schiffe:',
        'repaired_ships'       => 'Reparierte Schiffe:',
        'ships_count'          => 'Schiffe',
        'details'              => 'Details',
        'tooltip_late_added'   => 'Während laufender Reparaturen hinzugefügte Schiffe können nicht manuell eingesammelt werden. Du musst warten, bis alle Reparaturen automatisch abgeschlossen sind.',
        'tooltip_in_progress'  => 'Reparaturen sind noch im Gange. Verwende das Detail-Fenster für teilweises Einsammeln.',
        'tooltip_no_repaired'  => 'Noch keine Schiffe repariert',
        'tooltip_must_complete'=> 'Reparaturen müssen abgeschlossen sein, um Schiffe von hier einzusammeln.',
        'burn_confirm_title'   => 'Verglühen lassen',
        'burn_confirm_msg'     => 'Das Wrack wird in die Atmosphäre des Planeten eintreten und verglühen. Danach ist eine Reparatur nicht mehr möglich. Bist du sicher, dass du das Wrack verglühen lassen möchtest?',
        'burn_confirm_yes'     => 'Ja',
        'burn_confirm_no'      => 'Nein',
    ],

    // -------------------------------------------------------------------------
    // Fleet template labels (fleet/index)
    // -------------------------------------------------------------------------

    'fleet_templates' => [
        'name_col'            => 'Name',
        'actions_col'         => 'Aktionen',
        'template_name_label' => 'Name',
        'delete_tooltip'      => 'Vorlage/Eingaben löschen',
        'save_tooltip'        => 'Vorlage speichern',
        'err_name_required'   => 'Vorlagenname ist erforderlich.',
        'err_need_ships'      => 'Vorlage muss mindestens ein Schiff enthalten.',
        'err_not_found'       => 'Vorlage nicht gefunden.',
        'err_max_reached'     => 'Maximale Anzahl an Vorlagen erreicht (10).',
        'saved_success'       => 'Vorlage erfolgreich gespeichert.',
        'deleted_success'     => 'Vorlage erfolgreich gelöscht.',
    ],

    // -------------------------------------------------------------------------
    // Fleet events (eventlist, eventrow)
    // -------------------------------------------------------------------------

    'fleet_events' => [
        'events'              => 'Ereignisse',
        'recall_title'        => 'Zurückrufen',
        'recall_fleet'        => 'Flotte zurückrufen',
    ],
];
