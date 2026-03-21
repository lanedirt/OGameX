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
    'missile_attack_report' => [
        'from' => 'Vlootcommando',
        'subject' => 'Raketaanval op :target_coords',
        'body' => 'Jouw interplanetaire raketten van :origin_planet_name :origin_planet_coords (ID: :origin_planet_id) hebben hun doel bereikt op :target_planet_name :target_coords (ID: :target_planet_id, Type: :target_type).

Raketten afgevuurd: :missiles_sent
Raketten onderschept: :missiles_intercepted
Raketten ingeslagen: :missiles_hit

Verwoeste verdedigingen: :defenses_destroyed',
        'missile_singular'   => 'raket',
        'missile_plural'     => 'raketten',
        'from_your_planet'   => ' van jouw planeet ',
        'smashed_into'       => ' zijn ingeslagen op planeet ',
        'intercepted_label'  => 'Raketten Onderschept:',
        'defenses_hit_label' => 'Verdedigingen Geraakt',
        'none'               => 'Geen',
    ],

    // ------------------------
    'missile_defense_report' => [
        'from' => 'Verdedigingscommando',
        'subject' => 'Raketaanval op :planet_coords',
        'body' => 'Jouw planeet :planet_name op :planet_coords (ID: :planet_id) is aangevallen door interplanetaire raketten van :attacker_name!

Inkomende raketten: :missiles_incoming
Raketten onderschept: :missiles_intercepted
Raketten ingeslagen: :missiles_hit

Verwoeste verdedigingen: :defenses_destroyed',
        'your_planet'        => 'Jouw planeet ',
        'attacked_by_prefix' => ' is aangevallen door interplanetaire raketten van ',
        'incoming_label'     => 'Inkomende Raketten:',
        'intercepted_label'  => 'Raketten Onderschept:',
        'defenses_hit_label' => 'Verdedigingen Geraakt',
        'none'               => 'Geen',
    ],
];
