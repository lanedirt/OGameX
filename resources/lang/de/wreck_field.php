<?php

return [
    // Wreck Field Information and Status
    'wreck_field' => 'Wrackfeld',
    'wreck_field_formed' => 'Ein Wrackfeld hat sich bei den Koordinaten {coordinates} gebildet',
    'wreck_field_expired' => 'Das Wrackfeld ist abgelaufen',
    'wreck_field_burned' => 'Das Wrackfeld wurde verbrannt',

    // Wreck Field Conditions
    'formation_conditions' => 'Ein Wrackfeld bildet sich, wenn mindestens {min_resources} Rohstoffe verloren gehen und mindestens {min_percentage}% der verteidigenden Flotte zerstört wird.',
    'resources_lost' => 'Verlorene Rohstoffe: {amount}',
    'fleet_percentage' => 'Flotte zerstört: {percentage}%',

    // Repair Information
    'repair_time' => 'Reparaturzeit',
    'repair_progress' => 'Reparaturfortschritt',
    'repair_completed' => 'Reparatur abgeschlossen',
    'repairs_underway' => 'Reparaturen laufen',
    'repair_duration_min' => 'Minimale Reparaturzeit: {minutes} Minuten',
    'repair_duration_max' => 'Maximale Reparaturzeit: {hours} Stunden',
    'repair_speed_bonus' => 'Raumdock Stufe {level} gewährt {bonus}% Reparaturgeschwindigkeitsbonus',

    // Ships in Wreck Field
    'ships_in_wreck_field' => 'Schiffe im Wrackfeld',
    'ship_type' => 'Schiffstyp',
    'quantity' => 'Anzahl',
    'repairable' => 'Reparierbar',
    'total_ships' => 'Schiffe gesamt: {count}',

    // Actions
    'start_repairs' => 'Reparaturen starten',
    'complete_repairs' => 'Reparaturen abschließen',
    'burn_wreck_field' => 'Wrackfeld verbrennen',
    'cancel_repairs' => 'Reparaturen abbrechen',

    // Action Messages
    'repair_started' => 'Reparaturen wurden gestartet. Fertigstellungszeit: {time}',
    'repairs_completed' => 'Alle Reparaturen wurden abgeschlossen. Schiffe sind einsatzbereit.',
    'wreck_field_burned_success' => 'Das Wrackfeld wurde erfolgreich verbrannt.',
    'cannot_repair' => 'Dieses Wrackfeld kann nicht repariert werden.',
    'cannot_burn' => 'Dieses Wrackfeld kann nicht verbrannt werden, solange Reparaturen laufen.',

    // Galaxy View
    'wreck_field_icon' => 'WF',
    'wreck_field_tooltip' => 'Wrackfeld ({time_remaining} verbleibend)',
    'click_to_repair' => 'Klicke, um zum Raumdock für Reparaturen zu gelangen',
    'no_wreck_field' => 'Kein Wrackfeld',

    // Space Dock Integration
    'space_dock_required' => 'Raumdock Stufe 1 ist erforderlich, um Wrackfelder zu reparieren.',
    'space_dock_level' => 'Raumdock-Stufe: {level}',
    'upgrade_space_dock' => 'Raumdock ausbauen, um mehr Schiffe zu reparieren',
    'repair_capacity_reached' => 'Maximale Reparaturkapazität erreicht. Baue das Raumdock aus, um die Kapazität zu erhöhen.',

    // Battle Reports
    'wreck_field_section' => 'Wrackfeld-Informationen',
    'ships_available_for_repair' => 'Zur Reparatur verfügbare Schiffe: {count}',
    'wreck_field_resources' => 'Das Wrackfeld enthält Schiffe im Wert von ungefähr {value} Rohstoffen.',

    // Admin Settings
    'settings_title' => 'Wrackfeld-Einstellungen',
    'enabled_description' => 'Wrackfelder ermöglichen die Wiederherstellung zerstörter Schiffe über das Raumdock-Gebäude. Schiffe können repariert werden, wenn die Zerstörung bestimmte Kriterien erfüllt.',
    'percentage_setting' => 'Zerstörte Schiffe im Wrackfeld:',
    'min_resources_setting' => 'Mindestzerstörung für Wrackfelder:',
    'min_fleet_percentage_setting' => 'Mindestprozentsatz der Flottenzerstörung:',
    'lifetime_setting' => 'Wrackfeld-Lebensdauer (Stunden):',
    'repair_max_time_setting' => 'Maximale Reparaturzeit (Stunden):',
    'repair_min_time_setting' => 'Minimale Reparaturzeit (Minuten):',

    // Errors and Warnings
    'error_no_wreck_field' => 'Kein Wrackfeld an diesem Standort gefunden.',
    'error_not_owner' => 'Dieses Wrackfeld gehört dir nicht.',
    'error_already_repairing' => 'Reparaturen sind bereits im Gange.',
    'error_no_ships' => 'Keine Schiffe zur Reparatur verfügbar.',
    'error_space_dock_required' => 'Raumdock Stufe 1 ist erforderlich, um Wrackfelder zu reparieren.',
    'error_cannot_collect_late_added' => 'Schiffe, die während laufender Reparaturen hinzugefügt wurden, können nicht manuell abgeholt werden. Du musst warten, bis alle Reparaturen automatisch abgeschlossen sind.',
    'warning_auto_return' => 'Reparierte Schiffe werden {hours} Stunden nach Abschluss der Reparatur automatisch wieder in Dienst gestellt.',

    // Time Remaining
    'time_remaining' => '{hours}h {minutes}m verbleibend',
    'expires_soon' => 'Läuft bald ab',
    'repair_time_remaining' => 'Reparatur abgeschlossen: {time}',

    // Status Messages
    'status_active' => 'Aktiv',
    'status_repairing' => 'Wird repariert',
    'status_completed' => 'Abgeschlossen',
    'status_burned' => 'Verbrannt',
    'status_expired' => 'Abgelaufen',

    // Action Results
    'repairs_started' => 'Reparaturen erfolgreich gestartet',
    'all_ships_deployed' => 'Alle Schiffe wurden wieder in Dienst gestellt',
    'no_ships_ready' => 'Keine Schiffe bereit zur Abholung',
    'repairs_not_started' => 'Reparaturen wurden noch nicht gestartet',
];
