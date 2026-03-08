<?php

return [
    // -------------------------------------------------------------------------
    // Pagina panoramica
    // -------------------------------------------------------------------------

    'overview' => [
        // Pannello statistiche pianeta (animazione macchina da scrivere)
        'diameter'             => 'Diametro',
        'temperature'          => 'Temperatura',
        'position'             => 'Posizione',
        'points'               => 'Punti',
        'honour_points'        => 'Punti onore',
        'score_place'          => 'Posto',
        'score_of'             => 'di',

        // Intestazioni pagina / sezione
        'page_title'           => 'Panoramica',
        'buildings'            => 'Edifici',
        'research'             => 'Ricerche',

        // Pulsanti intestazione pianeta
        'switch_to_moon'       => 'Passa alla luna',
        'switch_to_planet'     => 'Passa al pianeta',
        'abandon_rename'       => 'Abbandona/Rinomina',
        'abandon_rename_title' => 'Abbandona/Rinomina Pianeta',
    ],

    // -------------------------------------------------------------------------
    // Trasferimento pianeta
    // -------------------------------------------------------------------------

    'planet_move' => [
        'resettle_title' => 'Trasferimento Pianeta',
        'cancel_confirm' => 'Sei sicuro di voler annullare il trasferimento del pianeta? La posizione riservata verrà rilasciata.',
        'cancel_success' => 'Il trasferimento del pianeta è stato annullato con successo.',
        'blockers_title' => 'Le seguenti cose impediscono attualmente il trasferimento del pianeta:',
        'no_blockers'    => 'Niente può ostacolare il trasferimento pianificato del pianeta ora.',
        'cooldown_title' => 'Tempo fino al prossimo trasferimento possibile',
        'to_galaxy'      => 'Alla galassia',
        'relocate'       => 'Trasferisci',
        'cancel'         => 'annulla',
        'explanation'    => 'Il trasferimento ti permette di spostare i tuoi pianeti in un\'altra posizione in un sistema lontano a tua scelta.<br /><br />Il trasferimento effettivo avviene per la prima volta 24 ore dopo l\'attivazione. In questo periodo puoi usare i tuoi pianeti normalmente. Un conto alla rovescia mostra quanto tempo rimane prima del trasferimento.<br /><br />Una volta scaduto il conto alla rovescia e il pianeta deve essere spostato, nessuna delle tue flotte stazionate lì può essere attiva. A questo punto non deve essere in costruzione nulla, nulla in riparazione e nulla in ricerca. Se ci sono attività di costruzione, riparazione o flotte ancora attive alla scadenza del conto alla rovescia, il trasferimento verrà annullato.<br /><br />Se il trasferimento ha successo, ti verranno addebitati 240.000 Materia Oscura. I pianeti, gli edifici e le risorse immagazzinate inclusa la luna verranno spostati immediatamente. Le tue flotte viaggiano automaticamente verso le nuove coordinate alla velocità della nave più lenta. Il portale di salto verso una luna trasferita viene disattivato per 24 ore.',
    ],

    // -------------------------------------------------------------------------
    // Stringhe UI condivise (pulsanti, etichette dialoghi)
    // -------------------------------------------------------------------------

    'shared' => [
        'caution' => 'Attenzione',
        'yes'     => 'sì',
        'no'      => 'No',
        'error'   => 'Errore',
    ],

    // -------------------------------------------------------------------------
    // Stringhe condivise pagine edifici (risorse, strutture, ricerche, cantiere, difesa)
    // -------------------------------------------------------------------------

    'buildings' => [
        // Tooltip di stato icone edificio
        'under_construction'     => 'In costruzione',
        'vacation_mode_error'    => 'Errore, il giocatore è in modalità vacanza',
        'requirements_not_met'   => 'I requisiti non sono soddisfatti!',
        'wrong_class'            => 'Classe personaggio sbagliata!',
        'no_moon_building'       => "Non puoi costruire quell'edificio sulla luna!",
        'not_enough_resources'   => 'Risorse insufficienti!',
        'queue_full'             => 'La coda è piena',
        'not_enough_fields'      => 'Campi insufficienti!',
        'shipyard_busy'          => 'Il cantiere spaziale è ancora occupato',
        'research_in_progress'   => 'È in corso una ricerca!',
        'research_lab_expanding' => 'Il laboratorio di ricerca è in espansione.',
        'shipyard_upgrading'     => 'Il cantiere spaziale è in aggiornamento.',
        'nanite_upgrading'       => 'La fabbrica di naniti è in aggiornamento.',
        'max_amount_reached'     => 'Numero massimo raggiunto!',
        // Pulsante espandi (parametri nominali: :title, :level)
        'expand_button'          => 'Espandi :title al livello :level',
        // Stringhe oggetto JS loca
        'loca_notice'            => 'Riferimento',
        'loca_demolish'          => 'Vuoi davvero declassare TECHNOLOGY_NAME di un livello?',
        'loca_lifeform_cap'      => 'Uno o più bonus associati hanno già raggiunto il massimo. Vuoi continuare la costruzione comunque?',
        'last_inquiry_error'     => "Impossibile elaborare l'ultima azione. Per favore riprova.",
        'planet_move_warning'    => 'Attenzione! Questa missione potrebbe essere ancora in corso quando inizia il periodo di ricollocazione e, in tal caso, il processo verrà annullato. Vuoi davvero continuare con questo lavoro?',
    ],

    // -------------------------------------------------------------------------
    // Pagina risorse (miniere / edifici di stoccaggio)
    // -------------------------------------------------------------------------

    'resources_page' => [
        'page_title'    => 'Risorse',
        'settings_link' => 'Impostazioni risorse',
        'section_title' => 'Edifici delle risorse',
    ],

    // -------------------------------------------------------------------------
    // Pagina strutture
    // -------------------------------------------------------------------------

    'facilities_page' => [
        'page_title'     => 'Strutture',
        'section_title'  => 'Strutture di supporto',
        'use_jump_gate'  => 'Usa Portale di Salto',
        'jump_gate'      => 'Portale di Salto',
        'alliance_depot' => 'Deposito Alleanza',
        'burn_confirm'   => 'Sei sicuro di voler bruciare questo campo di rottami? Questa azione non può essere annullata.',
    ],

    // -------------------------------------------------------------------------
    // Pagina ricerche
    // -------------------------------------------------------------------------

    'research_page' => [
        'basic'    => 'Ricerca di base',
        'drive'    => 'Ricerca dei propulsori',
        'advanced' => 'Ricerche avanzate',
        'combat'   => 'Ricerca da combattimento',
    ],

    // -------------------------------------------------------------------------
    // Pagina cantiere spaziale
    // -------------------------------------------------------------------------

    'shipyard_page' => [
        'battleships' => 'Astronavi da guerra',
        'civil_ships' => 'Navi civili',
    ],

    // -------------------------------------------------------------------------
    // Pagina difesa
    // -------------------------------------------------------------------------

    'defense_page' => [
        'page_title'    => 'Difesa',
        'section_title' => 'Strutture difensive',
    ],

    // -------------------------------------------------------------------------
    // Pagina impostazioni risorse
    // -------------------------------------------------------------------------

    'resource_settings' => [
        'production_factor'  => 'Fattore di produzione',
        'recalculate'        => 'Ricalcola',
        'metal'              => 'Metallo',
        'crystal'            => 'Cristallo',
        'deuterium'          => 'Deuterio',
        'energy'             => 'Energia',
        'basic_income'       => 'Reddito base',
        'level'              => 'Livello',
        'number'             => 'Numero:',
        'items'              => 'Oggetti',
        'geologist'          => 'Geologo',
        'mine_production'    => 'produzione miniere',
        'engineer'           => 'Ingegnere',
        'energy_production'  => 'produzione energetica',
        'character_class'    => 'Classe personaggio',
        'commanding_staff'   => 'Stato maggiore',
        'storage_capacity'   => 'Capacità di stoccaggio',
        'total_per_hour'     => 'Totale orario:',
        'total_per_day'      => 'Totale giornaliero',
        'total_per_week'     => 'Totale settimanale:',
    ],

    // -------------------------------------------------------------------------
    // Dialogo distruzione razzi (pagina strutture)
    // -------------------------------------------------------------------------

    'facilities_destroy' => [
        'silo_description'  => 'I silos missilistici vengono usati per costruire, immagazzinare e lanciare missili interplanetari e antibalistici. Con ogni livello del silo, possono essere immagazzinati cinque missili interplanetari o dieci missili antibalistici. Un missile interplanetario occupa lo stesso spazio di due missili antibalistici. È consentito immagazzinare sia missili interplanetari che antibalistici nello stesso silo.',
        'silo_capacity'     => 'Un silo missilistico al livello :level può contenere :ipm missili interplanetari o :abm missili antibalistici.',
        'type'              => 'Tipo',
        'number'            => 'Numero',
        'tear_down'         => 'demolisci',
        'proceed'           => 'Procedi',
        'enter_minimum'     => 'Inserisci almeno un missile da distruggere',
        'not_enough_abm'    => 'Non hai abbastanza missili antibalistici',
        'not_enough_ipm'    => 'Non hai abbastanza missili interplanetari',
        'destroyed_success' => 'Missili distrutti con successo',
        'destroy_failed'    => 'Impossibile distruggere i missili',
        'error'             => 'Si è verificato un errore. Per favore riprova.',
    ],

    // -------------------------------------------------------------------------
    // Pagine flotta (invio + movimento)
    // -------------------------------------------------------------------------

    'fleet' => [
        // Intestazioni pagina / step
        'dispatch_1_title'         => 'Invio flotta I',
        'dispatch_2_title'         => 'Invio flotta II',
        'dispatch_3_title'         => 'Invio flotta III',
        'movement_title'           => 'Movimento flotta',
        'to_movement'              => 'Al movimento flotta',

        // Barra di stato
        'fleets'                   => 'Flotte',
        'expeditions'              => 'Spedizioni',
        'reload'                   => 'Ricarica',
        'clock'                    => 'Ore',
        'load_dots'                => 'caricamento...',
        'never'                    => 'Mai',

        // Slot flotta
        'tooltip_slots'            => 'Usati/Totali slot flotta',
        'no_free_slots'            => 'Nessuno slot flotta disponibile',
        'tooltip_exp_slots'        => 'Usati/Totali slot spedizione',
        'market_slots'             => 'Offerte',
        'tooltip_market_slots'     => 'Usati/Totali flotte commerciali',

        // Avvisi / stati impossibili
        'fleet_dispatch'           => 'Invio flotta',
        'dispatch_impossible'      => 'Invio flotta impossibile',
        'no_ships'                 => 'Non ci sono navi su questo pianeta.',
        'in_combat'                => 'La flotta è attualmente in combattimento.',
        'vacation_error'           => 'Non è possibile inviare flotte dalla modalità vacanza!',
        'not_enough_deuterium'     => 'Deuterio insufficiente!',
        'no_target'                => 'Devi selezionare un bersaglio valido.',
        'cannot_send_to_target'    => 'Non è possibile inviare flotte a questo bersaglio.',
        'cannot_start_mission'     => 'Non puoi avviare questa missione.',

        // Etichette barra di stato (senza due punti finali)
        'mission_label'            => 'Missione',
        'target_label'             => 'Bersaglio',
        'player_name_label'        => 'Nome giocatore',
        'no_selection'             => 'Nessuna selezione',
        'no_mission_selected'      => 'Nessuna missione selezionata!',

        // Step 1 – selezione navi
        'combat_ships'             => 'Navi da guerra',
        'civil_ships'              => 'Navi civili',
        'standard_fleets'          => 'Flotte standard',
        'edit_standard_fleets'     => 'Modifica flotte standard',
        'select_all_ships'         => 'Seleziona tutte le navi',
        'reset_choice'             => 'Azzera selezione',
        'api_data'                 => 'Questi dati possono essere inseriti in un simulatore di combattimento compatibile:',
        'tactical_retreat'         => 'Ritirata tattica',
        'tactical_retreat_tooltip' => 'Mostra il consumo di Deuterio per ritirata tattica',
        'continue'                 => 'Continua',
        'back'                     => 'Indietro',

        // Step 2 – destinazione
        'origin'                   => 'Origine',
        'destination'              => 'Destinazione',
        'planet'                   => 'Pianeta',
        'moon'                     => 'Luna',
        'coordinates'              => 'Coordinate',
        'distance'                 => 'Distanza',
        'debris_field'             => 'Campo di detriti',
        'debris_field_lower'       => 'campo di detriti',
        'shortcuts'                => 'Scorciatoie',
        'combat_forces'            => 'Forze di combattimento',
        'player_label'             => 'Giocatore',
        'player_name'              => 'Nome giocatore',

        // Step 3 – selezione missione
        'select_mission'           => 'Seleziona missione per il bersaglio',
        'bashing_disabled'         => 'Le missioni di attacco sono state disattivate a causa di troppi attacchi al bersaglio.',

        // Nomi missione
        'mission_expedition'       => 'Spedizione',
        'mission_colonise'         => 'Colonizzazione',
        'mission_recycle'          => 'Raccogliere campo di detriti',
        'mission_transport'        => 'Trasporto',
        'mission_deploy'           => 'Stazionamento',
        'mission_espionage'        => 'Spionaggio',
        'mission_acs_defend'       => 'Difesa ACS',
        'mission_attack'           => 'Attacco',
        'mission_acs_attack'       => 'Attacco ACS',
        'mission_destroy_moon'     => 'Distruzione luna',

        // Descrizioni missione
        'desc_attack'              => 'Attacca la flotta e le difese dell\'avversario.',
        'desc_acs_attack'          => 'Le battaglie onorevoli possono diventare disonorevoli se giocatori forti entrano tramite ACS. Il fattore decisivo è la somma dei punti militari totali dell\'attaccante rispetto a quelli del difensore.',
        'desc_transport'           => 'Trasporta le tue risorse su altri pianeti.',
        'desc_deploy'              => 'Invia la tua flotta permanentemente su un altro pianeta del tuo impero.',
        'desc_acs_defend'          => 'Difendi il pianeta del tuo compagno di squadra.',
        'desc_espionage'           => 'Spia i mondi degli altri imperatori.',
        'desc_colonise'            => 'Colonizza un nuovo pianeta.',
        'desc_recycle'             => 'Invia i tuoi riciclatori in un campo di detriti per raccogliere le risorse.',
        'desc_destroy_moon'        => 'Distrugge la luna del tuo nemico.',
        'desc_expedition'          => 'Invia le tue navi ai confini dello spazio per compiere missioni emozionanti.',

        // Sezione briefing (senza due punti finali)
        'briefing'                 => 'Briefing',
        'load_resources'           => 'Carica risorse',
        'load_all_resources'       => 'Carica tutte le risorse',
        'all_resources'            => 'tutte le risorse',
        'flight_duration'          => 'Durata del volo (solo andata)',
        'federation_duration'      => 'Durata del volo (unione flotta)',
        'arrival'                  => 'Arrivo',
        'return_trip'              => 'Ritorno',
        'speed'                    => 'Velocità:',
        'max_abbr'                 => 'max.',
        'hour_abbr'                => 'h',
        'deuterium_consumption'    => 'Consumo di Deuterio',
        'empty_cargobays'          => 'Stiva libera',
        'hold_time'                => 'Tempo di stazionamento',
        'expedition_duration'      => 'Durata della spedizione',
        'cargo_bay'                => 'stiva',
        'cargo_space'              => 'Spazio disponibile / Stiva max.',
        'send_fleet'               => 'Invia flotta',
        'retreat_on_defender'      => 'Ritorno in caso di ritirata del difensore',
        'retreat_tooltip'          => 'Se questa opzione è attivata, la tua flotta si ritirerà senza combattere se il tuo avversario fugge.',
        'plunder_food'             => 'Saccheggia cibo',

        // Etichette risorse (per oggetto loca)
        'metal'                    => 'Metallo',
        'crystal'                  => 'Cristallo',
        'deuterium'                => 'Deuterio',

        // Pagina movimento flotta
        'fleet_details'            => 'Dettagli flotta',
        'ships'                    => 'Navi',
        'shipment'                 => 'Carico',
        'recall'                   => 'Richiama',
        'start_time'               => 'Ora di partenza',
        'time_of_arrival'          => 'Ora di arrivo',
        'deep_space'               => 'Spazio profondo',

        // Indicatori stato bersaglio / giocatore
        'uninhabited_planet'       => 'Pianeta disabitato',
        'no_debris_field'          => 'Nessun campo di detriti',
        'player_vacation'          => 'Giocatore in modalità vacanza',
        'admin_gm'                 => 'Admin o GM',
        'noob_protection'          => 'Protezione principianti',
        'player_too_strong'        => 'Questo pianeta non può essere attaccato perché il giocatore è troppo forte!',
        'no_moon'                  => 'Nessuna luna disponibile.',
        'no_recycler'              => 'Nessun riciclatore disponibile.',
        'no_events'                => 'Nessun evento in corso.',
        'planet_already_reserved'  => 'Questo pianeta è già stato riservato per una ricollocazione.',
        'max_planet_warning'       => 'Attenzione! Al momento non è possibile colonizzare altri pianeti. Per ogni nuova colonia sono necessari due livelli di astrofisica. Vuoi comunque inviare la tua flotta?',

        // Galassia / rete
        'empty_systems'            => 'Sistemi vuoti',
        'inactive_systems'         => 'Sistemi inattivi',
        'network_on'               => 'Sì',
        'network_off'              => 'No',

        // Codici errore (usati in errorCodeMap)
        'err_generic'              => 'Si è verificato un errore',
        'err_no_moon'              => 'Errore, non c\'è luna',
        'err_newbie_protection'    => 'Errore, il giocatore non può essere raggiunto a causa della protezione principianti',
        'err_too_strong'           => 'Il giocatore è troppo forte per essere attaccato',
        'err_vacation_mode'        => 'Errore, il giocatore è in modalità vacanza',
        'err_own_vacation'         => 'Non è possibile inviare flotte dalla modalità vacanza!',
        'err_not_enough_ships'     => 'Errore, navi insufficienti, invia il numero massimo:',
        'err_no_ships'             => 'Errore, nessuna nave disponibile',
        'err_no_slots'             => 'Errore, nessuno slot flotta libero',
        'err_no_deuterium'         => 'Errore, deuterio insufficiente',
        'err_no_planet'            => 'Errore, non c\'è nessun pianeta lì',
        'err_no_cargo'             => 'Errore, capacità di carico insufficiente',
        'err_multi_alarm'          => 'Multi-allarme',
        'err_attack_ban'           => 'Divieto di attacco',
    ],

    // -------------------------------------------------------------------------
    // Pagina Galassia
    // -------------------------------------------------------------------------

    'galaxy' => [
        // Modalità vacanza
        'vacation_error'               => 'Non è possibile usare la vista galassia in modalità vacanza!',

        // Navigazione / intestazione
        'system'                       => 'Sistema',
        'go'                           => 'Vai!',

        // Pulsanti azione sistema
        'system_phalanx'               => 'Phalanx di sistema',
        'system_espionage'             => 'Spionaggio di sistema',
        'discoveries'                  => 'Scoperte',
        'discoveries_tooltip'          => 'Avvia una missione di scoperta in tutte le posizioni disponibili',

        // Etichette riga statistiche intestazione
        'probes_short'                 => 'Sonda Sp.',
        'recycler_short'               => 'Ricic.',
        'ipm_short'                    => 'MIP.',
        'used_slots'                   => 'Slot usati',

        // Colonne intestazione tabella
        'planet_col'                   => 'Pianeta',
        'name_col'                     => 'Nome',
        'moon_col'                     => 'Luna',
        'debris_short'                 => 'CR',
        'player_status'                => 'Giocatore (Stato)',
        'alliance'                     => 'Alleanza',
        'action'                       => 'Azione',

        // Riga spedizione / spazio profondo
        'planets_colonized'            => 'Pianeti colonizzati',
        'expedition_fleet'             => 'Flotta spedizione',
        'admiral_needed'               => 'È necessario un Ammiraglio per usare questa funzione.',
        'send'                         => 'invia',

        // Tooltip legenda
        'legend'                       => 'Legenda',
        'status_admin_abbr'            => 'A',
        'legend_admin'                 => 'Amministratore',
        'status_strong_abbr'           => 'f',
        'legend_strong'                => 'giocatore più forte',
        'status_noob_abbr'             => 'd',
        'legend_noob'                  => 'giocatore più debole (principiante)',
        'status_outlaw_abbr'           => 'o',
        'legend_outlaw'                => 'Fuorilegge (temporaneo)',
        'status_vacation_abbr'         => 'v',
        'vacation_mode'                => 'Modalità vacanza',
        'status_banned_abbr'           => 'b',
        'legend_banned'                => 'bandito',
        'status_inactive_abbr'         => 'i',
        'legend_inactive_7'            => '7 giorni inattivo',
        'status_longinactive_abbr'     => 'I',
        'legend_inactive_28'           => '28 giorni inattivo',
        'status_honorable_abbr'        => 'hp',
        'legend_honorable'             => 'Bersaglio onorevole',

        // Oggetto JS loca (stringhe galassia uniche)
        'phalanx_restricted'           => 'Il phalanx di sistema può essere utilizzato solo dalla classe alleanza Ricercatore!',
        'astro_required'               => 'Devi prima ricercare l\'Astrofisica.',
        'galaxy_nav'                   => 'Galassia',
        'activity'                     => 'Attività',
        'no_action'                    => 'Nessuna azione disponibile.',
        'time_minute_abbr'             => 'm',
        'moon_diameter_km'             => 'Diametro della luna in km',
        'km'                           => 'km',
        'pathfinders_needed'           => 'Cercatori di percorsi necessari',
        'recyclers_needed'             => 'Riciclatori necessari',
        'mine_debris'                  => 'Estrai',
        'phalanx_no_deut'              => 'Deuterio insufficiente per usare il phalanx.',
        'use_phalanx'                  => 'Usa phalanx',
        'colonize_error'               => 'Non è possibile colonizzare un pianeta senza una nave colonia.',
        'ranking'                      => 'Classifica',
        'espionage_report'             => 'Rapporto di spionaggio',
        'missile_attack'               => 'Attacco missilistico',
        'rank'                         => 'Posizione',
        'alliance_member'              => 'Membro',
        'alliance_class'               => 'Classe alleanza',
        'espionage_not_possible'       => 'Spionaggio non possibile',
        'espionage'                    => 'Spionaggio',
        'hire_admiral'                 => 'Assumi ammiraglio',
        'dark_matter'                  => 'Materia oscura',
        'outlaw_explanation'           => 'Se sei un fuorilegge, non hai più alcuna protezione dagli attacchi e puoi essere attaccato da tutti i giocatori.',
        'honorable_target_explanation' => 'In battaglia contro questo bersaglio puoi ricevere punti onore e saccheggiare il 50% di bottino in più.',

        // Oggetto JS galaxyLoca
        'relocate_success'             => 'La posizione è stata riservata. La ricollocazione della colonia è iniziata.',
        'relocate_title'               => 'Ricolloca pianeta',
        'relocate_question'            => 'Sei sicuro di voler spostare il tuo pianeta in queste coordinate? Per finanziare la ricollocazione avrai bisogno di :cost Materia oscura.',
        'deut_needed_relocate'         => 'Deuterio insufficiente! Hai bisogno di 10 unità di deuterio.',
        'fleet_attacking'              => 'La flotta sta attaccando!',
        'fleet_underway'               => 'Flotta in rotta',
        'discovery_send'               => 'Invia nave da esplorazione',
        'discovery_success'            => 'Nave da esplorazione inviata',
        'discovery_unavailable'        => 'Non puoi inviare una nave da esplorazione in questa posizione.',
        'discovery_underway'           => 'Una nave da esplorazione è già diretta verso questo pianeta.',
        'discovery_locked'             => 'Non hai ancora sbloccato la ricerca per scoprire nuove forme di vita.',
        'discovery_title'              => 'Nave da esplorazione',
        'discovery_question'           => 'Vuoi inviare una nave da esplorazione su questo pianeta?<br/>Metallo: 5000 Cristallo: 1000 Deuterio: 500',

        // Dialogo risultati phalanx (stringhe JS nel blocco script Blade-rendered)
        'sensor_report'                => 'rapporto sensore',
        'refresh'                      => 'Aggiorna',
        'arrived'                      => 'Arrivata',

        // Dialogo attacco missilistico
        'target'                       => 'Bersaglio',
        'flight_duration'              => 'Durata del volo',
        'ipm_full'                     => 'Missili interplanetari',
        'primary_target'               => 'Bersaglio primario',
        'no_primary_target'            => 'Nessun bersaglio primario selezionato: bersaglio casuale',
        'target_has'                   => 'Il bersaglio ha',
        'abm_full'                     => 'Missili anti-balistici',
        'fire'                         => 'Fuoco',
        'valid_missile_count'          => 'Inserisci un numero valido di missili',
        'not_enough_missiles'          => 'Non hai abbastanza missili',
        'launched_success'             => 'Missili lanciati con successo!',
        'launch_failed'                => 'Lancio dei missili fallito',
    ],

    // -------------------------------------------------------------------------
    // Sistema buddy (richieste amicizia + ignora giocatore — usato nella pagina galassia)
    // -------------------------------------------------------------------------

    'buddy' => [
        'request_sent'   => 'Richiesta di amicizia inviata con successo!',
        'request_failed' => 'Invio della richiesta di amicizia non riuscito.',
        'request_to'     => 'Richiesta di amicizia a',
        'ignore_confirm' => 'Sei sicuro di voler ignorare',
        'ignore_success' => 'Giocatore ignorato con successo!',
        'ignore_failed'  => 'Impossibile ignorare il giocatore.',
    ],

    // -------------------------------------------------------------------------
    // Pagina messaggi
    // -------------------------------------------------------------------------

    'messages' => [
        // Tab principali
        'tab_fleets'        => 'Flotte',
        'tab_communication' => 'Comunicazione',
        'tab_economy'       => 'Economia',
        'tab_universe'      => 'Universo',
        'tab_system'        => 'OGame',
        'tab_favourites'    => 'Preferiti',

        // Subtab flotte
        'subtab_espionage'   => 'Spionaggio',
        'subtab_combat'      => 'Rapporti di combattimento',
        'subtab_expeditions' => 'Spedizioni',
        'subtab_transport'   => 'Unioni/Trasporto',
        'subtab_other'       => 'Altro',

        // Subtab comunicazione
        'subtab_messages'         => 'Messaggi',
        'subtab_information'      => 'Informazioni',
        'subtab_shared_combat'    => 'Rapporti di combattimento condivisi',
        'subtab_shared_espionage' => 'Rapporti di spionaggio condivisi',

        // UI generale
        'news_feed'          => 'Notizie',
        'loading'            => 'carico...',
        'error_occurred'     => 'Si è verificato un errore',
        'mark_favourite'     => 'segna come preferito',
        'remove_favourite'   => 'rimuovi dai preferiti',
        'from'               => 'Da',
        'no_messages'        => 'Non ci sono messaggi disponibili in questa scheda',
        'new_alliance_msg'   => 'Nuovo messaggio di alleanza',
        'to'                 => 'A',
        'all_players'        => 'tutti i giocatori',
        'send'               => 'invia',
        'delete_buddy_title' => 'Elimina amico',
        'report_to_operator' => 'Segnalare questo messaggio a un operatore di gioco?',
        'too_few_chars'      => 'Troppo pochi caratteri! Inserisci almeno 2 caratteri.',

        // Editor BBCode (localizedBBCode)
        'bbcode_bold'           => 'Grassetto',
        'bbcode_italic'         => 'Corsivo',
        'bbcode_underline'      => 'Sottolineato',
        'bbcode_stroke'         => 'Barrato',
        'bbcode_sub'            => 'Pedice',
        'bbcode_sup'            => 'Apice',
        'bbcode_font_color'     => 'Colore del testo',
        'bbcode_font_size'      => 'Dimensione del testo',
        'bbcode_bg_color'       => 'Colore di sfondo',
        'bbcode_bg_image'       => 'Immagine di sfondo',
        'bbcode_tooltip'        => 'Tooltip',
        'bbcode_align_left'     => 'Allinea a sinistra',
        'bbcode_align_center'   => 'Allinea al centro',
        'bbcode_align_right'    => 'Allinea a destra',
        'bbcode_align_justify'  => 'Giustifica',
        'bbcode_block'          => 'A capo',
        'bbcode_code'           => 'Codice',
        'bbcode_spoiler'        => 'Spoiler',
        'bbcode_moreopts'       => 'Altre opzioni',
        'bbcode_list'           => 'Elenco',
        'bbcode_hr'             => 'Linea orizzontale',
        'bbcode_picture'        => 'Immagine',
        'bbcode_link'           => 'Collegamento',
        'bbcode_email'          => 'Email',
        'bbcode_player'         => 'Giocatore',
        'bbcode_item'           => 'Oggetto',
        'bbcode_coordinates'    => 'Coordinate',
        'bbcode_preview'        => 'Anteprima',
        'bbcode_text_ph'        => 'Testo...',
        'bbcode_player_ph'      => 'ID o nome del giocatore',
        'bbcode_item_ph'        => 'ID oggetto',
        'bbcode_coord_ph'       => 'Galassia:sistema:posizione',
        'bbcode_chars_left'     => 'Caratteri rimanenti',
        'bbcode_ok'             => 'Ok',
        'bbcode_cancel'         => 'Annulla',
        'bbcode_repeat_x'       => 'Ripeti orizzontalmente',
        'bbcode_repeat_y'       => 'Ripeti verticalmente',

        // Rapporto di spionaggio
        'spy_player'            => 'Giocatore',
        'spy_activity'          => 'Attività',
        'spy_minutes_ago'       => 'minuti fa',
        'spy_class'             => 'Classe',
        'spy_unknown'           => 'Sconosciuto',
        'spy_alliance_class'    => 'Classe dell\'alleanza',
        'spy_no_alliance_class' => 'Nessuna classe di alleanza selezionata',
        'spy_resources'         => 'Risorse',
        'spy_loot'              => 'Bottino',
        'spy_counter_esp'       => 'Probabilità di controspionaggio',
        'spy_no_info'           => 'Non siamo stati in grado di recuperare informazioni affidabili di questo tipo dalla scansione.',
        'spy_debris_field'      => 'campo di detriti',
        'spy_no_activity'       => 'Il tuo spionaggio non mostra anomalie nell\'atmosfera del pianeta. Sembra che non ci sia stata alcuna attività sul pianeta nell\'ultima ora.',
        'spy_fleets'            => 'Flotte',
        'spy_defense'           => 'Difesa',
        'spy_research'          => 'Ricerca',
        'spy_building'          => 'Edificio',

        // Rapporto di battaglia (breve)
        'battle_attacker'    => 'Attaccante',
        'battle_defender'    => 'Difensore',
        'battle_resources'   => 'Risorse',
        'battle_loot'        => 'Bottino',
        'battle_debris_new'  => 'Campo di detriti (appena creato)',
        'battle_repaired'    => 'Difese riparate',
        'battle_moon_chance' => 'Probabilità di luna',

        // Rapporto di battaglia (completo)
        'battle_report'          => 'Rapporto di combattimento',
        'battle_planet'          => 'Pianeta',
        'battle_fleet_command'   => 'Comando flotta',
        'battle_from'            => 'Da',
        'battle_tactical_retreat' => 'Ritirata tattica',
        'battle_total_loot'      => 'Bottino totale',
        'battle_debris'          => 'Detriti (nuovi)',
        'battle_recycler'        => 'Riciclatore',
        'battle_mined_after'     => 'Raccolto dopo il combattimento',
        'battle_reaper'          => 'Mietitore',
        'battle_debris_left'     => 'Campi di detriti (rimanenti)',
        'battle_honour_points'   => 'Punti onore',
        'battle_dishonourable'   => 'Combattimento disonorevole',
        'battle_vs'              => 'vs',
        'battle_honourable'      => 'Combattimento onorevole',
        'battle_class'           => 'Classe',
        'battle_weapons'         => 'Armi',
        'battle_shields'         => 'Scudi',
        'battle_armour'          => 'Armatura',
        'battle_combat_ships'    => 'Navi da combattimento',
        'battle_civil_ships'     => 'Navi civili',
        'battle_defences'        => 'Difese',
        'battle_repaired_def'    => 'Difese riparate',
        'battle_share'           => 'condividi messaggio',
        'battle_attack'          => 'Attacca',
        'battle_espionage'       => 'Spionaggio',
        'battle_delete'          => 'elimina',
        'battle_favourite'       => 'segna come preferito',
        'battle_hamill'          => 'Un Caccia Leggero ha distrutto un Incrociatore Stellare prima dell\'inizio della battaglia!',
        'battle_retreat_tooltip'  => 'Le Stelle della Morte, le sonde spia, i satelliti solari e qualsiasi flotta in missione di difesa ACS non possono fuggire. Le ritirate tattiche sono disattivate anche nei combattimenti onorevoli. Una ritirata può anche essere stata disattivata manualmente o impedita dalla mancanza di deuterio. I banditi e i giocatori con più di 500.000 punti non si ritirano mai.',
        'battle_no_flee'         => 'La flotta in difesa non è fuggita.',
        'battle_rounds'          => 'Round',
        'battle_start'           => 'Inizio',
        'battle_player_from'     => 'da',
        'battle_attacker_fires'  => 'L\':attacker spara un totale di :hits colpi contro il :defender con una forza totale di :strength. Gli scudi del :defender2 assorbono :absorbed punti di danno.',
        'battle_defender_fires'  => 'Il :defender spara un totale di :hits colpi contro l\':attacker con una forza totale di :strength. Gli scudi dell\':attacker2 assorbono :absorbed punti di danno.',
    ],

    // -------------------------------------------------------------------------
    // Pagina Alleanza
    // -------------------------------------------------------------------------

    'alliance' => [
        // Pagina / navigazione
        'page_title'                    => 'Alleanza',
        'tab_overview'                  => 'Panoramica',
        'tab_management'                => 'Gestione',
        'tab_communication'             => 'Comunicazione',
        'tab_applications'              => 'Candidature',
        'tab_classes'                   => 'Classi Alleanza',
        'tab_create'                    => 'Crea alleanza',
        'tab_search'                    => 'Cerca alleanza',
        'tab_apply'                     => 'candidati',

        // Panoramica – tabella info alleanza
        'your_alliance'                 => 'La tua alleanza',
        'name'                          => 'Nome',
        'tag'                           => 'Tag',
        'created'                       => 'Creata',
        'member'                        => 'Membro',
        'your_rank'                     => 'Il tuo rango',
        'homepage'                      => 'Homepage',
        'logo'                          => 'Logo alleanza',
        'open_page'                     => 'Apri pagina alleanza',
        'highscore'                     => 'Classifica alleanza',
        'leave_wait_warning'            => 'Se lasci l\'alleanza, dovrai aspettare 3 giorni prima di unirti o creare un\'altra alleanza.',
        'leave_btn'                     => 'Lascia alleanza',

        // Panoramica – lista membri
        'member_list'                   => 'Lista Membri',
        'no_members'                    => 'Nessun membro trovato',
        'assign_rank_btn'               => 'Assegna rango',
        'kick_tooltip'                  => 'Espelli membro dall\'alleanza',
        'write_msg_tooltip'             => 'Scrivi messaggio',
        'col_name'                      => 'Nome',
        'col_rank'                      => 'Rango',
        'col_coords'                    => 'Coordinate',
        'col_joined'                    => 'Entrato',
        'col_online'                    => 'Online',
        'col_function'                  => 'Funzione',

        // Panoramica – aree testo
        'internal_area'                 => 'Area Interna',
        'external_area'                 => 'Area Esterna',

        // Gestione – privilegi
        'configure_privileges'          => 'Configura privilegi',
        'col_rank_name'                 => 'Nome rango',
        'col_applications_group'        => 'Candidature',
        'col_member_group'              => 'Membro',
        'col_alliance_group'            => 'Alleanza',
        'delete_rank'                   => 'Elimina rango',
        'save_btn'                      => 'Salva',
        'rights_warning_html'           => '<strong>Attenzione!</strong> Puoi assegnare solo i permessi che hai tu stesso.',
        'rights_warning_loca'           => '[b]Attenzione![/b] Puoi assegnare solo i permessi che hai tu stesso.',
        'rights_legend'                 => 'Legenda diritti',
        'create_rank_btn'               => 'Crea nuovo rango',
        'rank_name_placeholder'         => 'Nome rango',
        'no_ranks'                      => 'Nessun rango trovato',

        // Gestione – permessi
        'perm_see_applications'         => 'Visualizza candidature',
        'perm_edit_applications'        => 'Gestisci candidature',
        'perm_see_members'              => 'Visualizza lista membri',
        'perm_kick_user'                => 'Espelli utente',
        'perm_see_online'               => 'Vedi stato online',
        'perm_send_circular'            => 'Invia messaggio circolare',
        'perm_disband'                  => 'Sciogli alleanza',
        'perm_manage'                   => 'Gestisci alleanza',
        'perm_right_hand'               => 'Braccio destro',
        'perm_right_hand_long'          => '`Braccio Destro` (necessario per trasferire il titolo di fondatore)',
        'perm_manage_classes'           => 'Gestisci classe alleanza',

        // Gestione – sezione testi
        'manage_texts'                  => 'Gestisci testi',
        'internal_text'                 => 'Testo interno',
        'external_text'                 => 'Testo esterno',
        'application_text'              => 'Testo di candidatura',

        // Gestione – opzioni/impostazioni
        'options'                       => 'Opzioni',
        'alliance_logo_label'           => 'Logo alleanza',
        'applications_field'            => 'Candidature',
        'status_open'                   => 'Possibile (alleanza aperta)',
        'status_closed'                 => 'Impossibile (alleanza chiusa)',
        'rename_founder'                => 'Rinomina titolo fondatore in',
        'rename_newcomer'               => 'Rinomina rango Novizio',
        'no_settings_perm'              => 'Non hai il permesso di gestire le impostazioni dell\'alleanza.',

        // Gestione – cambio tag/nome
        'change_tag_name'               => 'Cambia tag/nome alleanza',
        'change_tag'                    => 'Cambia tag alleanza',
        'change_name'                   => 'Cambia nome alleanza',
        'former_tag'                    => 'Tag alleanza precedente:',
        'new_tag'                       => 'Nuovo tag alleanza:',
        'former_name'                   => 'Nome alleanza precedente:',
        'new_name'                      => 'Nuovo nome alleanza:',
        'former_tag_short'              => 'Tag alleanza precedente',
        'new_tag_short'                 => 'Nuovo tag alleanza',
        'former_name_short'             => 'Nome alleanza precedente',
        'new_name_short'                => 'Nuovo nome alleanza',
        'no_tagname_perm'               => 'Non hai il permesso di modificare il tag/nome dell\'alleanza.',

        // Gestione – scioglimento / passaggio
        'delete_pass_on'                => 'Sciogli alleanza / Passa alleanza',
        'delete_btn'                    => 'Sciogli questa alleanza',
        'no_delete_perm'                => 'Non hai il permesso di sciogliere l\'alleanza.',
        'handover'                      => 'Passa alleanza',
        'takeover_btn'                  => 'Prendi il controllo dell\'alleanza',
        'loca_continue'                 => 'Continua',
        'loca_change_founder'           => 'Trasferisci il titolo di fondatore a:',
        'loca_no_transfer_error'        => 'Nessun membro ha il diritto `braccio destro`. Non puoi passare l\'alleanza.',
        'loca_founder_inactive_error'   => 'Il fondatore non è inattivo da abbastanza tempo per prendere il controllo dell\'alleanza.',

        // Gestione – sezione abbandono (non fondatori)
        'leave_section_title'           => 'Lascia alleanza',
        'leave_consequences'            => 'Se lasci l\'alleanza, perderai tutti i tuoi permessi di rango e i benefici dell\'alleanza.',

        // Tab candidature
        'no_applications'               => 'Nessuna candidatura trovata',
        'accept_btn'                    => 'accetta',
        'deny_btn'                      => 'Rifiuta candidato',
        'report_btn'                    => 'Segnala candidatura',
        'app_date'                      => 'Data candidatura',
        'action_col'                    => 'Azione',
        'answer_btn'                    => 'rispondi',
        'reason_label'                  => 'Motivo',

        // Pagina di candidatura
        'apply_title'                   => 'Candidati all\'Alleanza',
        'apply_heading'                 => 'Candidatura a',
        'send_application_btn'          => 'Invia candidatura',
        'chars_remaining'               => 'Caratteri rimanenti',
        'msg_too_long'                  => 'Il messaggio è troppo lungo (max 2000 caratteri)',

        // Comunicazione broadcast
        'addressee'                     => 'A',
        'all_players'                   => 'tutti i giocatori',
        'only_rank'                     => 'solo rango:',
        'send_btn'                      => 'Invia',

        // Popup info
        'info_title'                    => 'Informazioni Alleanza',
        'apply_confirm'                 => 'Vuoi candidarti a questa alleanza?',
        'redirect_confirm'              => 'Seguendo questo link, lascerai OGame. Desideri continuare?',

        // Tab classi
        'class_selection_header'        => 'Selezione Classe',
        'select_class_title'            => 'Seleziona classe alleanza',
        'select_class_note'             => 'Seleziona una classe alleanza per ricevere bonus speciali. Puoi cambiare la classe alleanza nel menu dell\'alleanza, a condizione di avere i permessi necessari.',
        'class_warriors'                => 'Guerrieri (Alleanza)',
        'class_traders'                 => 'Commercianti (Alleanza)',
        'class_researchers'             => 'Ricercatori (Alleanza)',
        'class_label'                   => 'Classe Alleanza',
        'buy_for'                       => 'Acquista per',
        'no_dark_matter'                => 'Non c\'è abbastanza materia oscura disponibile',
        'loca_deactivate'               => 'Disattiva',
        'loca_activate_dm'              => 'Vuoi attivare la classe alleanza #allianceClassName# per #darkmatter# Materia Oscura? Così facendo, perderai la tua classe alleanza attuale.',
        'loca_activate_item'            => 'Vuoi attivare la classe alleanza #allianceClassName#? Così facendo, perderai la tua classe alleanza attuale.',
        'loca_deactivate_note'          => 'Vuoi davvero disattivare la classe alleanza #allianceClassName#? La riattivazione richiede un oggetto cambio classe alleanza per 500.000 Materia Oscura.',
        'loca_class_change_append'      => '<br><br>Classe alleanza attuale: #currentAllianceClassName#<br><br>Ultima modifica: #lastAllianceClassChange#',
        'loca_no_dm'                    => 'Materia Oscura insufficiente! Vuoi acquistarne altra adesso?',
        'loca_reference'                => 'Riferimento',
        'loca_language'                 => 'Lingua:',
        'loca_loading'                  => 'caricamento...',
        'warrior_bonus_1'               => '+10% velocità per le navi che volano tra i membri dell\'alleanza',
        'warrior_bonus_2'               => '+1 livello ricerca di combattimento',
        'warrior_bonus_3'               => '+1 livello ricerca spionaggio',
        'warrior_bonus_4'               => 'Il sistema di spionaggio può essere usato per scansionare interi sistemi.',
        'trader_bonus_1'                => '+10% velocità per i trasportatori',
        'trader_bonus_2'                => '+5% produzione miniere',
        'trader_bonus_3'                => '+5% produzione energetica',
        'trader_bonus_4'                => '+10% capacità di deposito planetario',
        'trader_bonus_5'                => '+10% capacità di deposito lunare',
        'researcher_bonus_1'            => '+5% pianeti più grandi alla colonizzazione',
        'researcher_bonus_2'            => '+10% velocità verso destinazione spedizione',
        'researcher_bonus_3'            => 'La falange di sistema può essere usata per scansionare i movimenti di flotta in interi sistemi.',
        'class_not_implemented'         => 'Sistema classi alleanza non ancora implementato',

        // Form crea alleanza
        'create_tag_label'              => 'Tag Alleanza (3-8 caratteri)',
        'create_name_label'             => 'Nome alleanza (3-30 caratteri)',
        'create_btn'                    => 'Crea alleanza',
        'loca_ally_tag_chars'           => 'Tag Alleanza (3-30 caratteri)',
        'loca_ally_name_chars'          => 'Nome Alleanza (3-8 caratteri)',
        'loca_ally_name_label'          => 'Nome alleanza (3-30 caratteri)',
        'loca_ally_tag_label'           => 'Tag Alleanza (3-8 caratteri)',
        'validation_min_chars'          => 'Caratteri insufficienti',
        'validation_special'            => 'Contiene caratteri non validi.',
        'validation_underscore'         => 'Il nome non può iniziare o finire con un trattino basso.',
        'validation_hyphen'             => 'Il nome non può iniziare o finire con un trattino.',
        'validation_space'              => 'Il nome non può iniziare o finire con uno spazio.',
        'validation_max_underscores'    => 'Il nome non può contenere più di 3 trattini bassi in totale.',
        'validation_max_hyphens'        => 'Il nome non può contenere più di 3 trattini.',
        'validation_max_spaces'         => 'Il nome non può contenere più di 3 spazi in totale.',
        'validation_consec_underscores' => 'Non puoi usare due o più trattini bassi consecutivi.',
        'validation_consec_hyphens'     => 'Non puoi usare due o più trattini consecutivi.',
        'validation_consec_spaces'      => 'Non puoi usare due o più spazi consecutivi.',

        // Dialoghi di conferma JS
        'confirm_leave'                 => 'Sei sicuro di voler lasciare l\'alleanza?',
        'confirm_kick'                  => 'Sei sicuro di voler espellere :username dall\'alleanza?',
        'confirm_deny'                  => 'Sei sicuro di voler rifiutare questa candidatura?',
        'confirm_deny_title'            => 'Rifiuta candidatura',
        'confirm_disband'               => 'Vuoi davvero sciogliere l\'alleanza?',
        'confirm_pass_on'               => 'Sei sicuro di voler cedere la tua alleanza?',
        'confirm_takeover'              => 'Sei sicuro di voler prendere il controllo di questa alleanza?',
        'confirm_abandon'               => 'Abbandonare questa alleanza?',
        'confirm_takeover_long'         => 'Prendere il controllo di questa alleanza?',

        // Messaggi di successo/errore del controller / AJAX
        'msg_already_in'                => 'Sei già in un\'alleanza',
        'msg_not_in_alliance'           => 'Non fai parte di nessuna alleanza',
        'msg_not_found'                 => 'Alleanza non trovata',
        'msg_id_required'               => 'ID alleanza richiesto',
        'msg_closed'                    => 'Questa alleanza è chiusa alle candidature',
        'msg_created'                   => 'Alleanza creata con successo',
        'msg_applied'                   => 'Candidatura inviata con successo',
        'msg_accepted'                  => 'Candidatura accettata',
        'msg_rejected'                  => 'Candidatura rifiutata',
        'msg_kicked'                    => 'Membro espulso dall\'alleanza',
        'msg_kicked_success'            => 'Membro espulso con successo',
        'msg_left'                      => 'Hai lasciato l\'alleanza',
        'msg_rank_assigned'             => 'Rango assegnato',
        'msg_rank_assigned_to'          => 'Rango assegnato con successo a :name',
        'msg_ranks_assigned'            => 'Ranghi assegnati con successo',
        'msg_rank_perms_updated'        => 'Permessi del rango aggiornati',
        'msg_texts_updated'             => 'Testi dell\'alleanza aggiornati',
        'msg_text_updated'              => 'Testo alleanza aggiornato',
        'msg_settings_updated'          => 'Impostazioni alleanza aggiornate',
        'msg_tag_updated'               => 'Tag alleanza aggiornato',
        'msg_name_updated'              => 'Nome alleanza aggiornato',
        'msg_tag_name_updated'          => 'Tag e nome alleanza aggiornati',
        'msg_disbanded'                 => 'Alleanza sciolta',
        'msg_broadcast_sent'            => 'Messaggio circolare inviato con successo',
        'msg_rank_created'              => 'Rango creato con successo',
        'msg_apply_success'             => 'Candidatura inviata con successo',
        'msg_apply_error'               => 'Impossibile inviare la candidatura',
        'msg_leave_error'               => 'Impossibile lasciare l\'alleanza',
        'msg_assign_error'              => 'Impossibile assegnare i ranghi',
        'msg_kick_error'                => 'Impossibile espellere il membro',
        'msg_invalid_action'            => 'Azione non valida',
        'msg_error'                     => 'Si è verificato un errore',
    ],

    // -------------------------------------------------------------------
    // Modulo Techtree
    // -------------------------------------------------------------------
    'techtree' => [
        // Tab di navigazione
        'tab_techtree'                          => 'Albero tecnologico',
        'tab_applications'                      => 'Applicazioni',
        'tab_techinfo'                          => 'Informazioni tecniche',
        'tab_technology'                        => 'Tecnologia',

        // Comuni
        'page_title'                            => 'Tecnologia',
        'no_requirements'                       => 'Nessun requisito disponibile',
        'is_requirement_for'                    => 'è un requisito per',
        'level'                                 => 'Livello',

        // Colonne tabella condivise
        'col_level'                             => 'Livello',
        'col_difference'                        => 'Differenza',
        'col_diff_per_level'                    => 'Differenza/Livello',
        'col_protected'                         => 'Protetto',
        'col_protected_percent'                 => 'Protetto (%)',

        // Tabella produzione
        'production_energy_balance'             => 'Bilancio energetico',
        'production_per_hour'                   => 'Produzione/h',
        'production_deuterium_consumption'      => 'Consumo deuterio',

        // Tabella proprietà (navi/difese)
        'properties_technical_data'             => 'Dati tecnici',
        'properties_structural_integrity'       => 'Integrità strutturale',
        'properties_shield_strength'            => 'Forza scudo',
        'properties_attack_strength'            => 'Forza attacco',
        'properties_speed'                      => 'Velocità',
        'properties_cargo_capacity'             => 'Capienza cargo',
        'properties_fuel_usage'                 => 'Consumo carburante (Deuterio)',

        // Tooltip proprietà
        'tooltip_basic_value'                   => 'Valore base',

        // Fuoco rapido
        'rapidfire_from'                        => 'Fuoco rapido da',
        'rapidfire_against'                     => 'Fuoco rapido contro',

        // Tabella magazzino
        'storage_capacity'                      => 'Cap. magazzino',

        // Tabella plasma
        'plasma_metal_bonus'                    => 'Bonus metallo %',
        'plasma_crystal_bonus'                  => 'Bonus cristallo %',
        'plasma_deuterium_bonus'                => 'Bonus deuterio %',

        // Tabella astrofisica
        'astrophysics_max_colonies'             => 'Colonie massime',
        'astrophysics_max_expeditions'          => 'Spedizioni massime',
        'astrophysics_note_1'                   => 'Le posizioni 3 e 13 possono essere popolate dal livello 4 in poi.',
        'astrophysics_note_2'                   => 'Le posizioni 2 e 14 possono essere popolate dal livello 6 in poi.',
        'astrophysics_note_3'                   => 'Le posizioni 1 e 15 possono essere popolate dal livello 8 in poi.',
    ],

    // -------------------------------------------------------------------------
    // Modulo Opzioni (impostazioni account)
    // -------------------------------------------------------------------------
    'options' => [
        'page_title'                            => 'Opzioni',
        'tab_userdata'                          => 'Dati utente',
        'tab_general'                           => 'Generale',
        'tab_display'                           => 'Visualizzazione',
        'tab_extended'                          => 'Esteso',

        // Tab 1 — Dati utente
        'section_playername'                    => 'Nome giocatore',
        'your_player_name'                      => 'Il tuo nome giocatore:',
        'new_player_name'                       => 'Nuovo nome giocatore:',
        'username_change_once_week'             => 'Puoi cambiare il nome utente una volta a settimana.',
        'username_change_hint'                  => 'Per farlo, clicca sul tuo nome o sulle impostazioni in cima allo schermo.',

        'section_password'                      => 'Cambia password',
        'old_password'                          => 'Inserisci la vecchia password:',
        'new_password'                          => 'Nuova password (almeno 4 caratteri):',
        'repeat_password'                       => 'Ripeti la nuova password:',
        'password_check'                        => 'Controllo password:',
        'password_strength_low'                 => 'Bassa',
        'password_strength_medium'              => 'Media',
        'password_strength_high'                => 'Alta',
        'password_properties_title'             => 'La password dovrebbe contenere le seguenti proprietà',
        'password_min_max'                      => 'min. 4 caratteri, max. 20 caratteri',
        'password_mixed_case'                   => 'Lettere maiuscole e minuscole',
        'password_special_chars'                => 'Caratteri speciali (es. !?:_., )',
        'password_numbers'                      => 'Numeri',
        'password_length_hint'                  => 'La tua password deve avere almeno <strong>4 caratteri</strong> e non può essere più lunga di <strong>20 caratteri</strong>.',

        'section_email'                         => 'Indirizzo email',
        'current_email'                         => 'Indirizzo email attuale:',
        'send_validation_link'                  => 'Invia link di validazione',
        'email_sent_success'                    => 'Email inviata con successo!',
        'email_sent_error'                      => 'Errore! L\'account è già validato o l\'email non può essere inviata!',
        'email_too_many_requests'               => 'Hai già richiesto troppe email!',
        'new_email'                             => 'Nuovo indirizzo email:',
        'new_email_confirm'                     => 'Nuovo indirizzo email (di conferma):',
        'enter_password_confirm'                => 'Inserisci la password (come conferma):',
        'email_warning'                         => 'Attenzione! Dopo una validazione dell\'account riuscita, un nuovo cambio di indirizzo email è possibile solo dopo un periodo di <b>7 giorni</b>.',

        // Tab 2 — Generale
        'section_spy_probes'                    => 'Sonde spia',
        'spy_probes_amount'                     => 'Numero di sonde di spionaggio:',
        'section_chat'                          => 'Chat',
        'disable_chat_bar'                      => 'Disattiva barra chat:',
        'section_warnings'                      => 'Avvisi',
        'disable_outlaw_warning'                => 'Disattiva avviso fuorilegge per attacchi a avversari 5 volte più forti:',

        // Tab 3 — Visualizzazione
        'section_general_display'               => 'Generale',
        'show_mobile_version'                   => 'Mostra versione mobile:',
        'show_alt_dropdowns'                    => 'Mostra menu a tendina alternativi:',
        'activate_autofocus'                    => 'Attiva autofocus nella classifica:',
        'always_show_events'                    => 'Mostra sempre gli eventi:',
        'events_hide'                           => 'Nascondi',
        'events_above'                          => 'Sopra il contenuto',
        'events_below'                          => 'Sotto il contenuto',
        'section_planets'                       => 'I tuoi pianeti',
        'sort_planets_by'                       => 'Ordina pianeti per:',
        'sort_emergence'                        => 'Ordine di comparsa',
        'sort_coordinates'                      => 'Coordinate',
        'sort_alphabet'                         => 'Alfabeto',
        'sort_size'                             => 'Dimensione',
        'sort_used_fields'                      => 'Campi utilizzati',
        'sort_sequence'                         => 'Sequenza di ordinamento:',
        'sort_order_up'                         => 'su',
        'sort_order_down'                       => 'giù',
        'section_overview_display'              => 'Panoramica',
        'highlight_planet_info'                 => 'Evidenzia informazioni pianeta:',
        'animated_detail_display'               => 'Visualizzazione dettagli animata:',
        'animated_overview'                     => 'Panoramica animata:',
        'section_overlays'                      => 'Overlay',
        'overlays_hint'                         => 'Le seguenti impostazioni consentono di aprire gli overlay corrispondenti come finestra del browser separata anziché all\'interno del gioco.',
        'popup_notes'                           => 'Note in finestra extra:',
        'popup_combat_reports'                  => 'Rapporti di combattimento in finestra extra:',
        'section_messages_display'              => 'Messaggi',
        'hide_report_pictures'                  => 'Nascondi immagini nei rapporti:',
        'msgs_per_page'                         => 'Numero di messaggi per pagina:',
        'auctioneer_notifications'              => 'Notifica banditore:',
        'economy_notifications'                 => 'Crea messaggi economia:',
        'section_galaxy_display'                => 'Galassia',
        'detailed_activity'                     => 'Visualizzazione attività dettagliata:',
        'preserve_galaxy_system'                => 'Mantieni galassia/sistema con cambio pianeta:',

        // Tab 4 — Esteso
        'section_vacation'                      => 'Modalità Vacanza',
        'vacation_active'                       => 'Sei attualmente in modalità vacanza.',
        'vacation_can_deactivate_after'         => 'Puoi disattivarla dopo:',
        'vacation_cannot_activate'              => 'La modalità vacanza non può essere attivata (Flotte attive)',
        'vacation_description_1'                => 'La modalità vacanza è progettata per proteggerti durante le lunghe assenze dal gioco. Puoi attivarla solo quando nessuna delle tue flotte è in transito. Gli ordini di costruzione e ricerca verranno messi in pausa.',
        'vacation_description_2'                => 'Una volta attivata, la modalità vacanza ti proteggerà dai nuovi attacchi. Gli attacchi già avviati continueranno e la tua produzione sarà azzerata. La modalità vacanza non impedisce la cancellazione del tuo account se è rimasto inattivo per 35+ giorni e non ha DM acquistato.',
        'vacation_description_3'                => 'La modalità vacanza dura un minimo di 48 ore. Solo dopo la scadenza di questo tempo potrai disattivarla.',
        'vacation_tooltip_min_days'             => 'La vacanza dura un minimo di 2 giorni.',
        'vacation_deactivate_btn'               => 'Disattiva',
        'vacation_activate_btn'                 => 'Attiva',
        'section_account'                       => 'Il tuo account',
        'delete_account'                        => 'Elimina account',
        'delete_account_hint'                   => 'Seleziona qui per contrassegnare il tuo account per l\'eliminazione automatica dopo 7 giorni.',

        // Pulsante submit
        'use_settings'                          => 'Usa impostazioni',

        // Regole di validazione JS
        'validation_not_enough_chars'           => 'Caratteri insufficienti',
        'validation_pw_too_short'               => 'La password inserita è troppo corta (min. 4 caratteri)',
        'validation_pw_too_long'                => 'La password inserita è troppo lunga (max. 20 caratteri)',
        'validation_invalid_email'              => 'Devi inserire un indirizzo email valido!',
        'validation_special_chars'              => 'Contiene caratteri non validi.',
        'validation_no_begin_end_underscore'    => 'Il nome non può iniziare o finire con un trattino basso.',
        'validation_no_begin_end_hyphen'        => 'Il nome non può iniziare o finire con un trattino.',
        'validation_no_begin_end_whitespace'    => 'Il nome non può iniziare o finire con uno spazio.',
        'validation_max_three_underscores'      => 'Il nome non può contenere più di 3 trattini bassi in totale.',
        'validation_max_three_hyphens'          => 'Il nome non può contenere più di 3 trattini.',
        'validation_max_three_spaces'           => 'Il nome non può contenere più di 3 spazi in totale.',
        'validation_no_consecutive_underscores' => 'Non puoi usare due o più trattini bassi consecutivi.',
        'validation_no_consecutive_hyphens'     => 'Non puoi usare due o più trattini consecutivi.',
        'validation_no_consecutive_spaces'      => 'Non puoi usare due o più spazi consecutivi.',

        // Stringhe JS
        'js_change_name_title'                  => 'Nuovo nome giocatore',
        'js_change_name_question'               => 'Sei sicuro di voler cambiare il tuo nome giocatore in %newName%?',
        'js_planet_move_question'               => 'Attenzione! Questa missione potrebbe essere ancora in corso quando inizia il periodo di ricollocazione e, in tal caso, il processo verrà annullato. Vuoi davvero continuare con questo lavoro?',
        'js_tab_disabled'                       => 'Per usare questa opzione devi essere validato e non essere in modalità vacanza!',
        'js_vacation_question'                  => 'Vuoi attivare la modalità vacanza? Puoi terminare la vacanza solo dopo 2 giorni.',

        // Messaggi controller
        'msg_settings_saved'                    => 'Impostazioni salvate',
        'msg_vacation_activated'                => 'Modalità vacanza attivata. Ti proteggerà dai nuovi attacchi per un minimo di 48 ore.',
        'msg_vacation_deactivated'              => 'Modalità vacanza disattivata.',
        'msg_vacation_min_duration'             => 'Puoi disattivare la modalità vacanza solo dopo che è trascorsa la durata minima di 48 ore.',
        'msg_vacation_fleets_in_transit'        => 'Non puoi attivare la modalità vacanza mentre hai flotte in transito.',
        'msg_probes_min_one'                    => 'Il numero di sonde di spionaggio deve essere almeno 1',
    ],

    // -------------------------------------------------------------------------
    // Layout (main.blade.php) — intestazione, menu, barra risorse, footer, JS loca
    // -------------------------------------------------------------------------
    'layout' => [
        // Barra intestazione
        'player'                    => 'Giocatore',
        'change_player_name'        => 'Cambia nome giocatore',
        'highscore'                 => 'Classifica',
        'notes'                     => 'Note',
        'notes_overlay_title'       => 'Le mie note',
        'buddies'                   => 'Amici',
        'search'                    => 'Cerca',
        'search_overlay_title'      => 'Cerca nell\'universo',
        'options'                   => 'Opzioni',
        'support'                   => 'Supporto',
        'log_out'                   => 'Esci',
        'unread_messages'           => 'messaggi non letti',
        'loading'                   => 'caricamento...',
        'no_fleet_movement'         => 'Nessun movimento di flotta',
        'under_attack'              => 'Sei sotto attacco!',

        // Classe personaggio
        'class_none'                => 'Nessuna classe selezionata',
        'class_selected'            => 'La tua classe: :name',
        'class_click_select'        => 'Clicca per selezionare una classe personaggio',

        // Barra risorse
        'res_available'             => 'Disponibile',
        'res_storage_capacity'      => 'Capacità del deposito',
        'res_current_production'    => 'Produzione attuale',
        'res_den_capacity'          => 'Capacità del nascondiglio',
        'res_consumption'           => 'Consumo',
        'res_purchase_dm'           => 'Acquista Materia Oscura',
        'res_metal'                 => 'Metallo',
        'res_crystal'               => 'Cristallo',
        'res_deuterium'             => 'Deuterio',
        'res_energy'                => 'Energia',
        'res_dark_matter'           => 'Materia Oscura',

        // Menu laterale — etichette voci
        'menu_overview'             => 'Riepilogo',
        'menu_resources'            => 'Risorse',
        'menu_facilities'           => 'Strutture',
        'menu_merchant'             => 'Mercante',
        'menu_research'             => 'Ricerca',
        'menu_shipyard'             => 'Cantiere Spaziale',
        'menu_defense'              => 'Difesa',
        'menu_fleet'                => 'Flotta',
        'menu_galaxy'               => 'Galassia',
        'menu_alliance'             => 'Alleanza',
        'menu_officers'             => 'Sala ufficiali',
        'menu_shop'                 => 'Negozio',
        'menu_directives'           => 'Direttive',

        // Menu laterale — tooltip icone
        'menu_rewards_title'        => 'Ricompense',
        'menu_resource_settings_title' => 'Impostazioni risorse',
        'menu_jump_gate'            => 'Portale di salto',
        'menu_resource_market_title' => 'Mercato risorse',
        'menu_technology_title'     => 'Tecnologia',
        'menu_fleet_movement_title' => 'Movimento flotta',
        'menu_inventory_title'      => 'Inventario',

        // Barra pianeti
        'planets'                   => 'Pianeti',

        // Barra chat
        'contacts_online'           => ':count Contatto/i online',

        // Pulsante torna su
        'back_to_top'               => 'Torna su',

        // Footer
        'all_rights_reserved'       => 'Tutti i diritti riservati.',
        'patch_notes'               => 'Note sulla patch',
        'server_settings'           => 'Impostazioni server',
        'help'                      => 'Aiuto',
        'rules'                     => 'Regole',
        'legal'                     => 'Note legali',
        'board'                     => 'Forum',

        // JS — jsloca
        'js_internal_error'         => "Si è verificato un errore sconosciuto. Purtroppo l'ultima azione non è stata eseguita!",
        'js_notify_info'            => 'Info',
        'js_notify_success'         => 'Successo',
        'js_notify_warning'         => 'Avviso',
        'js_combatsim_planning'     => 'Pianificazione',
        'js_combatsim_pending'      => 'Simulazione in corso...',
        'js_combatsim_done'         => 'Completata',
        'js_msg_restore'            => 'ripristina',
        'js_msg_delete'             => 'elimina',
        'js_copied'                 => 'Copiato negli appunti',
        'js_report_operator'        => 'Segnalare questo messaggio a un operatore di gioco?',

        // JS — LocalizationStrings
        'js_time_done'              => 'fatto',
        'js_question'               => 'Domanda',
        'js_ok'                     => 'Ok',
        'js_outlaw_warning'         => 'Stai per attaccare un giocatore più forte. In questo caso, le tue difese antiattacco verranno disattivate per 7 giorni e tutti i giocatori potranno attaccarti senza sanzioni. Sei sicuro di voler continuare?',
        'js_last_slot_moon'         => 'Questo edificio utilizzerà l\'ultimo slot di costruzione disponibile. Espandi la tua Base Lunare per ottenere più spazio. Sei sicuro di voler costruire questo edificio?',
        'js_last_slot_planet'       => 'Questo edificio utilizzerà l\'ultimo slot di costruzione disponibile. Espandi il tuo Terraformer o acquista un oggetto Campo Pianeta per ottenere più slot. Sei sicuro di voler costruire questo edificio?',
        'js_forced_vacation'        => 'Alcune funzionalità del gioco non sono disponibili fino alla validazione del tuo account.',
        'js_more_details'           => 'Più dettagli',
        'js_less_details'           => 'Meno dettagli',
        'js_planet_lock'            => 'Blocca disposizione',
        'js_planet_unlock'          => 'Sblocca disposizione',
        'js_activate_item_question' => 'Vuoi sostituire l\'oggetto esistente? Il vecchio bonus andrà perso.',
        'js_activate_item_header'   => 'Sostituisci oggetto?',

        // JS — chatLoca
        'chat_text_empty'           => 'Dove è il messaggio?',
        'chat_text_too_long'        => 'Il messaggio è troppo lungo.',
        'chat_same_user'            => 'Non puoi scrivere a te stesso.',
        'chat_ignored_user'         => 'Hai ignorato questo giocatore.',
        'chat_not_activated'        => 'Questa funzione è disponibile solo dopo l\'attivazione del tuo account.',
        'chat_new_chats'            => '#+# messaggi non letti',
        'chat_more_users'           => 'mostra altri',

        // JS — eventboxLoca
        'eventbox_mission'          => 'Missione',
        'eventbox_missions'         => 'Missioni',
        'eventbox_next'             => 'Successivo',
        'eventbox_type'             => 'Tipo',
        'eventbox_own'              => 'propria',
        'eventbox_friendly'         => 'amichevole',
        'eventbox_hostile'          => 'ostile',

        // JS — planetMoveLoca
        'planet_move_ask_title'     => 'Ricollocazione pianeta',
        'planet_move_ask_cancel'    => 'Sei sicuro di voler annullare questa ricollocazione? Il normale tempo di attesa sarà mantenuto.',
        'planet_move_success'       => 'La ricollocazione del pianeta è stata annullata con successo.',

        // JS — locaPremium
        'premium_building_half'     => 'Vuoi ridurre il tempo di costruzione del 50% del tempo totale () per <b>750 Materia Oscura<\/b>?',
        'premium_building_full'     => 'Vuoi completare immediatamente l\'ordine di costruzione per <b>750 Materia Oscura<\/b>?',
        'premium_ships_half'        => 'Vuoi ridurre il tempo di costruzione del 50% del tempo totale () per <b>750 Materia Oscura<\/b>?',
        'premium_ships_full'        => 'Vuoi completare immediatamente l\'ordine di costruzione per <b>750 Materia Oscura<\/b>?',
        'premium_research_half'     => 'Vuoi ridurre il tempo di ricerca del 50% del tempo totale () per <b>750 Materia Oscura<\/b>?',
        'premium_research_full'     => 'Vuoi completare immediatamente l\'ordine di ricerca per <b>750 Materia Oscura<\/b>?',

        // JS — loca object
        'loca_error_not_enough_dm'  => 'Materia Oscura insufficiente! Vuoi acquistarne altra ora?',
        'loca_notice'               => 'Riferimento',
        'loca_planet_giveup'        => 'Sei sicuro di voler abbandonare il pianeta %planetName% %planetCoordinates%?',
        'loca_moon_giveup'          => 'Sei sicuro di voler abbandonare la luna %planetName% %planetCoordinates%?',
    ],

    // ── Highscore ───────────────────────────────────────────────────────────
    'highscore' => [
        'player_highscore'      => 'Classifica giocatori',
        'alliance_highscore'    => 'Classifica alleanze',
        'own_position'          => 'Posizione propria',
        'own_position_hidden'   => 'Posizione propria (-)',
        'points'                => 'Punti',
        'economy'               => 'Economia',
        'research'              => 'Ricerca',
        'military'              => 'Militare',
        'military_built'        => 'Punti militari costruiti',
        'military_destroyed'    => 'Punti militari distrutti',
        'military_lost'         => 'Punti militari persi',
        'honour_points'         => 'Punti onore',
        'position'              => 'Posizione',
        'player_name_honour'    => 'Nome giocatore (Punti onore)',
        'action'                => 'Azione',
        'alliance'              => 'Alleanza',
        'member'                => 'Membro',
        'average_points'        => 'Punti medi',
        'no_alliances_found'    => 'Nessuna alleanza trovata',
        'write_message'         => 'Scrivi messaggio',
        'buddy_request'         => 'Richiesta amicizia',
        'buddy_request_to'      => 'Richiesta amicizia a',
        'total_ships'           => 'Navi totali',
        'buddy_request_sent'    => 'Richiesta amicizia inviata con successo!',
        'buddy_request_failed'  => 'Impossibile inviare la richiesta di amicizia.',
        'are_you_sure_ignore'   => 'Sei sicuro di voler ignorare',
        'player_ignored'        => 'Giocatore ignorato con successo!',
        'player_ignored_failed' => 'Impossibile ignorare il giocatore.',
    ],

    // ── Premium / Ufficiali ─────────────────────────────────────────────────
    'premium' => [
        'recruit_officers'           => 'Sala ufficiali',
        'your_officers'              => 'I tuoi ufficiali',
        'intro_text'                 => 'Con i tuoi ufficiali puoi guidare il tuo impero a dimensioni oltre i tuoi sogni più sfrenati - tutto ciò di cui hai bisogno è un po\' di Materia Oscura e i tuoi lavoratori e consiglieri lavoreranno ancora più duramente!',
        'info_dark_matter'           => 'Maggiori informazioni su: Materia Oscura',
        'info_commander'             => 'Maggiori informazioni su: Comandante',
        'info_admiral'               => 'Maggiori informazioni su: Ammiraglio',
        'info_engineer'              => 'Maggiori informazioni su: Ingegnere',
        'info_geologist'             => 'Maggiori informazioni su: Geologo',
        'info_technocrat'            => 'Maggiori informazioni su: Tecnocrate',
        'info_commanding_staff'      => 'Maggiori informazioni su: Stato Maggiore',
        'hire_commander_tooltip'     => 'Assumi comandante|+40 preferiti, coda costruzione, scorciatoie, scanner trasporto, senza pubblicità* <span style=\'font-size: 10px; line-height: 10px\'>(*esclusi: riferimenti relativi al gioco)</span>',
        'hire_admiral_tooltip'       => "Assumi ammiraglio|Slot flotta max +2,\nSpedizioni max +1,\nTasso fuga flotta migliorato,\nSlot salvataggio simulazione combattimento +20",
        'hire_engineer_tooltip'      => 'Assumi ingegnere|Dimezza le perdite nelle difese, +10% produzione energia',
        'hire_geologist_tooltip'     => 'Assumi geologo|+10% produzione miniere',
        'hire_technocrat_tooltip'    => 'Assumi tecnocrate|+2 livelli spionaggio, 25% meno tempo di ricerca',
        'remaining_officers'         => ':current di :max',
        'benefit_fleet_slots_title'  => 'Puoi inviare più flotte contemporaneamente.',
        'benefit_fleet_slots'        => 'Slot flotta max +1',
        'benefit_energy_title'       => 'Le tue centrali elettriche e i satelliti solari producono il 2% di energia in più.',
        'benefit_energy'             => '+2% produzione energia',
        'benefit_mines_title'        => 'Le tue miniere producono il 2% in più.',
        'benefit_mines'              => '+2% produzione miniere',
        'benefit_espionage_title'    => '1 livello verrà aggiunto alla tua ricerca di spionaggio.',
        'benefit_espionage'          => '+1 livelli spionaggio',
    ],

    // ── Shop ────────────────────────────────────────────────────────────────
    'shop' => [
        'page_title'               => 'Negozio',
        'tooltip_shop'             => 'Puoi acquistare oggetti qui.',
        'tooltip_inventory'        => 'Qui puoi visualizzare una panoramica degli oggetti acquistati.',
        'btn_shop'                 => 'Negozio',
        'btn_inventory'            => 'Inventario',
        'category_special_offers'  => 'Offerte speciali',
        'category_all'             => 'tutti',
        'category_resources'       => 'Risorse',
        'category_buddy_items'     => 'Oggetti amico',
        'category_construction'    => 'Costruzione',
        'btn_get_more_resources'   => 'Ottieni più risorse',
        'btn_purchase_dark_matter' => 'Acquista Materia Oscura',
        'feature_coming_soon'      => 'Funzionalità in arrivo.',
        // Livelli oggetto
        'tier_gold'                => 'Oro',
        'tier_silver'              => 'Argento',
        'tier_bronze'              => 'Bronzo',
        // Etichette tooltip schede oggetto
        'tooltip_duration'         => 'Durata',
        'duration_now'             => 'ora',
        'tooltip_price'            => 'Prezzo',
        'tooltip_in_inventory'     => 'In inventario',
        'dark_matter'              => 'Materia Oscura',
        'dm_abbreviation'          => 'MO',
        'item_duration'            => 'Durata',
        'now'                      => 'ora',
        'item_price'               => 'Prezzo',
        'item_in_inventory'        => 'In inventario',
        // Chiavi JS loca (usate da inventory.js)
        'loca_extend'              => 'Prolunga',
        'loca_activate'            => 'Attiva',
        'loca_buy_activate'        => 'Acquista e attiva',
        'loca_buy_extend'          => 'Acquista e prolunga',
        'loca_buy_dm'              => 'Non hai abbastanza Materia Oscura. Vuoi acquistarne altra adesso?',
    ],

    // -------------------------------------------------------------------------
    // Overlay di ricerca
    // -------------------------------------------------------------------------

    'search' => [
        'input_hint'              => 'Inserisci il nome del giocatore, dell\'alleanza o del pianeta',
        'search_btn'              => 'Cerca',
        'tab_players'             => 'Nomi dei giocatori',
        'tab_alliances'           => 'Alleanze/Tag',
        'tab_planets'             => 'Nomi dei pianeti',
        'no_search_term'          => 'Nessun termine di ricerca inserito',
        'searching'               => 'Ricerca in corso...',
        'search_failed'           => 'Ricerca fallita. Riprova.',
        'no_results'              => 'Nessun risultato trovato',
        'player_name'             => 'Nome giocatore',
        'planet_name'             => 'Nome pianeta',
        'coordinates'             => 'Coordinate',
        'tag'                     => 'Tag',
        'alliance_name'           => 'Nome alleanza',
        'member'                  => 'Membri',
        'points'                  => 'Punti',
        'action'                  => 'Azione',
        'apply_for_alliance'      => 'Candidati per questa alleanza',
    ],

    // -------------------------------------------------------------------------
    // Overlay delle note
    // -------------------------------------------------------------------------

    'notes' => [
        'no_notes_found'          => 'Nessuna nota trovata',
    ],

    // -------------------------------------------------------------------------
    // Overlay abbandono/rinomina pianeta
    // -------------------------------------------------------------------------

    'planet_abandon' => [
        // Descrizione pagina
        'description'                   => 'Tramite questo menu puoi modificare i nomi dei pianeti e delle lune o abbandonarli completamente.',

        // Sezione rinomina
        'rename_heading'                => 'Rinomina',
        'new_planet_name'               => 'Nuovo nome del pianeta',
        'new_moon_name'                 => 'Nuovo nome della luna',
        'rename_btn'                    => 'Rinomina',

        // Tooltip (contenuto HTML – {{ }} lo codifica automaticamente negli attributi title)
        'tooltip_rules_title'           => 'Regole',
        'tooltip_rename_planet'         => 'Qui puoi rinominare il tuo pianeta.<br /><br />Il nome del pianeta deve avere una lunghezza compresa tra <span style="font-weight: bold;">2 e 20 caratteri</span>.<br />I nomi dei pianeti possono contenere lettere maiuscole e minuscole e numeri.<br />Possono contenere trattini, trattini bassi e spazi, che però non possono essere posizionati nel modo seguente:<br />- all\'inizio o alla fine del nome<br />- direttamente uno dopo l\'altro<br />- più di tre volte nel nome',
        'tooltip_rename_moon'           => 'Qui puoi rinominare la tua luna.<br /><br />Il nome della luna deve avere una lunghezza compresa tra <span style="font-weight: bold;">2 e 20 caratteri</span>.<br />I nomi delle lune possono contenere lettere maiuscole e minuscole e numeri.<br />Possono contenere trattini, trattini bassi e spazi, che però non possono essere posizionati nel modo seguente:<br />- all\'inizio o alla fine del nome<br />- direttamente uno dopo l\'altro<br />- più di tre volte nel nome',

        // Intestazioni sezione abbandono
        'abandon_home_planet'           => 'Abbandona pianeta madre',
        'abandon_moon'                  => 'Abbandona luna',
        'abandon_colony'                => 'Abbandona colonia',
        'abandon_home_planet_btn'       => 'Abbandona pianeta madre',
        'abandon_moon_btn'              => 'Abbandona luna',
        'abandon_colony_btn'            => 'Abbandona colonia',

        // Avvisi abbandono
        'home_planet_warning'           => 'Se abbandoni il tuo pianeta madre, al prossimo accesso verrai reindirizzato al pianeta che hai colonizzato successivamente.',
        'items_lost_moon'               => 'Se hai attivato oggetti su una luna, questi verranno persi se abbandoni la luna.',
        'items_lost_planet'             => 'Se hai attivato oggetti su un pianeta, questi verranno persi se abbandoni il pianeta.',

        // Form conferma abbandono
        'confirm_password'              => 'Conferma la cancellazione di :type [:coordinates] inserendo la tua password',
        'confirm_btn'                   => 'Conferma',
        'type_moon'                     => 'luna',
        'type_planet'                   => 'pianeta',

        // Messaggi di validazione (JS)
        'validation_min_chars'          => 'Caratteri insufficienti',
        'validation_pw_min'             => 'La password inserita è troppo corta (min. 4 caratteri)',
        'validation_pw_max'             => 'La password inserita è troppo lunga (max. 20 caratteri)',
        'validation_email'              => 'Devi inserire un indirizzo e-mail valido!',
        'validation_special'            => 'Contiene caratteri non validi.',
        'validation_underscore'         => 'Il nome non può iniziare o terminare con un trattino basso.',
        'validation_hyphen'             => 'Il nome non può iniziare o terminare con un trattino.',
        'validation_space'              => 'Il nome non può iniziare o terminare con uno spazio.',
        'validation_max_underscores'    => 'Il nome non può contenere più di 3 trattini bassi in totale.',
        'validation_max_hyphens'        => 'Il nome non può contenere più di 3 trattini.',
        'validation_max_spaces'         => 'Il nome non può contenere più di 3 spazi in totale.',
        'validation_consec_underscores' => 'Non è possibile usare due o più trattini bassi consecutivamente.',
        'validation_consec_hyphens'     => 'Non è possibile usare due o più trattini consecutivamente.',
        'validation_consec_spaces'      => 'Non è possibile usare due o più spazi consecutivamente.',

        // Messaggi controller
        'msg_invalid_planet_name'       => 'Il nuovo nome del pianeta non è valido. Riprova.',
        'msg_invalid_moon_name'         => 'Il nuovo nome della luna non è valido. Riprova.',
        'msg_planet_renamed'            => 'Pianeta rinominato con successo.',
        'msg_moon_renamed'              => 'Luna rinominata con successo.',
        'msg_wrong_password'            => 'Password errata!',
        'msg_confirm_title'             => 'Conferma',
        'msg_confirm_deletion'          => 'Se confermi la cancellazione di :type [:coordinates] (:name), tutti gli edifici, le navi e i sistemi di difesa presenti su quel :type verranno rimossi dal tuo account. Se hai oggetti attivi sul tuo :type, anche questi verranno persi quando lo abbandoni. Questo processo non può essere annullato!',
        'msg_reference'                 => 'Avviso',
        'msg_abandoned'                 => ':type abbandonato/a con successo!',
        'msg_type_moon'                 => 'Luna',
        'msg_type_planet'               => 'Pianeta',
        'msg_yes'                       => 'Sì',
        'msg_no'                        => 'No',
        'msg_ok'                        => 'Ok',
    ],
];
