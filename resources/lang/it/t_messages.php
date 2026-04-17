<?php

return [
    // ------------------------
    'return_of_fleet_with_resources' => [
        'from' => 'Comando Flotta',
        'subject' => 'Ritorno di una flotta',
        'body' => 'La tua flotta sta tornando da :from a :to e ha consegnato il suo carico:

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
        'body' => 'Una delle tue flotte da :from ha raggiunto :to e ha consegnato il suo carico:

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
        'body' => 'La tua flotta da :from raggiunge :to e consegna il suo carico:
Metallo: :metal Cristallo: :crystal Deuterio: :deuterium',
    ],

    // ------------------------
    'transport_received' => [
        'from' => 'Comando Flotta',
        'subject' => 'Flotta in arrivo',
        'body' => 'Una flotta in arrivo da :from ha raggiunto il tuo pianeta :to e ha consegnato il suo carico:
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
        'subject' => 'Rapporto insediamento',
        'body' => 'La flotta è arrivata alle coordinate assegnate :coordinates, ha trovato un nuovo pianeta e ha iniziato subito a svilupparlo.',
    ],

    // ------------------------
    'colony_establish_fail_astrophysics' => [
        'from' => 'Coloni',
        'subject' => 'Rapporto insediamento',
        'body' => 'La flotta è arrivata alle coordinate assegnate :coordinates e ha accertato che il pianeta è adatto alla colonizzazione. Poco dopo aver iniziato a sviluppare il pianeta, i coloni si rendono conto che le loro conoscenze di astrofisica non sono sufficienti per completare la colonizzazione di un nuovo pianeta.',
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
        'body' => '(Significa che è stata distrutta al primo round.)',
    ],

    // ------------------------
    'debris_field_harvest' => [
        'from' => 'Flotta',
        'subject' => 'Rapporto raccolta dal CR di :coordinates',
        'body' => 'I tuoi :ship_name (:ship_amount navi) hanno una capacità totale di stoccaggio di :storage_capacity. Al bersaglio :to, :metal Metallo, :crystal Cristallo e :deuterium Deuterio fluttuano nello spazio. Hai raccolto :harvested_metal Metallo, :harvested_crystal Cristallo e :harvested_deuterium Deuterio.',
    ],

    // ------------------------
    // Missile Attack Report (Attaccante)
    'missile_attack_report' => [
        'from' => 'Comando Flotta',
        'subject' => 'Attacco missilistico su :target_coords',
        'body' => 'I tuoi missili interplanetari da :origin_planet_name :origin_planet_coords (ID: :origin_planet_id) hanno raggiunto il loro bersaglio a :target_planet_name :target_coords (ID: :target_planet_id, Tipo: :target_type).

Missili lanciati: :missiles_sent
Missili intercettati: :missiles_intercepted
Missili colpiti: :missiles_hit

Difese distrutte: :defenses_destroyed',
        // Sub-keys usate da MissileAttackReport::getBody()
        'missile_singular'   => 'missile',
        'missile_plural'     => 'missili',
        'from_your_planet'   => ' dal tuo pianeta ',
        'smashed_into'       => ' si è/si sono schiantato/i sul pianeta ',
        'intercepted_label'  => 'Missili Intercettati:',
        'defenses_hit_label' => 'Difese Colpite',
        'none'               => 'Nessuna',
    ],

    // ------------------------
    // Missile Defense Report (Difensore)
    'missile_defense_report' => [
        'from' => 'Comando Difesa',
        'subject' => 'Attacco missilistico su :planet_coords',
        'body' => 'Il tuo pianeta :planet_name a :planet_coords (ID: :planet_id) è stato attaccato da missili interplanetari di :attacker_name!

Missili in arrivo: :missiles_incoming
Missili intercettati: :missiles_intercepted
Missili colpiti: :missiles_hit

Difese distrutte: :defenses_destroyed',
        // Sub-keys usate da MissileDefenseReport::getBody()
        'your_planet'        => 'Il tuo pianeta ',
        'attacked_by_prefix' => ' è stato attaccato da missili interplanetari di ',
        'incoming_label'     => 'Missili in Arrivo:',
        'intercepted_label'  => 'Missili Intercettati:',
        'defenses_hit_label' => 'Difese Colpite',
        'none'               => 'Nessuna',
    ],
];
