<?php

return [
    // ------------------------
    'return_of_fleet_subject' => 'Terugkeer van een vloot',
    'return_of_fleet_body' => 'Je vloot keert terug van planeet :from naar planeet :to.

De vloot levert:

Metaal: :metal
Kristal: :crystal
Deuterium: :deuterium',

    // ------------------------
    'return_of_fleet_no_goods_subject' => 'Terugkeer van een vloot',
    'return_of_fleet_no_goods_body' => 'Je vloot keert terug van planeet :from naar planeet :to.

De vloot levert geen grondstoffen af.',

    // ------------------------
    // Fleet messages (nested array format used by GameMessage system)
    'return_of_fleet_with_resources' => [
        'from' => 'Vlootcommando',
        'subject' => 'Terugkeer van een vloot',
        'body' => 'Je vloot keert terug van :from naar :to en heeft zijn goederen afgeleverd:

Metaal: :metal
Kristal: :crystal
Deuterium: :deuterium',
    ],

    // ------------------------
    'return_of_fleet' => [
        'from' => 'Vlootcommando',
        'subject' => 'Terugkeer van een vloot',
        'body' => 'Je vloot keert terug van :from naar :to.

De vloot levert geen grondstoffen af.',
    ],

    // ------------------------
    'fleet_deployment_with_resources' => [
        'from' => 'Vlootcommando',
        'subject' => 'Terugkeer van een vloot',
        'body' => 'Een van je vloten van :from heeft :to bereikt en zijn goederen afgeleverd:

Metaal: :metal
Kristal: :crystal
Deuterium: :deuterium',
    ],

    // ------------------------
    'fleet_deployment' => [
        'from' => 'Vlootcommando',
        'subject' => 'Terugkeer van een vloot',
        'body' => 'Een van je vloten van :from heeft :to bereikt. De vloot levert geen grondstoffen af.',
    ],

    // ------------------------
    'transport_arrived' => [
        'from' => 'Vlootcommando',
        'subject' => 'Aankomst bij een planeet',
        'body' => 'Je vloot van :from bereikt :to en levert zijn goederen af:
Metaal: :metal Kristal: :crystal Deuterium: :deuterium',
    ],

    // ------------------------
    'transport_received' => [
        'from' => 'Vlootcommando',
        'subject' => 'Inkomende vloot',
        'body' => 'Een inkomende vloot van :from heeft je planeet :to bereikt en zijn goederen afgeleverd:
Metaal: :metal Kristal: :crystal Deuterium: :deuterium',
    ],

    // ------------------------
    // Missile Attack Report (Attacker)
    'missile_attack_report' => [
        'from' => 'Vlootcommando',
        'subject' => 'Raketaanval op :target_coords',
        'body' => 'Je interplanetaire raketten van :origin_planet_name :origin_planet_coords (ID: :origin_planet_id) hebben hun doelwit bereikt op :target_planet_name :target_coords (ID: :target_planet_id, Type: :target_type).

Raketten gelanceerd: :missiles_sent
Raketten onderschept: :missiles_intercepted
Raketten geraakt: :missiles_hit

Verdediging vernietigd: :defenses_destroyed',
        // Sub-keys used by MissileAttackReport::getBody() override
        'missile_singular'   => 'raket',
        'missile_plural'     => 'raketten',
        'from_your_planet'   => ' van je planeet ',
        'smashed_into'       => ' zijn neergestort op de planeet ',
        'intercepted_label'  => '<b>Onderschepte Raketten:</b> ',
        'defenses_hit_label' => '<b>Getroffen Verdediging</b><br>',
        'none'               => 'Geen<br>',
    ],

    // ------------------------
    // Missile Defense Report (Defender)
    'missile_defense_report' => [
        'from' => 'Verdedigingscommando',
        'subject' => 'Raketaanval op :planet_coords',
        'body' => 'Je planeet :planet_name op :planet_coords (ID: :planet_id) is aangevallen door interplanetaire raketten van :attacker_name!

Inkomende raketten: :missiles_incoming
Onderschepte raketten: :missiles_intercepted
Gerichte raketten: :missiles_hit

Verdediging vernietigd: :defenses_destroyed',
        // Sub-keys used by MissileDefenseReport::getBody() override
        'your_planet'        => 'Je planeet ',
        'attacked_by_prefix' => ' is aangevallen door interplanetaire raketten van <b>',
        'incoming_label'     => '<b>Inkomende Raketten:</b> ',
        'intercepted_label'  => '<b>Onderschepte Raketten:</b> ',
        'defenses_hit_label' => '<b>Getroffen Verdediging</b><br>',
        'none'               => 'Geen<br>',
    ],
];
