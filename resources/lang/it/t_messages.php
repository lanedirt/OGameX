<?php

return [
    // ------------------------
    'welcome_message' => [
        'from' => 'OGameX',
        'subject' => 'Benvenuto in OGameX!',
        'body' => 'Saluti Imperatore :player!

Congratulazioni per aver iniziato la tua illustre carriera. Sarò qui per guidarti nei tuoi primi passi.

A sinistra puoi vedere il menu che ti permette di supervisionare e governare il tuo impero galattico.

Hai già visto la Panoramica. Risorse e Strutture ti permettono di costruire edifici per aiutarti ad espandere il tuo impero. Inizia costruendo una Centrale Solare per raccogliere energia per le tue miniere.

Poi espandi la Miniera di Metallo e la Miniera di Cristallo per produrre risorse vitali. Altrimenti, dai semplicemente uno sguardo in giro da solo. Presto ti sentirai a casa, ne sono sicuro.

Puoi trovare ulteriori aiuti, consigli e tattiche qui:

Chat Discord: Discord Server
Forum: OGameX Forum
Supporto: Supporto Gioco

Troverai solo annunci attuali e modifiche al gioco nei forum.


Ora sei pronto per il futuro. Buona fortuna!

Questo messaggio verrà eliminato tra 7 giorni.',
    ],

    // ------------------------
    'return_of_fleet_with_resources' => [
        'from' => 'Comando Flotta',
        'subject' => 'Ritorno di una flotta',
        'body' => 'La tua flotta sta tornando da :from a :to e ha consegnato le sue merci:

Metallo: :metal
Cristallo: :crystal
Deuterio: :deuterium',
    ],

    // ------------------------
    'return_of_fleet' => [
        'from' => 'Comando Flotta',
        'subject' => 'Ritorno di una flotta',
        'body' => 'La tua flotta sta tornando da :from a :to.

La flotta non consegna merci.',
    ],

    // ------------------------
    'fleet_deployment_with_resources' => [
        'from' => 'Comando Flotta',
        'subject' => 'Ritorno di una flotta',
        'body' => 'Una delle tue flotte da :from ha raggiunto :to e ha consegnato le sue merci:

Metallo: :metal
Cristallo: :crystal
Deuterio: :deuterium',
    ],

    // ------------------------
    'fleet_deployment' => [
        'from' => 'Comando Flotta',
        'subject' => 'Ritorno di una flotta',
        'body' => 'Una delle tue flotte da :from ha raggiunto :to. La flotta non consegna merci.',
    ],

    // ------------------------
    'transport_arrived' => [
        'from' => 'Comando Flotta',
        'subject' => 'Raggiungimento di un pianeta',
        'body' => 'La tua flotta da :from raggiunge :to e consegna le sue merci:
Metallo: :metal Cristallo: :crystal Deuterio: :deuterium',
    ],

    // ------------------------
    'transport_received' => [
        'from' => 'Comando Flotta',
        'subject' => 'Flotta in arrivo',
        'body' => 'Una flotta in arrivo da :from ha raggiunto il tuo pianeta :to e ha consegnato le sue merci:
Metallo: :metal Cristallo: :crystal Deuterio: :deuterium',
    ],

    // ------------------------
    'acs_defend_arrival_host' => [
        'from' => 'Monitoraggio Spaziale',
        'subject' => 'La flotta si è fermata',
        'body' => 'Una flotta è arrivata a :to.',
    ],

    // ------------------------
    'acs_defend_arrival_sender' => [
        'from' => 'Comando Flotta',
        'subject' => 'La flotta si è fermata',
        'body' => 'Una flotta è arrivata a :to.',
    ],

    // ------------------------
    'colony_established' => [
        'from' => 'Comando Flotta',
        'subject' => 'Rapporto di Insediamento',
        'body' => 'La flotta è arrivata alle coordinate assegnate :coordinates, ha trovato un nuovo pianeta e sta iniziando a svilupparlo immediatamente.',
    ],

    // ------------------------
    'colony_establish_fail_astrophysics' => [
        'from' => 'Coloni',
        'subject' => 'Rapporto di Insediamento',
        'body' => 'La flotta è arrivata alle coordinate assegnate :coordinates e accerta che il pianeta è adatto alla colonizzazione. Poco dopo aver iniziato a sviluppare il pianeta, i coloni si rendono conto che le loro conoscenze di astrofisica non sono sufficienti per completare la colonizzazione di un nuovo pianeta.',
    ],

    // ------------------------
    'espionage_report' => [
        'from' => 'Comando Flotta',
        'subject' => 'Rapporto di spionaggio da :planet',
    ],

    // ------------------------
    'espionage_detected' => [
        'from' => 'Comando Flotta',
        'subject' => 'Rapporto di spionaggio dal Pianeta :planet',
        'body' => "Una flotta straniera dal pianeta :planet (:attacker_name) è stata avvistata vicino al tuo pianeta\n:defender\nProbabilità di controspionaggio: :chance%",
    ],

    // ------------------------
    'battle_report' => [
        'from' => 'Comando Flotta',
        'subject' => 'Rapporto di combattimento :planet',
    ],

    // ------------------------
    'fleet_lost_contact' => [
        'from' => 'Comando Flotta',
        'subject' => 'Il contatto con la flotta attaccante è stato perso. :coordinates',
        'body' => '(Ciò significa che è stata distrutta al primo round.)',
    ],

    // ------------------------
    'debris_field_harvest' => [
        'from' => 'Flotta',
        'subject' => 'Rapporto di raccolta dal campo di detriti a :coordinates',
        'body' => 'La tua :ship_name (:ship_amount navi) ha una capacità di stivaggio totale di :storage_capacity. Al bersaglio :to, :metal Metallo, :crystal Cristallo e :deuterium Deuterio flottano nello spazio. Hai raccolto :harvested_metal Metallo, :harvested_crystal Cristallo e :harvested_deuterium Deuterio.',
    ],

    // ------------------------
    // Buddy Request Received
    'buddy_request_received' => [
        'from' => 'Amici',
        'subject' => 'Richiesta di amicizia',
        'body' => 'Hai ricevuto una nuova richiesta di amicizia da :sender_name.<span style="display:none;">:buddy_request_id</span>',
    ],

    // ------------------------
    // Buddy Request Accepted
    'buddy_request_accepted' => [
        'from' => 'Amici',
        'subject' => 'Richiesta di amicizia accettata',
        'body' => 'Il giocatore :accepter_name ti ha aggiunto alla sua lista amici.',
    ],

    // ------------------------
    // Buddy Removed
    'buddy_removed' => [
        'from' => 'Amici',
        'subject' => 'Sei stato eliminato da una lista amici',
        'body' => 'Il giocatore :remover_name ti ha rimosso dalla sua lista amici.',
    ],

    // ------------------------
    // Missile Attack Report (Attacker)
    'missile_attack_report' => [
        'from' => 'Comando Flotta',
        'subject' => 'Attacco missilistico su :target_coords',
        'body' => 'I tuoi missili interplanetari da :origin_planet_name :origin_planet_coords (ID: :origin_planet_id) hanno raggiunto il loro bersaglio a :target_planet_name :target_coords (ID: :target_planet_id, Tipo: :target_type).

Missili lanciati: :missiles_sent
Missili intercettati: :missiles_intercepted
Missili colpiti: :missiles_hit

Difese distrutte: :defenses_destroyed',
        // Sub-keys used by MissileAttackReport::getBody() override
        'missile_singular'   => 'missile',
        'missile_plural'     => 'missili',
        'from_your_planet'   => ' dal tuo pianeta ',
        'smashed_into'       => ' si sono schiantati sul pianeta ',
        'intercepted_label'  => '<b>Missili Intercettati:</b> ',
        'defenses_hit_label' => '<b>Difese Colpite</b><br>',
        'none'               => 'Nessuna<br>',
    ],

    // ------------------------
    // Missile Defense Report (Defender)
    'missile_defense_report' => [
        'from' => 'Comando Difesa',
        'subject' => 'Attacco missilistico su :planet_coords',
        'body' => 'Il tuo pianeta :planet_name a :planet_coords (ID: :planet_id) è stato attaccato da missili interplanetari di :attacker_name!

Missili in arrivo: :missiles_incoming
Missili intercettati: :missiles_intercepted
Missili colpiti: :missiles_hit

Difese distrutte: :defenses_destroyed',
        // Sub-keys used by MissileDefenseReport::getBody() override
        'your_planet'        => 'Il tuo pianeta ',
        'attacked_by_prefix' => ' è stato attaccato da missili interplanetari di <b>',
        'incoming_label'     => '<b>Missili in Arrivo:</b> ',
        'intercepted_label'  => '<b>Missili Intercettati:</b> ',
        'defenses_hit_label' => '<b>Difese Colpite</b><br>',
        'none'               => 'Nessuna<br>',
    ],
];
