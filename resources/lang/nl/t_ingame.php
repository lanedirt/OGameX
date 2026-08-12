<?php

return [
    // -------------------------------------------------------------------------
    // Overzichtspagina
    // -------------------------------------------------------------------------

    'overview' => [
        // Planeetstatistiekenpaneel (schrijfmachine-animatie)
        'diameter'             => 'Diameter',
        'temperature'          => 'Temperatuur',
        'position'             => 'Positie',
        'points'               => 'Punten',
        'honour_points'        => 'Eerepunten',
        'score_place'          => 'Plaats',
        'score_of'             => 'van',

        // Pagina- / sectiekoppen
        'page_title'           => 'Overzicht',
        'buildings'            => 'Gebouwen',
        'research'             => 'Onderzoek',

        // Planeetkopknoppen
        'switch_to_moon'       => 'Schakel naar maan',
        'switch_to_planet'     => 'Schakel naar planeet',
        'abandon_rename'       => 'Verlaten/Hernoemen',
        'abandon_rename_title' => 'Planeet verlaten/hernoemen',
    ],

    // -------------------------------------------------------------------------
    // Planeetverplaatsing
    // -------------------------------------------------------------------------

    'planet_move' => [
        'resettle_title' => 'Planeet Hervestigen',
        'cancel_confirm' => 'Weet u zeker dat u de planeetverplaatsing wilt annuleren? De gereserveerde positie wordt vrijgegeven.',
        'cancel_success' => 'De planeetverplaatsing is succesvol geannuleerd.',
        'blockers_title' => 'De volgende zaken staan momenteel in de weg van uw planeetverplaatsing:',
        'no_blockers'    => 'Niets kan de geplande verplaatsing van de planeet nu in de weg staan.',
        'cooldown_title' => 'Tijd tot de volgende mogelijke verplaatsing',
        'to_galaxy'      => 'Naar melkweg',
        'relocate'       => 'Verplaatsen',
        'cancel'         => 'annuleren',
        'explanation'    => 'Met de verplaatsing kunt u uw planeten verplaatsen naar een andere positie in een ver systeem naar keuze.<br /><br />De daadwerkelijke verplaatsing vindt voor het eerst 24 uur na activering plaats. In deze tijd kunt u uw planeten normaal gebruiken. Een afteltimer toont hoeveel tijd er resteert voor de verplaatsing.<br /><br />Zodra de afteltimer is verlopen en de planeet moet worden verplaatst, mogen geen van uw vloten die daar gestationeerd zijn actief zijn. Op dit moment mag er ook niets in aanbouw zijn, niets worden gerepareerd en niets worden onderzocht. Als er een bouwactiviteit, een reparatietaak of een vloot nog actief is bij het verstrijken van de afteltimer, wordt de verplaatsing geannuleerd.<br /><br />Als de verplaatsing succesvol is, worden 240.000 Donkere Materie in rekening gebracht. De planeten, de gebouwen en de opgeslagen grondstoffen inclusief maan worden onmiddellijk verplaatst. Uw vloten reizen automatisch naar de nieuwe coördinaten met de snelheid van het langzaamste schip. De sprongpoort naar een verplaatste maan wordt 24 uur gedeactiveerd.',
    ],

    // -------------------------------------------------------------------------
    // Gedeelde UI-strings (knoppen, dialoogvenster-labels)
    // -------------------------------------------------------------------------

    'shared' => [
        'caution' => 'Waarschuwing',
        'yes'     => 'ja',
        'no'      => 'Nee',
        'error'   => 'Fout',
    ],

    // -------------------------------------------------------------------------
    // Gedeelde gebouwenpagina-strings (grondstoffen, structuren, onderzoek, scheepswerf, defensie)
    // -------------------------------------------------------------------------

    'buildings' => [
        // Tooltip gebouwstatus
        'under_construction'     => 'In aanbouw',
        'vacation_mode_error'    => 'Fout, speler is in vakantiemodus',
        'requirements_not_met'   => 'Vereisten zijn niet vervuld!',
        'wrong_class'            => 'Je hebt niet de vereiste spelersklasse voor dit gebouw.',
        'wrong_class_general'    => 'Om dit schip te kunnen bouwen, moet je de klasse Generaal hebben geselecteerd.',
        'wrong_class_collector'  => 'Om dit schip te kunnen bouwen, moet je de klasse Verzamelaar hebben geselecteerd.',
        'wrong_class_discoverer' => 'Om dit schip te kunnen bouwen, moet je de klasse Ontdekker hebben geselecteerd.',
        'no_moon_building'       => 'Je kunt dat gebouw niet op een maan bouwen!',
        'not_enough_resources'   => 'Niet genoeg grondstoffen!',
        'queue_full'             => 'Wachtrij is vol',
        'not_enough_fields'      => 'Niet genoeg velden!',
        'shipyard_busy'          => 'De scheepswerf is nog bezig',
        'research_in_progress'   => 'Er wordt momenteel onderzoek gedaan!',
        'research_lab_expanding' => 'Het onderzoekslaboratorium wordt uitgebreid.',
        'shipyard_upgrading'     => 'De scheepswerf wordt uitgebreid.',
        'nanite_upgrading'       => 'De nanietfabriek wordt uitgebreid.',
        'max_amount_reached'     => 'Maximaal aantal bereikt!',
        // Uitbreidingsknop (benoemde params: :title, :level)
        'expand_button'          => ':title uitbreiden naar niveau :level',
        // JS loca-object strings
        'loca_notice'            => 'Referentie',
        'loca_demolish'          => 'Wilt u TECHNOLOGY_NAME echt met één niveau verlagen?',
        'loca_lifeform_cap'      => 'Een of meer gekoppelde bonussen hebben al het maximum bereikt. Wilt u toch doorgaan met de bouw?',
        'last_inquiry_error'     => 'Uw laatste actie kon niet worden verwerkt. Probeer het opnieuw.',
        'planet_move_warning'    => 'Waarschuwing! Deze missie kan nog actief zijn wanneer de verplaatsingsperiode begint. Als dat zo is, wordt het proces geannuleerd. Wilt u toch doorgaan met deze taak?',
    ],

    // -------------------------------------------------------------------------
    // Grondstoffenpagina (mijnen / opslaggebouwen)
    // -------------------------------------------------------------------------

    'resources_page' => [
        'page_title'    => 'Grondstoffen',
        'settings_link' => 'Grondstoffinstellingen',
        'section_title' => 'Grondstofgebouwen',
    ],

    // -------------------------------------------------------------------------
    // Structurenpagina
    // -------------------------------------------------------------------------

    'facilities_page' => [
        'page_title'     => 'Structuren',
        'section_title'  => 'Structuurgebouwen',
        'use_jump_gate'  => 'Gebruik Sprongpoort',
        'jump_gate'      => 'Sprongpoort',
        'alliance_depot' => 'Alliantiedepot',
        'burn_confirm'   => 'Weet u zeker dat u dit wrakstuk wilt verbranden? Deze actie kan niet ongedaan worden gemaakt.',
    ],

    // -------------------------------------------------------------------------
    // Onderzoekspagina
    // -------------------------------------------------------------------------

    'research_page' => [
        'basic'    => 'Basisonderzoek',
        'drive'    => 'Aandrijvingsonderzoek',
        'advanced' => 'Geavanceerde onderzoeken',
        'combat'   => 'Gevechtsonderzoek',
    ],

    // -------------------------------------------------------------------------
    // Scheepswerfpagina
    // -------------------------------------------------------------------------

    'shipyard_page' => [
        'battleships' => 'Gevechtsschepen',
        'civil_ships' => 'Civiele schepen',
    ],

    // -------------------------------------------------------------------------
    // Defensiepagina
    // -------------------------------------------------------------------------

    'defense_page' => [
        'page_title'    => 'Defensie',
        'section_title' => 'Defensiestructuren',
    ],

    // -------------------------------------------------------------------------
    // Grondstoffinstellingen pagina
    // -------------------------------------------------------------------------

    'resource_settings' => [
        'production_factor'  => 'Productiefactor',
        'recalculate'        => 'Herberekenen',
        'metal'              => 'Metaal',
        'crystal'            => 'Kristal',
        'deuterium'          => 'Deuterium',
        'energy'             => 'Energie',
        'basic_income'       => 'Basisinkomen',
        'level'              => 'Niveau',
        'number'             => 'Aantal:',
        'items'              => 'Items',
        'geologist'          => 'Geoloog',
        'mine_production'    => 'mijnproductie',
        'engineer'           => 'Ingenieur',
        'energy_production'  => 'energieproductie',
        'character_class'    => 'Karakterklasse',
        'commanding_staff'   => 'Commandostaf',
        'storage_capacity'   => 'Opslagcapaciteit',
        'total_per_hour'     => 'Totaal per uur:',
        'total_per_day'      => 'Totaal per dag',
        'total_per_week'     => 'Totaal per week:',
    ],

    // -------------------------------------------------------------------------
    // Dialoog raketten vernietigen (structurenpagina)
    // -------------------------------------------------------------------------

    'facilities_destroy' => [
        'silo_description'  => "Raketensilo's worden gebruikt om interplanetaire en anti-ballistische raketten te bouwen, op te slaan en te lanceren. Per niveau van de silo kunnen vijf interplanetaire raketten of tien anti-ballistische raketten worden opgeslagen. Een interplanetaire raket neemt evenveel ruimte in als twee anti-ballistische raketten. Opslag van zowel interplanetaire als anti-ballistische raketten in dezelfde silo is toegestaan.",
        'silo_capacity'     => 'Een raketensilo op niveau :level kan :ipm interplanetaire raketten of :abm anti-ballistische raketten bevatten.',
        'type'              => 'Type',
        'number'            => 'Aantal',
        'tear_down'         => 'afbreken',
        'proceed'           => 'Doorgaan',
        'enter_minimum'     => 'Voer minimaal één te vernietigen raket in',
        'not_enough_abm'    => 'U heeft niet genoeg anti-ballistische raketten',
        'not_enough_ipm'    => 'U heeft niet genoeg interplanetaire raketten',
        'destroyed_success' => 'Raketten succesvol vernietigd',
        'destroy_failed'    => 'Raketten vernietigen mislukt',
        'error'             => 'Er is een fout opgetreden. Probeer het opnieuw.',
    ],

    // -------------------------------------------------------------------------
    // Vlootpagina's (versturen + beweging)
    // -------------------------------------------------------------------------

    'fleet' => [
        // Pagina / stap koppen
        'dispatch_1_title'         => 'Vloot versturen I',
        'dispatch_2_title'         => 'Vloot versturen II',
        'dispatch_3_title'         => 'Vloot versturen III',
        'movement_title'           => 'Vlootbeweging',
        'to_movement'              => 'Naar vlootbeweging',

        // Statusbalk
        'fleets'                   => 'Vloten',
        'expeditions'              => 'Expedities',
        'reload'                   => 'Herladen',
        'clock'                    => 'Uur',
        'load_dots'                => 'laden...',
        'never'                    => 'Nooit',

        // Vlootslots
        'tooltip_slots'            => 'Gebruikt/Totaal vlootslots',
        'no_free_slots'            => 'Geen vlootslots beschikbaar',
        'tooltip_exp_slots'        => 'Gebruikt/Totaal expeditieslots',
        'market_slots'             => 'Aanbiedingen',
        'tooltip_market_slots'     => 'Gebruikt/Totaal handelsvloten',

        // Waarschuwing / onmogelijke staten
        'fleet_dispatch'           => 'Vloot versturen',
        'dispatch_impossible'      => 'Vloot versturen onmogelijk',
        'no_ships'                 => 'Er zijn geen schepen op deze planeet.',
        'in_combat'                => 'De vloot is momenteel in gevecht.',
        'vacation_error'           => 'Er kunnen geen vloten worden verstuurd vanuit vakantiemodus!',
        'not_enough_deuterium'     => 'Onvoldoende deuterium!',
        'no_target'                => 'U moet een geldig doel selecteren.',
        'cannot_send_to_target'    => 'Vloten kunnen niet naar dit doel worden gestuurd.',
        'cannot_start_mission'     => 'U kunt deze missie niet starten.',

        // Statusbalk labels (zonder afsluitende dubbele punt)
        'mission_label'            => 'Missie',
        'target_label'             => 'Doel',
        'player_name_label'        => 'Spelernaam',
        'no_selection'             => 'Niets geselecteerd',
        'no_mission_selected'      => 'Geen missie geselecteerd!',

        // Stap 1 – scheepsselectie
        'combat_ships'             => 'Gevechtsschepen',
        'civil_ships'              => 'Burgerschepen',
        'standard_fleets'          => 'Standaard vloten',
        'edit_standard_fleets'     => 'Standaard vloten bewerken',
        'select_all_ships'         => 'Alle schepen selecteren',
        'reset_choice'             => 'Selectie resetten',
        'api_data'                 => 'Deze gegevens kunnen worden ingevoerd in een compatibele gevechtssimulator:',
        'tactical_retreat'         => 'Tactische terugtrekking',
        'tactical_retreat_tooltip' => 'Toon deuteriumverbruik per tactische terugtrekking',
        'continue'                 => 'Doorgaan',
        'back'                     => 'Terug',

        // Stap 2 – bestemming
        'origin'                   => 'Herkomst',
        'destination'              => 'Bestemming',
        'planet'                   => 'Planeet',
        'moon'                     => 'Maan',
        'coordinates'              => 'Coördinaten',
        'distance'                 => 'Afstand',
        'debris_field'             => 'Puinveld',
        'debris_field_lower'       => 'puinveld',
        'shortcuts'                => 'Snelkoppelingen',
        'combat_forces'            => 'Strijdkrachten',
        'player_label'             => 'Speler',
        'player_name'              => 'Spelernaam',

        // Stap 3 – missieselectie
        'select_mission'           => 'Selecteer missie voor doel',
        'bashing_disabled'         => 'Aanvalsmissies zijn uitgeschakeld vanwege te veel aanvallen op het doel.',

        // Missienamen
        'mission_expedition'       => 'Expeditie',
        'mission_colonise'         => 'Kolonisatie',
        'mission_recycle'          => 'Puinveld opruimen',
        'mission_transport'        => 'Transport',
        'mission_deploy'           => 'Stationering',
        'mission_espionage'        => 'Spionage',
        'mission_acs_defend'       => 'ACS Verdedigen',
        'mission_attack'           => 'Aanval',
        'mission_acs_attack'       => 'ACS Aanval',
        'mission_destroy_moon'     => 'Maanvernietiging',

        // Missiebeschrijvingen
        'desc_attack'              => 'Valt de vloot en verdediging van uw tegenstander aan.',
        'desc_acs_attack'          => 'Eervolle gevechten kunnen oneerlijk worden als sterke spelers via ACS deelnemen. De som van de totale militaire punten van de aanvaller versus die van de verdediger is de beslissende factor.',
        'desc_transport'           => 'Transporteert uw grondstoffen naar andere planeten.',
        'desc_deploy'              => 'Stuurt uw vloot permanent naar een andere planeet van uw rijk.',
        'desc_acs_defend'          => 'Verdedig de planeet van uw teamgenoot.',
        'desc_espionage'           => 'Bespioneer de werelden van vreemde keizers.',
        'desc_colonise'            => 'Koloniseert een nieuwe planeet.',
        'desc_recycle'             => 'Stuur uw recyclers naar een puinveld om de rondzwevende grondstoffen te verzamelen.',
        'desc_destroy_moon'        => 'Vernietigt de maan van uw vijand.',
        'desc_expedition'          => 'Stuur uw schepen naar de verste uithoeken van de ruimte voor spannende quests.',

        // Briefingsectie (zonder afsluitende dubbele punt)
        'briefing'                 => 'Briefing',
        'load_resources'           => 'Grondstoffen laden',
        'load_all_resources'       => 'Alle grondstoffen laden',
        'all_resources'            => 'alle grondstoffen',
        'flight_duration'          => 'Vluchttijd (enkel)',
        'federation_duration'      => 'Vluchttijd (vlootunie)',
        'arrival'                  => 'Aankomst',
        'return_trip'              => 'Terugkeer',
        'speed'                    => 'Snelheid:',
        'max_abbr'                 => 'max.',
        'hour_abbr'                => 'u',
        'deuterium_consumption'    => 'Deuteriumverbruik',
        'empty_cargobays'          => 'Lege laadruimte',
        'hold_time'                => 'Bezettingstijd',
        'expedition_duration'      => 'Duur van expeditie',
        'cargo_bay'                => 'laadruimte',
        'cargo_space'              => 'Beschikbare ruimte / Max. laadruimte',
        'send_fleet'               => 'Vloot sturen',
        'retreat_on_defender'      => 'Terugtrekken bij verdedigersvlucht',
        'retreat_tooltip'          => 'Als deze optie is geactiveerd, zal uw vloot zich ook zonder gevecht terugtrekken als uw tegenstander vlucht.',
        'plunder_food'             => 'Voedsel plunderen',

        // Grondstoffenlabels (voor loca-object)
        'metal'                    => 'Metaal',
        'crystal'                  => 'Kristal',
        'deuterium'                => 'Deuterium',

        // Vlootbewegingspagina
        'fleet_details'            => 'Vlootdetails',
        'ships'                    => 'Schepen',
        'shipment'                 => 'Lading',
        'recall'                   => 'Terugroepen',
        'start_time'               => 'Vertrektijd',
        'time_of_arrival'          => 'Aankomsttijd',
        'deep_space'               => 'Diep heelal',

        // Doel / spelerstatus indicatoren
        'uninhabited_planet'       => 'Onbewoonde planeet',
        'no_debris_field'          => 'Geen puinveld',
        'player_vacation'          => 'Speler in vakantiemodus',
        'admin_gm'                 => 'Admin of GM',
        'noob_protection'          => 'Beginnersbescherming',
        'player_too_strong'        => 'Deze planeet kan niet worden aangevallen omdat de speler te sterk is!',
        'no_moon'                  => 'Geen maan beschikbaar.',
        'no_recycler'              => 'Geen recycler beschikbaar.',
        'no_events'                => 'Er zijn momenteel geen evenementen actief.',
        'planet_already_reserved'  => 'Deze planeet is al gereserveerd voor een verplaatsing.',
        'max_planet_warning'       => 'Let op! Er kunnen momenteel geen extra planeten worden gekoloniseerd. Voor elke nieuwe kolonie zijn twee niveaus astrofysica nodig. Wilt u toch uw vloot sturen?',

        // Melkweg / netwerk
        'empty_systems'            => 'Lege systemen',
        'inactive_systems'         => 'Inactieve systemen',
        'network_on'               => 'Aan',
        'network_off'              => 'Uit',

        // Foutcodes (gebruikt in errorCodeMap)
        'err_generic'              => 'Er is een fout opgetreden',
        'err_no_moon'              => 'Fout, er is geen maan',
        'err_newbie_protection'    => 'Fout, speler kan niet worden bereikt vanwege beginnersbescherming',
        'err_too_strong'           => 'Speler is te sterk om aan te vallen',
        'err_vacation_mode'        => 'Fout, speler is in vakantiemodus',
        'err_own_vacation'         => 'Er kunnen geen vloten worden verstuurd vanuit vakantiemodus!',
        'err_not_enough_ships'     => 'Fout, onvoldoende schepen, stuur maximaal aantal:',
        'err_no_ships'             => 'Fout, geen schepen beschikbaar',
        'err_no_slots'             => 'Fout, geen vrije vlootslots beschikbaar',
        'err_no_deuterium'         => 'Fout, onvoldoende deuterium',
        'err_no_planet'            => 'Fout, er is geen planeet',
        'err_no_cargo'             => 'Fout, onvoldoende laadruimte',
        'err_multi_alarm'          => 'Multi-alarm',
        'err_attack_ban'           => 'Aanvalverbod',
    ],

    // -------------------------------------------------------------------------
    // Melkweg-pagina
    // -------------------------------------------------------------------------

    'galaxy' => [
        // Vakantiemodus
        'vacation_error'               => 'U kunt de melkwegweergave niet gebruiken terwijl u in vakantiemodus bent!',

        // Navigatie / koptekst
        'system'                       => 'Systeem',
        'go'                           => 'Ga!',

        // Systeemactieknoppen
        'system_phalanx'               => 'Systeem Phalanx',
        'system_espionage'             => 'Systeem Spionage',
        'discoveries'                  => 'Ontdekkingen',
        'discoveries_tooltip'          => 'Start een ontdekkingsmissie naar alle mogelijke locaties',

        // Labels statistiekenrij koptekst
        'probes_short'                 => 'Spionageson.',
        'recycler_short'               => 'Recycl.',
        'ipm_short'                    => 'IPR.',
        'used_slots'                   => 'Gebruikte slots',

        // Tabelkolomkopteksten
        'planet_col'                   => 'Planeet',
        'name_col'                     => 'Naam',
        'moon_col'                     => 'Maan',
        'debris_short'                 => 'PV',
        'player_status'                => 'Speler (Status)',
        'alliance'                     => 'Alliantie',
        'action'                       => 'Actie',

        // Expeditie / diepe ruimte rij
        'planets_colonized'            => 'Gekoloniseerde planeten',
        'expedition_fleet'             => 'Expeditievloot',
        'admiral_needed'               => 'U heeft een Admiraal nodig om deze functie te gebruiken.',
        'send'                         => 'stuur',

        // Legenda tooltip
        'legend'                       => 'Legenda',
        'status_admin_abbr'            => 'A',
        'legend_admin'                 => 'Beheerder',
        'status_strong_abbr'           => 's',
        'legend_strong'                => 'sterkere speler',
        'status_noob_abbr'             => 'n',
        'legend_noob'                  => 'zwakkere speler (beginner)',
        'status_outlaw_abbr'           => 'o',
        'legend_outlaw'                => 'Vogelvrij (tijdelijk)',
        'status_vacation_abbr'         => 'v',
        'vacation_mode'                => 'Vakantiemodus',
        'status_banned_abbr'           => 'b',
        'legend_banned'                => 'verbannen',
        'status_inactive_abbr'         => 'i',
        'legend_inactive_7'            => '7 dagen inactief',
        'status_longinactive_abbr'     => 'I',
        'legend_inactive_28'           => '28 dagen inactief',
        'status_honorable_abbr'        => 'hp',
        'legend_honorable'             => 'Eervolle doelwit',

        // JS loca-object (unieke melkwegstrings)
        'phalanx_restricted'           => 'Het systeem-phalanx kan alleen worden gebruikt door de alliantieklasse Onderzoeker!',
        'astro_required'               => 'U moet eerst Astrofysica onderzoeken.',
        'galaxy_nav'                   => 'Melkweg',
        'activity'                     => 'Activiteit',
        'no_action'                    => 'Geen acties beschikbaar.',
        'time_minute_abbr'             => 'm',
        'moon_diameter_km'             => 'Diameter van maan in km',
        'km'                           => 'km',
        'pathfinders_needed'           => 'Verkenners nodig',
        'recyclers_needed'             => 'Recyclers nodig',
        'mine_debris'                  => 'Mijnbouw',
        'phalanx_no_deut'              => 'Onvoldoende deuterium voor phalanxscan.',
        'use_phalanx'                  => 'Gebruik phalanx',
        'colonize_error'               => 'Het is niet mogelijk om een planeet te koloniseren zonder een kolonieschip.',
        'ranking'                      => 'Ranglijst',
        'espionage_report'             => 'Spionagebericht',
        'missile_attack'               => 'Rakettenaanval',
        'rank'                         => 'Rang',
        'alliance_member'              => 'Lid',
        'alliance_class'               => 'Alliantieklasse',
        'espionage_not_possible'       => 'Spionage niet mogelijk',
        'espionage'                    => 'Spionage',
        'hire_admiral'                 => 'Admiraal inhuren',
        'dark_matter'                  => 'Donkere materie',
        'outlaw_explanation'           => 'Als u vogelvrij bent, hebt u geen aanvalsbescherming meer en kunt u door alle spelers worden aangevallen.',
        'honorable_target_explanation' => 'In gevecht tegen dit doelwit kunt u eerepunten verdienen en 50% meer buit plunderen.',

        // JS galaxyLoca-object
        'relocate_success'             => 'De positie is voor u gereserveerd. De verplaatsing van de kolonie is begonnen.',
        'relocate_title'               => 'Planeet verplaatsen',
        'relocate_question'            => 'Weet u zeker dat u uw planeet naar deze coördinaten wilt verplaatsen? Voor de verplaatsing heeft u :cost Donkere materie nodig.',
        'deut_needed_relocate'         => 'Onvoldoende deuterium! U heeft 10 eenheden deuterium nodig.',
        'fleet_attacking'              => 'Vloot valt aan!',
        'fleet_underway'               => 'Vloot is onderweg',
        'discovery_send'               => 'Verkenningsschip sturen',
        'discovery_success'            => 'Verkenningsschip verstuurd',
        'discovery_unavailable'        => 'U kunt geen verkenningsschip naar deze locatie sturen.',
        'discovery_underway'           => 'Er is al een verkenningsschip op weg naar deze planeet.',
        'discovery_locked'             => 'U heeft het onderzoek om nieuwe levensvormen te ontdekken nog niet ontgrendeld.',
        'discovery_title'              => 'Verkenningsschip',
        'discovery_question'           => 'Wilt u een verkenningsschip naar deze planeet sturen?<br/>Metaal: 5000 Kristal: 1000 Deuterium: 500',

        // Phalanx-resultaatdialoog (JS-strings in Blade-rendered scriptblok)
        'sensor_report'                => 'sensorrapport',
        'refresh'                      => 'Vernieuwen',
        'arrived'                      => 'Aangekomen',

        // Rakettenaanval dialoog
        'target'                       => 'Doelwit',
        'flight_duration'              => 'Vliegduur',
        'ipm_full'                     => 'Interplanetaire raketten',
        'primary_target'               => 'Primair doelwit',
        'no_primary_target'            => 'Geen primair doelwit geselecteerd: willekeurig doelwit',
        'target_has'                   => 'Doelwit heeft',
        'abm_full'                     => 'Anti-ballistische raketten',
        'fire'                         => 'Vuur',
        'valid_missile_count'          => 'Voer een geldig aantal raketten in',
        'not_enough_missiles'          => 'U heeft niet genoeg raketten',
        'launched_success'             => 'Raketten succesvol gelanceerd!',
        'launch_failed'                => 'Lancering van raketten mislukt',
        'insufficient_range'           => 'Onvoldoende bereik (onderzoeksniveau impulsaandrijving) van uw interplanetaire raketten!',
    ],

    // -------------------------------------------------------------------------
    // Buddy-systeem (vriendschapsverzoeken + speler negeren — gebruikt op melkwegpagina)
    // -------------------------------------------------------------------------

    'buddy' => [
        'request_sent'   => 'Vriendenverzoek succesvol verzonden!',
        'request_failed' => 'Verzenden van vriendenverzoek mislukt.',
        'request_to'     => 'Vriendenverzoek aan',
        'ignore_confirm' => 'Weet u zeker dat u wilt negeren',
        'ignore_success' => 'Speler succesvol genegeerd!',
        'ignore_failed'  => 'Negeren van speler mislukt.',
    ],

    // -------------------------------------------------------------------------
    // Berichtenpagina
    // -------------------------------------------------------------------------

    'messages' => [
        // Hoofdtabbladen
        'tab_fleets'        => 'Vloten',
        'tab_communication' => 'Communicatie',
        'tab_economy'       => 'Economie',
        'tab_universe'      => 'Universum',
        'tab_system'        => 'OGame',
        'tab_favourites'    => 'Favorieten',

        // Vloten subtabbladen
        'subtab_espionage'   => 'Spionage',
        'subtab_combat'      => 'Gevechtverslagen',
        'subtab_expeditions' => 'Expedities',
        'subtab_transport'   => 'Verbonden/Transport',
        'subtab_other'       => 'Overige',

        // Communicatie subtabbladen
        'subtab_messages'         => 'Berichten',
        'subtab_information'      => 'Informatie',
        'subtab_shared_combat'    => 'Gedeelde gevechtverslagen',
        'subtab_shared_espionage' => 'Gedeelde spionagerapporten',

        // Algemene UI
        'news_feed'          => 'Nieuwsfeed',
        'loading'            => 'laden...',
        'error_occurred'     => 'Er is een fout opgetreden',
        'mark_favourite'     => 'markeer als favoriet',
        'remove_favourite'   => 'verwijder uit favorieten',
        'from'               => 'Van',
        'no_messages'        => 'Er zijn momenteel geen berichten beschikbaar op dit tabblad',
        'new_alliance_msg'   => 'Nieuw alliantiebericht',
        'to'                 => 'Aan',
        'all_players'        => 'alle spelers',
        'send'               => 'verstuur',
        'delete_buddy_title' => 'Vriend verwijderen',
        'report_to_operator' => 'Dit bericht melden bij een speloperator?',
        'too_few_chars'      => 'Te weinig tekens! Voer minimaal 2 tekens in.',

        // BBCode-editor (localizedBBCode)
        'bbcode_bold'           => 'Vet',
        'bbcode_italic'         => 'Cursief',
        'bbcode_underline'      => 'Onderstreept',
        'bbcode_stroke'         => 'Doorgestreept',
        'bbcode_sub'            => 'Subscript',
        'bbcode_sup'            => 'Superscript',
        'bbcode_font_color'     => 'Tekstkleur',
        'bbcode_font_size'      => 'Tekstgrootte',
        'bbcode_bg_color'       => 'Achtergrondkleur',
        'bbcode_bg_image'       => 'Achtergrondafbeelding',
        'bbcode_tooltip'        => 'Tooltip',
        'bbcode_align_left'     => 'Links uitlijnen',
        'bbcode_align_center'   => 'Centreren',
        'bbcode_align_right'    => 'Rechts uitlijnen',
        'bbcode_align_justify'  => 'Uitvullen',
        'bbcode_block'          => 'Regeleinde',
        'bbcode_code'           => 'Code',
        'bbcode_spoiler'        => 'Spoiler',
        'bbcode_moreopts'       => 'Meer opties',
        'bbcode_list'           => 'Lijst',
        'bbcode_hr'             => 'Horizontale lijn',
        'bbcode_picture'        => 'Afbeelding',
        'bbcode_link'           => 'Koppeling',
        'bbcode_email'          => 'E-mail',
        'bbcode_player'         => 'Speler',
        'bbcode_item'           => 'Item',
        'bbcode_coordinates'    => 'Coördinaten',
        'bbcode_preview'        => 'Voorbeeld',
        'bbcode_text_ph'        => 'Tekst...',
        'bbcode_player_ph'      => 'Speler-ID of naam',
        'bbcode_item_ph'        => 'Item-ID',
        'bbcode_coord_ph'       => 'Melkweg:systeem:positie',
        'bbcode_chars_left'     => 'Resterend tekens',
        'bbcode_ok'             => 'Ok',
        'bbcode_cancel'         => 'Annuleren',
        'bbcode_repeat_x'       => 'Horizontaal herhalen',
        'bbcode_repeat_y'       => 'Verticaal herhalen',

        // Spionagebericht
        'spy_player'            => 'Speler',
        'spy_activity'          => 'Activiteit',
        'spy_minutes_ago'       => 'minuten geleden',
        'spy_class'             => 'Klasse',
        'spy_unknown'           => 'Onbekend',
        'spy_alliance_class'    => 'Alliantieklasse',
        'spy_no_alliance_class' => 'Geen alliantieklasse geselecteerd',
        'spy_resources'         => 'Grondstoffen',
        'spy_loot'              => 'Buit',
        'spy_counter_esp'       => 'Kans op tegensterspionage',
        'spy_no_info'           => 'We konden geen betrouwbare informatie van dit type ophalen uit de scan.',
        'spy_debris_field'      => 'puin-veld',
        'spy_no_activity'       => 'Uw spionage toont geen abnormaliteiten in de atmosfeer van de planeet. Er lijkt de afgelopen uur geen activiteit op de planeet te zijn geweest.',
        'spy_fleets'            => 'Vloten',
        'spy_defense'           => 'Verdediging',
        'spy_research'          => 'Onderzoek',
        'spy_building'          => 'Gebouw',

        // Gevechtverslag (beknopt)
        'battle_attacker'    => 'Aanvaller',
        'battle_defender'    => 'Verdediger',
        'battle_resources'   => 'Grondstoffen',
        'battle_loot'        => 'Buit',
        'battle_debris_new'        => 'Puin-veld (nieuw aangemaakt)',
        'battle_wreckage_created'  => 'Wrakstukken ontstaan',
        'battle_attacker_wreckage' => 'Aanvaller wrakstukken',
        'battle_repaired'    => 'Gerepareerd',
        'battle_moon_chance' => 'Maankans',

        // Gevechtverslag (volledig)
        'battle_report'          => 'Gevechtverslag',
        'battle_planet'          => 'Planeet',
        'battle_fleet_command'   => 'Vlootcommando',
        'battle_from'            => 'Van',
        'battle_tactical_retreat' => 'Tactische terugtrekking',
        'battle_total_loot'      => 'Totale buit',
        'battle_debris'          => 'Puin (nieuw)',
        'battle_recycler'        => 'Recycler',
        'battle_mined_after'     => 'Gedolven na gevecht',
        'battle_reaper'          => 'Reaper',
        'battle_debris_left'     => 'Puin-velden (resterend)',
        'battle_honour_points'   => 'Eerepunten',
        'battle_dishonourable'   => 'Oneerlijk gevecht',
        'battle_vs'              => 'vs',
        'battle_honourable'      => 'Eerlijk gevecht',
        'battle_class'           => 'Klasse',
        'battle_weapons'         => 'Wapens',
        'battle_shields'         => 'Schilden',
        'battle_armour'          => 'Pantser',
        'battle_combat_ships'    => 'Gevechtsschepen',
        'battle_civil_ships'     => 'Burgerschepen',
        'battle_defences'        => 'Verdedigingen',
        'battle_repaired_def'    => 'Gerepareerde verdedigingen',
        'battle_share'           => 'bericht delen',
        'battle_attack'          => 'Aanvallen',
        'battle_espionage'       => 'Spionage',
        'battle_delete'          => 'verwijderen',
        'battle_favourite'       => 'markeer als favoriet',
        'battle_hamill'          => 'Een Lichte Jager heeft een Dodestar vernietigd voordat de strijd begon!',
        'battle_retreat_tooltip'  => 'Let op: Dodestarren, Spionagesondes, Zonne-energiesatellieten en elke vloot op een ACS-verdedigingsmissie kunnen niet vluchten. Tactische terugtrekkingen zijn ook gedeactiveerd in eervolle gevechten. Een terugtrekking kan ook handmatig zijn gedeactiveerd of verhinderd door een gebrek aan deuterium. Bandieten en spelers met meer dan 500.000 punten trekken zich nooit terug.',
        'battle_no_flee'         => 'De verdedigende vloot is niet gevlucht.',
        'battle_rounds'          => 'Rondes',
        'battle_start'           => 'Start',
        'battle_player_from'     => 'van',
        'battle_attacker_fires'  => 'De :attacker vuurt in totaal :hits schoten op de :defender met een totale sterkte van :strength. De schilden van de :defender2 absorberen :absorbed punten schade.',
        'battle_defender_fires'  => 'De :defender vuurt in totaal :hits schoten op de :attacker met een totale sterkte van :strength. De schilden van de :attacker2 absorberen :absorbed punten schade.',
    ],

    // -------------------------------------------------------------------------
    // Alliantiepagina
    // -------------------------------------------------------------------------

    'alliance' => [
        // Pagina / navigatie
        'page_title'                    => 'Alliantie',
        'tab_overview'                  => 'Overzicht',
        'tab_management'                => 'Beheer',
        'tab_communication'             => 'Communicatie',
        'tab_applications'              => 'Aanmeldingen',
        'tab_classes'                   => 'Alliantieklassen',
        'tab_create'                    => 'Alliantie aanmaken',
        'tab_search'                    => 'Alliantie zoeken',
        'tab_apply'                     => 'aanmelden',

        // Overzicht – alliantie-infotabel
        'your_alliance'                 => 'Jouw alliantie',
        'name'                          => 'Naam',
        'tag'                           => 'Tag',
        'created'                       => 'Aangemaakt',
        'member'                        => 'Lid',
        'your_rank'                     => 'Jouw rang',
        'homepage'                      => 'Homepage',
        'logo'                          => 'Alliantielogo',
        'open_page'                     => 'Open alliantiepagina',
        'highscore'                     => 'Alliantieklassement',
        'leave_wait_warning'            => 'Als je de alliantie verlaat, moet je 3 dagen wachten voordat je een andere alliantie kunt aanmelden of aanmaken.',
        'leave_btn'                     => 'Alliantie verlaten',

        // Overzicht – ledenlijst
        'member_list'                   => 'Ledenlijst',
        'no_members'                    => 'Geen leden gevonden',
        'assign_rank_btn'               => 'Rang toewijzen',
        'kick_tooltip'                  => 'Alliantielid verwijderen',
        'write_msg_tooltip'             => 'Bericht schrijven',
        'col_name'                      => 'Naam',
        'col_rank'                      => 'Rang',
        'col_coords'                    => 'Coördinaten',
        'col_joined'                    => 'Lid geworden',
        'col_online'                    => 'Online',
        'col_function'                  => 'Functie',

        // Overzicht – tekstgebieden
        'internal_area'                 => 'Intern Gebied',
        'external_area'                 => 'Extern Gebied',

        // Beheer – privileges
        'configure_privileges'          => 'Privileges configureren',
        'col_rank_name'                 => 'Rangnaam',
        'col_applications_group'        => 'Aanmeldingen',
        'col_member_group'              => 'Lid',
        'col_alliance_group'            => 'Alliantie',
        'delete_rank'                   => 'Rang verwijderen',
        'save_btn'                      => 'Opslaan',
        'rights_warning_html'           => '<strong>Waarschuwing!</strong> Je kunt alleen rechten geven die je zelf hebt.',
        'rights_warning_loca'           => '[b]Waarschuwing![/b] Je kunt alleen rechten geven die je zelf hebt.',
        'rights_legend'                 => 'Rechtentoelichting',
        'create_rank_btn'               => 'Nieuwe rang aanmaken',
        'rank_name_placeholder'         => 'Rangnaam',
        'no_ranks'                      => 'Geen rangen gevonden',

        // Beheer – rechten
        'perm_see_applications'         => 'Aanmeldingen bekijken',
        'perm_edit_applications'        => 'Aanmeldingen verwerken',
        'perm_see_members'              => 'Ledenlijst bekijken',
        'perm_kick_user'                => 'Gebruiker verwijderen',
        'perm_see_online'               => 'Online status zien',
        'perm_send_circular'            => 'Circulaire berichten sturen',
        'perm_disband'                  => 'Alliantie ontbinden',
        'perm_manage'                   => 'Alliantie beheren',
        'perm_right_hand'               => 'Rechterhand',
        'perm_right_hand_long'          => '`Rechterhand` (nodig om de rang van stichter over te dragen)',
        'perm_manage_classes'           => 'Alliantieklasse beheren',

        // Beheer – tekstsectie
        'manage_texts'                  => 'Teksten beheren',
        'internal_text'                 => 'Interne tekst',
        'external_text'                 => 'Externe tekst',
        'application_text'              => 'Aanmeldingstekst',

        // Beheer – opties/instellingen
        'options'                       => 'Opties',
        'alliance_logo_label'           => 'Alliantielogo',
        'applications_field'            => 'Aanmeldingen',
        'status_open'                   => 'Mogelijk (alliantie open)',
        'status_closed'                 => 'Onmogelijk (alliantie gesloten)',
        'rename_founder'                => 'Stichtertitel hernoemen als',
        'rename_newcomer'               => 'Rang Nieuwkomer hernoemen',
        'no_settings_perm'              => 'Je hebt geen toestemming om de alliantie-instellingen te beheren.',

        // Beheer – tag/naam wijzigen
        'change_tag_name'               => 'Alliantietag/naam wijzigen',
        'change_tag'                    => 'Alliantietag wijzigen',
        'change_name'                   => 'Alliantienaam wijzigen',
        'former_tag'                    => 'Vorige alliantietag:',
        'new_tag'                       => 'Nieuwe alliantietag:',
        'former_name'                   => 'Vorige alliantienaam:',
        'new_name'                      => 'Nieuwe alliantienaam:',
        'former_tag_short'              => 'Vorige alliantietag',
        'new_tag_short'                 => 'Nieuwe alliantietag',
        'former_name_short'             => 'Vorige alliantienaam',
        'new_name_short'                => 'Nieuwe alliantienaam',
        'no_tagname_perm'               => 'Je hebt geen toestemming om de alliantietag/naam te wijzigen.',

        // Beheer – ontbinden / overdragen
        'delete_pass_on'                => 'Alliantie ontbinden/overdragen',
        'delete_btn'                    => 'Deze alliantie ontbinden',
        'no_delete_perm'                => 'Je hebt geen toestemming om de alliantie te ontbinden.',
        'handover'                      => 'Alliantie overdragen',
        'takeover_btn'                  => 'Alliantie overnemen',
        'loca_continue'                 => 'Doorgaan',
        'loca_change_founder'           => 'Draag de stichtertitel over aan:',
        'loca_no_transfer_error'        => 'Geen van de leden heeft het vereiste `rechterhand`-recht. Je kunt de alliantie niet overdragen.',
        'loca_founder_inactive_error'   => 'De stichter is niet lang genoeg inactief om de alliantie over te nemen.',

        // Beheer – alliantie verlaten (niet-stichters)
        'leave_section_title'           => 'Alliantie verlaten',
        'leave_consequences'            => 'Als je de alliantie verlaat, verlies je al je rangrechten en alliantievoordelen.',

        // Tab aanmeldingen
        'no_applications'               => 'Geen aanmeldingen gevonden',
        'accept_btn'                    => 'accepteren',
        'deny_btn'                      => 'Aanmelder weigeren',
        'report_btn'                    => 'Aanmelding melden',
        'app_date'                      => 'Aanmeldingsdatum',
        'action_col'                    => 'Actie',
        'answer_btn'                    => 'antwoorden',
        'reason_label'                  => 'Reden',

        // Aanmeldingspagina
        'apply_title'                   => 'Aanmelden bij alliantie',
        'apply_heading'                 => 'Aanmelding bij',
        'send_application_btn'          => 'Aanmelding sturen',
        'chars_remaining'               => 'Tekens over',
        'msg_too_long'                  => 'Het bericht is te lang (max 2000 tekens)',

        // Broadcast communicatie
        'addressee'                     => 'Aan',
        'all_players'                   => 'alle spelers',
        'only_rank'                     => 'alleen rang:',
        'send_btn'                      => 'Sturen',

        // Info popup
        'info_title'                    => 'Alliantie-informatie',
        'apply_confirm'                 => 'Wil je je aanmelden bij deze alliantie?',
        'redirect_confirm'              => 'Door deze link te volgen, verlaat je OGame. Wil je doorgaan?',

        // Klassen tab
        'class_selection_header'        => 'Klasseselectie',
        'select_class_title'            => 'Alliantieklasse selecteren',
        'select_class_note'             => 'Selecteer een alliantieklasse om speciale bonussen te ontvangen. Je kunt de alliantieklasse wijzigen in het alliantiemenu, mits je de vereiste rechten hebt.',
        'class_warriors'                => 'Strijders (Alliantie)',
        'class_traders'                 => 'Handelaren (Alliantie)',
        'class_researchers'             => 'Onderzoekers (Alliantie)',
        'class_label'                   => 'Alliantieklasse',
        'buy_for'                       => 'Kopen voor',
        'no_dark_matter'                => 'Er is niet genoeg donkere materie beschikbaar',
        'loca_deactivate'               => 'Deactiveren',
        'loca_activate_dm'              => 'Wil je de alliantieklasse #allianceClassName# activeren voor #darkmatter# Donkere Materie? Daardoor verlies je je huidige alliantieklasse.',
        'loca_activate_item'            => 'Wil je de alliantieklasse #allianceClassName# activeren? Daardoor verlies je je huidige alliantieklasse.',
        'loca_deactivate_note'          => 'Wil je de alliantieklasse #allianceClassName# echt deactiveren? Heractivering vereist een klassewisselitem voor 500.000 Donkere Materie.',
        'loca_class_change_append'      => '<br><br>Huidige alliantieklasse: #currentAllianceClassName#<br><br>Laatste wijziging: #lastAllianceClassChange#',
        'loca_no_dm'                    => 'Niet genoeg Donkere Materie beschikbaar! Wil je nu wat kopen?',
        'loca_reference'                => 'Referentie',
        'loca_language'                 => 'Taal:',
        'loca_loading'                  => 'laden...',
        'warrior_bonus_1'               => '+10% snelheid voor schepen die tussen alliantieleden vliegen',
        'warrior_bonus_2'               => '+1 gevechtsniveaus onderzoek',
        'warrior_bonus_3'               => '+1 spionageniveaus onderzoek',
        'warrior_bonus_4'               => 'Het spionagesysteem kan worden gebruikt om hele systemen te scannen.',
        'trader_bonus_1'                => '+10% snelheid voor transporters',
        'trader_bonus_2'                => '+5% mijnproductie',
        'trader_bonus_3'                => '+5% energieproductie',
        'trader_bonus_4'                => '+10% opslagcapaciteit planeet',
        'trader_bonus_5'                => '+10% opslagcapaciteit maan',
        'researcher_bonus_1'            => '+5% grotere planeten bij kolonisatie',
        'researcher_bonus_2'            => '+10% snelheid naar expeditiebestemming',
        'researcher_bonus_3'            => 'De systeemfalanx kan worden gebruikt om vlootbewegingen in hele systemen te scannen.',
        'class_not_implemented'         => 'Alliantieklassesysteem nog niet geïmplementeerd',

        // Aanmaakformulier alliantie
        'create_tag_label'              => 'Alliantietag (3-8 tekens)',
        'create_name_label'             => 'Alliantienaam (3-30 tekens)',
        'create_btn'                    => 'Alliantie aanmaken',
        'loca_ally_tag_chars'           => 'Alliantie-Tag (3-30 tekens)',
        'loca_ally_name_chars'          => 'Alliantie-Naam (3-8 tekens)',
        'loca_ally_name_label'          => 'Alliantienaam (3-30 tekens)',
        'loca_ally_tag_label'           => 'Alliantietag (3-8 tekens)',
        'validation_min_chars'          => 'Niet genoeg tekens',
        'validation_special'            => 'Bevat ongeldige tekens.',
        'validation_underscore'         => 'Je naam mag niet beginnen of eindigen met een onderstrepingsteken.',
        'validation_hyphen'             => 'Je naam mag niet beginnen of eindigen met een koppelteken.',
        'validation_space'              => 'Je naam mag niet beginnen of eindigen met een spatie.',
        'validation_max_underscores'    => 'Je naam mag niet meer dan 3 onderstrepingstekens bevatten.',
        'validation_max_hyphens'        => 'Je naam mag niet meer dan 3 koppeltekens bevatten.',
        'validation_max_spaces'         => 'Je naam mag niet meer dan 3 spaties bevatten.',
        'validation_consec_underscores' => 'Je mag niet twee of meer onderstrepingstekens na elkaar gebruiken.',
        'validation_consec_hyphens'     => 'Je mag niet twee of meer koppeltekens achter elkaar gebruiken.',
        'validation_consec_spaces'      => 'Je mag niet twee of meer spaties na elkaar gebruiken.',

        // JS bevestigingsdialogen
        'confirm_leave'                 => 'Weet je zeker dat je de alliantie wilt verlaten?',
        'confirm_kick'                  => 'Weet je zeker dat je :username uit de alliantie wilt verwijderen?',
        'confirm_deny'                  => 'Weet je zeker dat je deze aanmelding wilt weigeren?',
        'confirm_deny_title'            => 'Aanmelding weigeren',
        'confirm_disband'               => 'Alliantie echt ontbinden?',
        'confirm_pass_on'               => 'Weet je zeker dat je je alliantie wilt overdragen?',
        'confirm_takeover'              => 'Weet je zeker dat je deze alliantie wilt overnemen?',
        'confirm_abandon'               => 'Deze alliantie verlaten?',
        'confirm_takeover_long'         => 'Deze alliantie overnemen?',

        // Controller / AJAX succes- en foutmeldingen
        'msg_already_in'                => 'Je zit al in een alliantie',
        'msg_not_in_alliance'           => 'Je zit niet in een alliantie',
        'msg_not_found'                 => 'Alliantie niet gevonden',
        'msg_id_required'               => 'Alliantie-ID is vereist',
        'msg_closed'                    => 'Deze alliantie is gesloten voor aanmeldingen',
        'msg_created'                   => 'Alliantie succesvol aangemaakt',
        'msg_applied'                   => 'Aanmelding succesvol verstuurd',
        'msg_accepted'                  => 'Aanmelding geaccepteerd',
        'msg_rejected'                  => 'Aanmelding geweigerd',
        'msg_kicked'                    => 'Lid uit alliantie verwijderd',
        'msg_kicked_success'            => 'Lid succesvol verwijderd',
        'msg_left'                      => 'Je hebt de alliantie verlaten',
        'msg_rank_assigned'             => 'Rang toegewezen',
        'msg_rank_assigned_to'          => 'Rang succesvol toegewezen aan :name',
        'msg_ranks_assigned'            => 'Rangen succesvol toegewezen',
        'msg_rank_perms_updated'        => 'Rangrechten bijgewerkt',
        'msg_texts_updated'             => 'Alliantieteksten bijgewerkt',
        'msg_text_updated'              => 'Alliantietekst bijgewerkt',
        'msg_settings_updated'          => 'Alliantie-instellingen bijgewerkt',
        'msg_tag_updated'               => 'Alliantietag bijgewerkt',
        'msg_name_updated'              => 'Alliantienaam bijgewerkt',
        'msg_tag_name_updated'          => 'Alliantietag en -naam bijgewerkt',
        'msg_disbanded'                 => 'Alliantie ontbonden',
        'msg_broadcast_sent'            => 'Circulair bericht succesvol verstuurd',
        'msg_rank_created'              => 'Rang succesvol aangemaakt',
        'msg_apply_success'             => 'Aanmelding succesvol verstuurd',
        'msg_apply_error'               => 'Aanmelding kon niet worden verstuurd',
        'msg_leave_error'               => 'Kon de alliantie niet verlaten',
        'msg_assign_error'              => 'Kon rangen niet toewijzen',
        'msg_kick_error'                => 'Kon lid niet verwijderen',
        'msg_invalid_action'            => 'Ongeldige actie',
        'msg_error'                     => 'Er is een fout opgetreden',
    ],

    // -------------------------------------------------------------------
    // Techtree module
    // -------------------------------------------------------------------
    'techtree' => [
        // Navigatietabbladen
        'tab_techtree'                          => 'Techboom',
        'tab_applications'                      => 'Toepassingen',
        'tab_techinfo'                          => 'Technische info',
        'tab_technology'                        => 'Technologie',

        // Gemeenschappelijk
        'page_title'                            => 'Technologie',
        'no_requirements'                       => 'Geen vereisten beschikbaar',
        'is_requirement_for'                    => 'is een vereiste voor',
        'level'                                 => 'Niveau',

        // Gedeelde tabelkolommen
        'col_level'                             => 'Niveau',
        'col_difference'                        => 'Verschil',
        'col_diff_per_level'                    => 'Verschil/Niveau',
        'col_protected'                         => 'Beschermd',
        'col_protected_percent'                 => 'Beschermd (%)',

        // Productietabel
        'production_energy_balance'             => 'Energiebalans',
        'production_per_hour'                   => 'Productie/u',
        'production_deuterium_consumption'      => 'Deuteriumverbruik',

        // Eigenschappentabel (schepen/verdediging)
        'properties_technical_data'             => 'Technische gegevens',
        'properties_structural_integrity'       => 'Structurele integriteit',
        'properties_shield_strength'            => 'Schildsterkte',
        'properties_attack_strength'            => 'Aanvalssterkte',
        'properties_speed'                      => 'Snelheid',
        'properties_cargo_capacity'             => 'Laadcapaciteit',
        'properties_fuel_usage'                 => 'Brandstofverbruik (Deuterium)',

        // Eigenschapstooltip
        'tooltip_basic_value'                   => 'Basiswaarde',

        // Snelvuur
        'rapidfire_from'                        => 'Snelvuur van',
        'rapidfire_against'                     => 'Snelvuur tegen',

        // Opslagtabel
        'storage_capacity'                      => 'Opslagcap.',

        // Plasmatabel
        'plasma_metal_bonus'                    => 'Metaalbonus %',
        'plasma_crystal_bonus'                  => 'Kristalbonus %',
        'plasma_deuterium_bonus'                => 'Deuteriumbonus %',

        // Astrofysicatabel
        'astrophysics_max_colonies'             => 'Maximale kolonies',
        'astrophysics_max_expeditions'          => 'Maximale expedities',
        'astrophysics_note_1'                   => 'Posities 3 en 13 kunnen vanaf niveau 4 worden bevolkt.',
        'astrophysics_note_2'                   => 'Posities 2 en 14 kunnen vanaf niveau 6 worden bevolkt.',
        'astrophysics_note_3'                   => 'Posities 1 en 15 kunnen vanaf niveau 8 worden bevolkt.',
    ],

    // -------------------------------------------------------------------------
    // Opties module (accountinstellingen)
    // -------------------------------------------------------------------------
    'options' => [
        'page_title'                            => 'Opties',
        'tab_userdata'                          => 'Gebruikersgegevens',
        'tab_general'                           => 'Algemeen',
        'tab_display'                           => 'Weergave',
        'tab_extended'                          => 'Uitgebreid',

        // Tab 1 — Gebruikersgegevens
        'section_playername'                    => 'Spelernaam',
        'your_player_name'                      => 'Jouw spelernaam:',
        'new_player_name'                       => 'Nieuwe spelernaam:',
        'username_change_once_week'             => 'Je kunt je gebruikersnaam eenmaal per week wijzigen.',
        'username_change_hint'                  => 'Klik hiervoor op je naam of de instellingen bovenaan het scherm.',

        'section_password'                      => 'Wachtwoord wijzigen',
        'old_password'                          => 'Voer oud wachtwoord in:',
        'new_password'                          => 'Nieuw wachtwoord (minimaal 4 tekens):',
        'repeat_password'                       => 'Herhaal het nieuwe wachtwoord:',
        'password_check'                        => 'Wachtwoordcontrole:',
        'password_strength_low'                 => 'Laag',
        'password_strength_medium'              => 'Gemiddeld',
        'password_strength_high'                => 'Hoog',
        'password_properties_title'             => 'Het wachtwoord moet de volgende eigenschappen bevatten',
        'password_min_max'                      => 'min. 4 tekens, max. 128 tekens',
        'password_mixed_case'                   => 'Hoofd- en kleine letters',
        'password_special_chars'                => 'Speciale tekens (bijv. !?:_., )',
        'password_numbers'                      => 'Cijfers',
        'password_length_hint'                  => 'Je wachtwoord moet minimaal <strong>4 tekens</strong> hebben en mag niet langer zijn dan <strong>128 tekens</strong>.',

        'section_email'                         => 'E-mailadres',
        'current_email'                         => 'Huidig e-mailadres:',
        'send_validation_link'                  => 'Validatielink versturen',
        'email_sent_success'                    => 'E-mail succesvol verzonden!',
        'email_sent_error'                      => 'Fout! Account is al gevalideerd of de e-mail kon niet worden verzonden!',
        'email_too_many_requests'               => 'Je hebt al te veel e-mails aangevraagd!',
        'new_email'                             => 'Nieuw e-mailadres:',
        'new_email_confirm'                     => 'Nieuw e-mailadres (ter bevestiging):',
        'enter_password_confirm'                => 'Voer wachtwoord in (ter bevestiging):',
        'email_warning'                         => 'Waarschuwing! Na een succesvolle accountvalidatie is een nieuwe wijziging van het e-mailadres pas mogelijk na een periode van <b>7 dagen</b>.',

        // Tab 2 — Algemeen
        'section_spy_probes'                    => 'Spionagesondes',
        'spy_probes_amount'                     => 'Aantal spionagesondes:',
        'section_chat'                          => 'Chat',
        'disable_chat_bar'                      => 'Chatbalk deactiveren:',
        'section_warnings'                      => 'Waarschuwingen',
        'disable_outlaw_warning'                => 'Wetteloze-waarschuwing deactiveren bij aanvallen op tegenstanders 5x sterker:',

        // Tab 3 — Weergave
        'section_general_display'               => 'Algemeen',
        'show_mobile_version'                   => 'Mobiele versie weergeven:',
        'show_alt_dropdowns'                    => 'Alternatieve dropdowns weergeven:',
        'activate_autofocus'                    => 'Autofocus activeren in de ranglijsten:',
        'always_show_events'                    => 'Gebeurtenissen altijd weergeven:',
        'events_hide'                           => 'Verbergen',
        'events_above'                          => 'Boven de inhoud',
        'events_below'                          => 'Onder de inhoud',
        'section_planets'                       => 'Jouw planeten',
        'sort_planets_by'                       => 'Planeten sorteren op:',
        'sort_emergence'                        => 'Volgorde van verschijning',
        'sort_coordinates'                      => 'Coördinaten',
        'sort_alphabet'                         => 'Alfabet',
        'sort_size'                             => 'Grootte',
        'sort_used_fields'                      => 'Gebruikte velden',
        'sort_sequence'                         => 'Sorteervolgorde:',
        'sort_order_up'                         => 'omhoog',
        'sort_order_down'                       => 'omlaag',
        'section_overview_display'              => 'Overzicht',
        'highlight_planet_info'                 => 'Planeetinformatie markeren:',
        'animated_detail_display'               => 'Geanimeerde detailweergave:',
        'animated_overview'                     => 'Geanimeerd overzicht:',
        'section_overlays'                      => 'Overlays',
        'overlays_hint'                         => 'Met de volgende instellingen kunnen de bijbehorende overlays als een extra browservenster worden geopend in plaats van binnen het spel.',
        'popup_notes'                           => 'Notities in extra venster:',
        'popup_combat_reports'                  => 'Gevechtsrapporten in extra venster:',
        'section_messages_display'              => 'Berichten',
        'hide_report_pictures'                  => 'Afbeeldingen in rapporten verbergen:',
        'msgs_per_page'                         => 'Aantal weergegeven berichten per pagina:',
        'auctioneer_notifications'              => 'Veilingmeesters melding:',
        'economy_notifications'                 => 'Economieberichten aanmaken:',
        'section_galaxy_display'                => 'Melkweg',
        'detailed_activity'                     => 'Gedetailleerde activiteitsweergave:',
        'preserve_galaxy_system'                => 'Melkweg/systeem bewaren bij planeetwisseling:',

        // Tab 4 — Uitgebreid
        'section_vacation'                      => 'Vakantiemodus',
        'vacation_active'                       => 'Je bent momenteel in vakantiemodus.',
        'vacation_can_deactivate_after'         => 'Je kunt het deactiveren na:',
        'vacation_cannot_activate'              => 'Vakantiemodus kan niet worden geactiveerd (Actieve vloten)',
        'vacation_description_1'                => 'De vakantiemodus is ontworpen om je te beschermen tijdens lange afwezigheid van het spel. Je kunt het alleen activeren als geen van je vloten onderweg zijn. Bouw- en onderzoeksopdrachten worden in de wacht gezet.',
        'vacation_description_2'                => 'Eenmaal geactiveerd beschermt de vakantiemodus je tegen nieuwe aanvallen. Aanvallen die al zijn gestart gaan echter door en je productie wordt op nul gezet. Vakantiemodus voorkomt niet dat je account wordt verwijderd als het meer dan 35 dagen inactief is geweest en er geen gekochte DM op staat.',
        'vacation_description_3'                => 'Vakantiemodus duurt minimaal 48 uur. Pas na het verstrijken van deze tijd kun je het deactiveren.',
        'vacation_tooltip_min_days'             => 'De vakantie duurt minimaal 2 dagen.',
        'vacation_deactivate_btn'               => 'Deactiveren',
        'vacation_activate_btn'                 => 'Activeren',
        'section_account'                       => 'Jouw account',
        'delete_account'                        => 'Account verwijderen',
        'delete_account_hint'                   => 'Vink hier aan om je account te markeren voor automatische verwijdering na 7 dagen.',

        // Verzendknop
        'use_settings'                          => 'Instellingen gebruiken',

        // JS validatieregels
        'validation_not_enough_chars'           => 'Niet genoeg tekens',
        'validation_pw_too_short'               => 'Het ingevoerde wachtwoord is te kort (min. 4 tekens)',
        'validation_pw_too_long'                => 'Het ingevoerde wachtwoord is te lang (max. 20 tekens)',
        'validation_invalid_email'              => 'Je moet een geldig e-mailadres invoeren!',
        'validation_special_chars'              => 'Bevat ongeldige tekens.',
        'validation_no_begin_end_underscore'    => 'Je naam mag niet beginnen of eindigen met een underscore.',
        'validation_no_begin_end_hyphen'        => 'Je naam mag niet beginnen of eindigen met een koppelteken.',
        'validation_no_begin_end_whitespace'    => 'Je naam mag niet beginnen of eindigen met een spatie.',
        'validation_max_three_underscores'      => 'Je naam mag niet meer dan 3 underscores bevatten.',
        'validation_max_three_hyphens'          => 'Je naam mag niet meer dan 3 koppeltekens bevatten.',
        'validation_max_three_spaces'           => 'Je naam mag niet meer dan 3 spaties bevatten.',
        'validation_no_consecutive_underscores' => 'Je mag niet twee of meer underscores achter elkaar gebruiken.',
        'validation_no_consecutive_hyphens'     => 'Je mag niet twee of meer koppeltekens achter elkaar gebruiken.',
        'validation_no_consecutive_spaces'      => 'Je mag niet twee of meer spaties achter elkaar gebruiken.',

        // JS strings
        'js_change_name_title'                  => 'Nieuwe spelernaam',
        'js_change_name_question'               => 'Weet je zeker dat je je spelernaam wilt wijzigen in %newName%?',
        'js_planet_move_question'               => 'Let op! Deze missie kan nog actief zijn wanneer de herlocatieperiode begint en als dat zo is, wordt het proces geannuleerd. Wil je echt doorgaan met deze taak?',
        'js_tab_disabled'                       => 'Om deze optie te gebruiken moet je gevalideerd zijn en niet in vakantiemodus zijn!',
        'js_vacation_question'                  => 'Wil je de vakantiemodus activeren? Je kunt je vakantie pas na 2 dagen beëindigen.',

        // Controllerberichten
        'msg_settings_saved'                    => 'Instellingen opgeslagen',
        'msg_password_incorrect'                => 'Het huidige wachtwoord dat je hebt ingevoerd is onjuist.',
        'msg_password_mismatch'                 => 'De nieuwe wachtwoorden komen niet overeen.',
        'msg_password_length_invalid'           => 'Het nieuwe wachtwoord moet tussen 4 en 128 tekens lang zijn.',
        'msg_vacation_activated'                => 'Vakantiemodus geactiveerd. Je wordt minimaal 48 uur beschermd tegen nieuwe aanvallen.',
        'msg_vacation_deactivated'              => 'Vakantiemodus gedeactiveerd.',
        'msg_vacation_min_duration'             => 'Je kunt de vakantiemodus pas deactiveren nadat de minimale duur van 48 uur is verstreken.',
        'msg_vacation_fleets_in_transit'        => 'Je kunt de vakantiemodus niet activeren terwijl je vloten onderweg zijn.',
        'msg_probes_min_one'                    => 'Het aantal spionagesondes moet minimaal 1 zijn',
    ],

    // -------------------------------------------------------------------------
    // Layout (main.blade.php) — header, menu, resourcebalk, footer, JS loca
    // -------------------------------------------------------------------------
    'layout' => [
        // Headerbalk
        'player'                    => 'Speler',
        'change_player_name'        => 'Spelernaam wijzigen',
        'highscore'                 => 'Ranglijst',
        'notes'                     => 'Notities',
        'notes_overlay_title'       => 'Mijn notities',
        'buddies'                   => 'Vrienden',
        'search'                    => 'Zoeken',
        'search_overlay_title'      => 'Zoek in universum',
        'options'                   => 'Opties',
        'support'                   => 'Ondersteuning',
        'log_out'                   => 'Uitloggen',
        'unread_messages'           => 'ongelezen bericht(en)',
        'loading'                   => 'laden...',
        'no_fleet_movement'         => 'Geen vlootbeweging',
        'under_attack'              => 'Je wordt aangevallen!',

        // Karakterklasse
        'class_none'                => 'Geen klasse geselecteerd',
        'class_selected'            => 'Jouw klasse: :name',
        'class_click_select'        => 'Klik om een karakterklasse te selecteren',

        // Resourcebalk
        'res_available'             => 'Beschikbaar',
        'res_storage_capacity'      => 'Opslagcapaciteit',
        'res_current_production'    => 'Huidige productie',
        'res_den_capacity'          => 'Holcapaciteit',
        'res_consumption'           => 'Verbruik',
        'res_purchase_dm'           => 'Donkere Materie kopen',
        'res_metal'                 => 'Metaal',
        'res_crystal'               => 'Kristal',
        'res_deuterium'             => 'Deuterium',
        'res_energy'                => 'Energie',
        'res_dark_matter'           => 'Donkere Materie',

        // Zijmenu — itemlabels
        'menu_overview'             => 'Overzicht',
        'menu_resources'            => 'Grondstoffen',
        'menu_facilities'           => 'Faciliteiten',
        'menu_merchant'             => 'Handelaar',
        'menu_research'             => 'Onderzoek',
        'menu_shipyard'             => 'Scheepswerf',
        'menu_defense'              => 'Verdediging',
        'menu_fleet'                => 'Vloot',
        'menu_galaxy'               => 'Melkweg',
        'menu_alliance'             => 'Alliantie',
        'menu_officers'             => 'Officieren werven',
        'menu_shop'                 => 'Winkel',
        'menu_directives'           => 'Richtlijnen',

        // Zijmenu — icoontitels
        'menu_rewards_title'        => 'Beloningen',
        'menu_resource_settings_title' => 'Resource-instellingen',
        'menu_jump_gate'            => 'Sprongpoort',
        'menu_resource_market_title' => 'Grondstoffenmarkt',
        'menu_technology_title'     => 'Technologie',
        'menu_fleet_movement_title' => 'Vlootbeweging',
        'menu_inventory_title'      => 'Inventaris',

        // Planetenbalk
        'planets'                   => 'Planeten',

        // Chatbalk
        'contacts_online'           => ':count Contact(en) online',

        // Omhoog-knop
        'back_to_top'               => 'Terug naar boven',

        // Footer
        'all_rights_reserved'       => 'Alle rechten voorbehouden.',
        'patch_notes'               => 'Patchnotities',
        'server_settings'           => 'Serverinstellingen',
        'help'                      => 'Hulp',
        'rules'                     => 'Regels',
        'legal'                     => 'Juridisch',
        'board'                     => 'Forum',

        // JS — jsloca
        'js_internal_error'         => 'Er is een onbekende fout opgetreden. Helaas kon je laatste actie niet worden uitgevoerd!',
        'js_notify_info'            => 'Info',
        'js_notify_success'         => 'Succes',
        'js_notify_warning'         => 'Waarschuwing',
        'js_combatsim_planning'     => 'Planning',
        'js_combatsim_pending'      => 'Simulatie loopt...',
        'js_combatsim_done'         => 'Voltooid',
        'js_msg_restore'            => 'herstellen',
        'js_msg_delete'             => 'verwijderen',
        'js_copied'                 => 'Gekopieerd naar klembord',
        'js_report_operator'        => 'Dit bericht melden aan een speloperator?',

        // JS — LocalizationStrings
        'js_time_done'              => 'klaar',
        'js_question'               => 'Vraag',
        'js_ok'                     => 'Ok',
        'js_outlaw_warning'         => 'Je staat op het punt een sterkere speler aan te vallen. Als je dit doet, worden je aanvalsverdedigingen 7 dagen uitgeschakeld en kunnen alle spelers je zonder straf aanvallen. Weet je zeker dat je wilt doorgaan?',
        'js_last_slot_moon'         => 'Dit gebouw gebruikt de laatste beschikbare bouwplaats. Vergroot je Maanbase voor meer ruimte. Weet je zeker dat je dit gebouw wilt bouwen?',
        'js_last_slot_planet'       => 'Dit gebouw gebruikt de laatste beschikbare bouwplaats. Vergroot je Terraformer of koop een Planetenveld-item voor meer plaatsen. Weet je zeker dat je dit gebouw wilt bouwen?',
        'js_forced_vacation'        => 'Sommige spelfuncties zijn niet beschikbaar totdat je account is gevalideerd.',
        'js_more_details'           => 'Meer details',
        'js_less_details'           => 'Minder detail',
        'js_planet_lock'            => 'Indeling vergrendelen',
        'js_planet_unlock'          => 'Indeling ontgrendelen',
        'js_activate_item_question' => 'Wil je het bestaande item vervangen? De oude bonus gaat verloren.',
        'js_activate_item_header'   => 'Item vervangen?',

        // JS — chatLoca
        'chat_text_empty'           => 'Waar is het bericht?',
        'chat_text_too_long'        => 'Het bericht is te lang.',
        'chat_same_user'            => 'Je kunt niet naar jezelf schrijven.',
        'chat_ignored_user'         => 'Je hebt deze speler genegeerd.',
        'chat_not_activated'        => 'Deze functie is alleen beschikbaar na activering van je account.',
        'chat_new_chats'            => '#+# ongelezen bericht(en)',
        'chat_more_users'           => 'meer weergeven',

        // JS — eventboxLoca
        'eventbox_mission'          => 'Missie',
        'eventbox_missions'         => 'Missies',
        'eventbox_next'             => 'Volgende',
        'eventbox_type'             => 'Type',
        'eventbox_own'              => 'eigen',
        'eventbox_friendly'         => 'bevriend',
        'eventbox_hostile'          => 'vijandig',

        // JS — planetMoveLoca
        'planet_move_ask_title'     => 'Planeet herplaatsen',
        'planet_move_ask_cancel'    => 'Weet je zeker dat je deze planeetverplaatsing wilt annuleren? De normale wachttijd blijft van kracht.',
        'planet_move_success'       => 'De planeetverplaatsing is succesvol geannuleerd.',

        // JS — locaPremium
        'premium_building_half'     => 'Wil je de bouwtijd met 50% van de totale bouwtijd () verminderen voor <b>750 Donkere Materie<\/b>?',
        'premium_building_full'     => 'Wil je de bouworder onmiddellijk voltooien voor <b>750 Donkere Materie<\/b>?',
        'premium_ships_half'        => 'Wil je de bouwtijd met 50% van de totale bouwtijd () verminderen voor <b>750 Donkere Materie<\/b>?',
        'premium_ships_full'        => 'Wil je de bouworder onmiddellijk voltooien voor <b>750 Donkere Materie<\/b>?',
        'premium_research_half'     => 'Wil je de onderzoekstijd met 50% van de totale onderzoekstijd () verminderen voor <b>750 Donkere Materie<\/b>?',
        'premium_research_full'     => 'Wil je de onderzoeksopdracht onmiddellijk voltooien voor <b>750 Donkere Materie<\/b>?',

        // JS — loca object
        'loca_error_not_enough_dm'  => 'Niet genoeg Donkere Materie beschikbaar! Wil je nu wat kopen?',
        'loca_notice'               => 'Referentie',
        'loca_planet_giveup'        => 'Weet je zeker dat je planeet %planetName% %planetCoordinates% wilt verlaten?',
        'loca_moon_giveup'          => 'Weet je zeker dat je maan %planetName% %planetCoordinates% wilt verlaten?',
    ],

    // ── Highscore ───────────────────────────────────────────────────────────
    'highscore' => [
        'player_highscore'      => 'Spelersranglijst',
        'alliance_highscore'    => 'Alliantieranglijst',
        'own_position'          => 'Eigen positie',
        'own_position_hidden'   => 'Eigen positie (-)',
        'points'                => 'Punten',
        'economy'               => 'Economie',
        'research'              => 'Onderzoek',
        'military'              => 'Militair',
        'military_built'        => 'Militaire punten gebouwd',
        'military_destroyed'    => 'Militaire punten vernietigd',
        'military_lost'         => 'Militaire punten verloren',
        'honour_points'         => 'Eerepunten',
        'position'              => 'Positie',
        'player_name_honour'    => 'Spelernaam (Eerepunten)',
        'action'                => 'Actie',
        'alliance'              => 'Alliantie',
        'member'                => 'Lid',
        'average_points'        => 'Gemiddelde punten',
        'no_alliances_found'    => 'Geen allianties gevonden',
        'write_message'         => 'Schrijf bericht',
        'buddy_request'         => 'Vriendschapsverzoek',
        'buddy_request_to'      => 'Vriendschapsverzoek aan',
        'total_ships'           => 'Totaal schepen',
        'buddy_request_sent'    => 'Vriendschapsverzoek succesvol verzonden!',
        'buddy_request_failed'  => 'Vriendschapsverzoek verzenden mislukt.',
        'are_you_sure_ignore'   => 'Weet je zeker dat je wilt negeren',
        'player_ignored'        => 'Speler succesvol genegeerd!',
        'player_ignored_failed' => 'Speler negeren mislukt.',
    ],

    // ── Premium / Officieren ────────────────────────────────────────────────
    'premium' => [
        'recruit_officers'           => 'Officieren werven',
        'your_officers'              => 'Jouw officieren',
        'intro_text'                 => 'Met jouw officieren kun je jouw imperium leiden naar een omvang voorbij je stoutste dromen - alles wat je nodig hebt is wat Donkere Materie en jouw werkers en adviseurs zullen nog harder werken!',
        'info_dark_matter'           => 'Meer informatie over: Donkere Materie',
        'info_commander'             => 'Meer informatie over: Commandant',
        'info_admiral'               => 'Meer informatie over: Admiraal',
        'info_engineer'              => 'Meer informatie over: Ingenieur',
        'info_geologist'             => 'Meer informatie over: Geoloog',
        'info_technocrat'            => 'Meer informatie over: Technocraat',
        'info_commanding_staff'      => 'Meer informatie over: Commandostaf',
        'hire_commander_tooltip'     => 'Commandant inhuren|+40 favorieten, bouwrij, snelkoppelingen, transportscanner, reclamevrij* <span style=\'font-size: 10px; line-height: 10px\'>(*uitgezonderd: spelgerelateerde referenties)</span>',
        'hire_admiral_tooltip'       => "Admiraal inhuren|Max. vlootslots +2,\nMax. expedities +1,\nVerbeterde vluchtsnelheid vloot,\nOpslagslots gevechtssimulatie +20",
        'hire_engineer_tooltip'      => 'Ingenieur inhuren|Halveert verliezen bij verdedigingen, +10% energieproductie',
        'hire_geologist_tooltip'     => 'Geoloog inhuren|+10% mijnproductie',
        'hire_technocrat_tooltip'    => 'Technocraat inhuren|+2 spionageniveaus, 25% minder onderzoekstijd',
        'remaining_officers'         => ':current van :max',
        'benefit_fleet_slots_title'  => 'Je kunt meer vloten tegelijkertijd sturen.',
        'benefit_fleet_slots'        => 'Max. vlootslots +1',
        'benefit_energy_title'       => 'Jouw energiecentrales en zonnesatellieten produceren 2% meer energie.',
        'benefit_energy'             => '+2% energieproductie',
        'benefit_mines_title'        => 'Jouw mijnen produceren 2% meer.',
        'benefit_mines'              => '+2% mijnproductie',
        'benefit_espionage_title'    => '1 niveau wordt toegevoegd aan jouw spionageonderzoek.',
        'benefit_espionage'          => '+1 spionageniveaus',
    ],

    // ── Shop ────────────────────────────────────────────────────────────────
    'shop' => [
        'page_title'               => 'Shop',
        'tooltip_shop'             => 'Je kunt hier items kopen.',
        'tooltip_inventory'        => 'Hier kun je een overzicht zien van je gekochte items.',
        'btn_shop'                 => 'Shop',
        'btn_inventory'            => 'Inventaris',
        'category_special_offers'  => 'Speciale aanbiedingen',
        'category_all'             => 'alle',
        'category_resources'       => 'Grondstoffen',
        'category_buddy_items'     => 'Buddy-items',
        'category_construction'    => 'Constructie',
        'btn_get_more_resources'   => 'Meer grondstoffen kopen',
        'btn_purchase_dark_matter' => 'Donkere Materie kopen',
        'feature_coming_soon'      => 'Functie komt binnenkort.',
        // Item niveaus
        'tier_gold'                => 'Goud',
        'tier_silver'              => 'Zilver',
        'tier_bronze'              => 'Brons',
        // Tooltip-labels in itemkaarten
        'tooltip_duration'         => 'Duur',
        'duration_now'             => 'nu',
        'tooltip_price'            => 'Prijs',
        'tooltip_in_inventory'     => 'In inventaris',
        'dark_matter'              => 'Donkere Materie',
        'dm_abbreviation'          => 'DM',
        'item_duration'            => 'Looptijd',
        'now'                      => 'nu',
        'item_price'               => 'Prijs',
        'item_in_inventory'        => 'In inventaris',
        // JS loca-sleutels (gebruikt door inventory.js)
        'loca_extend'              => 'Verlengen',
        'loca_activate'            => 'Activeren',
        'loca_buy_activate'        => 'Kopen en activeren',
        'loca_buy_extend'          => 'Kopen en verlengen',
        'loca_buy_dm'              => 'Je hebt niet genoeg Donkere Materie. Wil je er nu wat kopen?',
    ],

    // -------------------------------------------------------------------------
    // Zoek-overlay
    // -------------------------------------------------------------------------

    'search' => [
        'input_hint'              => 'Voer speler-, alliantie- of planeetnaam in',
        'search_btn'              => 'Zoeken',
        'tab_players'             => 'Spelernamen',
        'tab_alliances'           => 'Allianties/Tags',
        'tab_planets'             => 'Planeetnamen',
        'no_search_term'          => 'Geen zoekterm ingevoerd',
        'searching'               => 'Zoeken...',
        'search_failed'           => 'Zoeken mislukt. Probeer het opnieuw.',
        'no_results'              => 'Geen resultaten gevonden',
        'player_name'             => 'Spelernaam',
        'planet_name'             => 'Planeetnaam',
        'coordinates'             => 'Coördinaten',
        'tag'                     => 'Tag',
        'alliance_name'           => 'Alliantienaam',
        'member'                  => 'Leden',
        'points'                  => 'Punten',
        'action'                  => 'Actie',
        'apply_for_alliance'      => 'Solliciteer bij deze alliantie',
    ],

    // -------------------------------------------------------------------------
    // Notitie-overlay
    // -------------------------------------------------------------------------

    'notes' => [
        'no_notes_found'          => 'Geen notities gevonden',
    ],

    // -------------------------------------------------------------------------
    // Planeet verlaten/hernoemen overlay
    // -------------------------------------------------------------------------

    'planet_abandon' => [
        // Paginabeschrijving
        'description'                   => 'Via dit menu kun je planeetnamen en manen wijzigen of ze volledig verlaten.',

        // Hernoemen sectie
        'rename_heading'                => 'Hernoemen',
        'new_planet_name'               => 'Nieuwe planeetnaam',
        'new_moon_name'                 => 'Nieuwe naam van de maan',
        'rename_btn'                    => 'Hernoemen',

        // Tooltips (HTML-inhoud – {{ }} codeert automatisch in title-attributen)
        'tooltip_rules_title'           => 'Regels',
        'tooltip_rename_planet'         => 'Je kunt je planeet hier hernoemen.<br /><br />De planeetnaam moet tussen de <span style="font-weight: bold;">2 en 20 tekens</span> lang zijn.<br />Planeetnamen mogen bestaan uit kleine en grote letters en cijfers.<br />Ze mogen koppeltekens, underscores en spaties bevatten - deze mogen echter niet als volgt worden geplaatst:<br />- aan het begin of einde van de naam<br />- direct naast elkaar<br />- meer dan drie keer in de naam',
        'tooltip_rename_moon'           => 'Je kunt je maan hier hernoemen.<br /><br />De maannaam moet tussen de <span style="font-weight: bold;">2 en 20 tekens</span> lang zijn.<br />Maannamen mogen bestaan uit kleine en grote letters en cijfers.<br />Ze mogen koppeltekens, underscores en spaties bevatten - deze mogen echter niet als volgt worden geplaatst:<br />- aan het begin of einde van de naam<br />- direct naast elkaar<br />- meer dan drie keer in de naam',

        // Verlaten sectiekoppen
        'abandon_home_planet'           => 'Thuisplaneet verlaten',
        'abandon_moon'                  => 'Maan verlaten',
        'abandon_colony'                => 'Kolonie verlaten',
        'abandon_home_planet_btn'       => 'Thuisplaneet verlaten',
        'abandon_moon_btn'              => 'Maan verlaten',
        'abandon_colony_btn'            => 'Kolonie verlaten',

        // Waarschuwingen bij verlaten
        'home_planet_warning'           => 'Als je je thuisplaneet verlaat, word je bij je volgende login doorgestuurd naar de planeet die je daarna hebt gekoloniseerd.',
        'items_lost_moon'               => 'Als je items hebt geactiveerd op een maan, gaan deze verloren als je de maan verlaat.',
        'items_lost_planet'             => 'Als je items hebt geactiveerd op een planeet, gaan deze verloren als je de planeet verlaat.',

        // Formulier bevestiging verlaten
        'confirm_password'              => 'Bevestig de verwijdering van :type [:coordinates] door je wachtwoord in te voeren',
        'confirm_btn'                   => 'Bevestigen',
        'type_moon'                     => 'maan',
        'type_planet'                   => 'planeet',

        // Validatieberichten (JS)
        'validation_min_chars'          => 'Niet genoeg tekens',
        'validation_pw_min'             => 'Het ingevoerde wachtwoord is te kort (min. 4 tekens)',
        'validation_pw_max'             => 'Het ingevoerde wachtwoord is te lang (max. 20 tekens)',
        'validation_email'              => 'Je moet een geldig e-mailadres invoeren!',
        'validation_special'            => 'Bevat ongeldige tekens.',
        'validation_underscore'         => 'Je naam mag niet beginnen of eindigen met een underscore.',
        'validation_hyphen'             => 'Je naam mag niet beginnen of eindigen met een koppelteken.',
        'validation_space'              => 'Je naam mag niet beginnen of eindigen met een spatie.',
        'validation_max_underscores'    => 'Je naam mag niet meer dan 3 underscores in totaal bevatten.',
        'validation_max_hyphens'        => 'Je naam mag niet meer dan 3 koppeltekens bevatten.',
        'validation_max_spaces'         => 'Je naam mag niet meer dan 3 spaties in totaal bevatten.',
        'validation_consec_underscores' => 'Je mag niet twee of meer underscores achter elkaar gebruiken.',
        'validation_consec_hyphens'     => 'Je mag niet twee of meer koppeltekens achter elkaar gebruiken.',
        'validation_consec_spaces'      => 'Je mag niet twee of meer spaties achter elkaar gebruiken.',

        // Controllerberichten
        'msg_invalid_planet_name'       => 'De nieuwe planeetnaam is ongeldig. Probeer het opnieuw.',
        'msg_invalid_moon_name'         => 'De nieuwe maannaam is ongeldig. Probeer het opnieuw.',
        'msg_planet_renamed'            => 'Planeet succesvol hernoemd.',
        'msg_moon_renamed'              => 'Maan succesvol hernoemd.',
        'msg_wrong_password'            => 'Verkeerd wachtwoord!',
        'msg_confirm_title'             => 'Bevestigen',
        'msg_confirm_deletion'          => 'Als je de verwijdering van :type [:coordinates] (:name) bevestigt, worden alle gebouwen, schepen en verdedigingssystemen op die :type van je account verwijderd. Als je actieve items op je :type hebt, gaan deze ook verloren wanneer je de :type opgeeft. Dit proces kan niet ongedaan worden gemaakt!',
        'msg_reference'                 => 'Melding',
        'msg_abandoned'                 => ':type is succesvol verlaten!',
        'msg_type_moon'                 => 'Maan',
        'msg_type_planet'               => 'Planeet',
        'msg_yes'                       => 'Ja',
        'msg_no'                        => 'Nee',
        'msg_ok'                        => 'Ok',
    ],
];
