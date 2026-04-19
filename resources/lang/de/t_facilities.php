<?php

return [
    // Raumdock
    'space_dock' => [
        'name' => 'Raumdock',
        'description' => 'Wracks können im Raumdock repariert werden.',
        'description_long' => 'Das Raumdock bietet die Möglichkeit, im Kampf zerstörte Schiffe zu reparieren, die Wrackteile hinterlassen haben. Die Reparaturzeit beträgt maximal 12 Stunden, es dauert jedoch mindestens 30 Minuten, bis die Schiffe wieder in Dienst gestellt werden können.

Da das Raumdock im Orbit schwebt, benötigt es kein Planetenfeld.',
        'requirements' => 'Benötigt Raumschiffswerft Stufe 2',
        'field_consumption' => 'Verbraucht keine Planetenfelder (schwebt im Orbit)',

        // Raumdock-Oberfläche
        'wreck_field_section' => 'Trümmerfeld',
        'no_wreck_field' => 'Kein Trümmerfeld an dieser Position verfügbar.',
        'wreck_field_info' => 'Ein Trümmerfeld mit reparierbaren Schiffen ist verfügbar.',
        'ships_available' => 'Zur Reparatur verfügbare Schiffe: {count}',
        'repair_capacity' => 'Reparaturkapazität basierend auf Raumdock Stufe {level}',

        // Reparatur-Aktionen
        'start_repair' => 'Trümmerfeld-Reparatur starten',
        'repair_in_progress' => 'Reparatur läuft',
        'repair_completed' => 'Reparatur abgeschlossen',
        'deploy_ships' => 'Reparierte Schiffe in Dienst stellen',
        'burn_wreck_field' => 'Trümmerfeld verglühen',

        // Reparatur-Informationen
        'repair_time' => 'Geschätzte Reparaturzeit: {time}',
        'repair_progress' => 'Reparaturfortschritt: {progress}%',
        'completion_time' => 'Fertigstellung: {time}',
        'auto_deploy_warning' => 'Schiffe werden {hours} Stunden nach Abschluss der Reparatur automatisch in Dienst gestellt, sofern sie nicht manuell eingesetzt werden.',

        // Stufeneffekte
        'level_effects' => [
            'repair_speed' => 'Reparaturgeschwindigkeit um {bonus}% erhöht',
            'capacity_increase' => 'Maximale Anzahl reparierbarer Schiffe erhöht',
        ],

        // Statusmeldungen
        'status' => [
            'no_dock' => 'Raumdock erforderlich, um Trümmerfelder zu reparieren',
            'level_too_low' => 'Raumdock Stufe 1 erforderlich, um Trümmerfelder zu reparieren',
            'no_wreck_field' => 'Kein Trümmerfeld verfügbar',
            'repairing' => 'Trümmerfeld wird derzeit repariert',
            'ready_to_deploy' => 'Reparatur abgeschlossen, Schiffe bereit zur Indienststellung',
        ],
    ],

    // Allgemeine Anlagen-Meldungen
    'actions' => [
        'build' => 'Bauen',
        'upgrade' => 'Ausbauen auf Stufe {level}',
        'downgrade' => 'Rückbauen auf Stufe {level}',
        'demolish' => 'Abreißen',
        'cancel' => 'Abbrechen',
    ],

    // Voraussetzungen
    'requirements' => [
        'met' => 'Voraussetzungen erfüllt',
        'not_met' => 'Voraussetzungen nicht erfüllt',
        'research' => 'Forschung: {requirement}',
        'building' => 'Gebäude: {requirement} Stufe {level}',
    ],

    // Ressourcen
    'cost' => [
        'metal' => 'Metall: {amount}',
        'crystal' => 'Kristall: {amount}',
        'deuterium' => 'Deuterium: {amount}',
        'energy' => 'Energie: {amount}',
        'dark_matter' => 'Dunkle Materie: {amount}',
        'total' => 'Gesamtkosten: {amount}',
    ],

    // Zeit
    'construction_time' => 'Bauzeit: {time}',
    'upgrade_time' => 'Ausbauzeit: {time}',
];
