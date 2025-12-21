<?php

return [
    // Wreck Field Information and Status
    'wreck_field' => 'Wreck Field',
    'wreck_field_formed' => 'Wreck field has formed at coordinates {coordinates}',
    'wreck_field_expired' => 'Wreck field has expired',
    'wreck_field_burned' => 'Wreck field has been burned',

    // Wreck Field Conditions
    'formation_conditions' => 'A wreck field forms when at least {min_resources} resources are lost and at least {min_percentage}% of the defending fleet is destroyed.',
    'resources_lost' => 'Resources lost: {amount}',
    'fleet_percentage' => 'Fleet destroyed: {percentage}%',

    // Repair Information
    'repair_time' => 'Repair time',
    'repair_progress' => 'Repair progress',
    'repair_completed' => 'Repair completed',
    'repairs_underway' => 'Repairs underway',
    'repair_duration_min' => 'Minimum repair time: {minutes} minutes',
    'repair_duration_max' => 'Maximum repair time: {hours} hours',
    'repair_speed_bonus' => 'Space Dock level {level} provides {bonus}% repair speed bonus',

    // Ships in Wreck Field
    'ships_in_wreck_field' => 'Ships in wreck field',
    'ship_type' => 'Ship type',
    'quantity' => 'Quantity',
    'repairable' => 'Repairable',
    'total_ships' => 'Total ships: {count}',

    // Actions
    'start_repairs' => 'Start repairs',
    'complete_repairs' => 'Complete repairs',
    'burn_wreck_field' => 'Burn wreck field',
    'cancel_repairs' => 'Cancel repairs',

    // Action Messages
    'repair_started' => 'Repairs have started. Completion time: {time}',
    'repairs_completed' => 'All repairs have been completed. Ships are ready for deployment.',
    'wreck_field_burned_success' => 'Wreck field has been successfully burned.',
    'cannot_repair' => 'This wreck field cannot be repaired.',
    'cannot_burn' => 'This wreck field cannot be burned while repairs are in progress.',

    // Galaxy View
    'wreck_field_icon' => 'WF',
    'wreck_field_tooltip' => 'Wreck Field ({time_remaining} remaining)',
    'click_to_repair' => 'Click to go to Space Dock for repairs',
    'no_wreck_field' => 'No wreck field',

    // Space Dock Integration
    'space_dock_required' => 'Space Dock level 1 is required to repair wreck fields.',
    'space_dock_level' => 'Space Dock level: {level}',
    'upgrade_space_dock' => 'Upgrade Space Dock to repair more ships',
    'repair_capacity_reached' => 'Maximum repair capacity reached. Upgrade Space Dock to increase capacity.',

    // Battle Reports
    'wreck_field_section' => 'Wreck Field Information',
    'ships_available_for_repair' => 'Ships available for repair: {count}',
    'wreck_field_resources' => 'Wreck field contains approximately {value} resources worth of ships.',

    // Admin Settings
    'settings_title' => 'Wreck Field Settings',
    'enabled_description' => 'Wreck fields allow recovery of destroyed ships through the Space Dock building. Ships can be repaired if the destruction meets certain criteria.',
    'percentage_setting' => 'Destroyed ships in wreck field:',
    'min_resources_setting' => 'Minimum destruction for wreck fields:',
    'min_fleet_percentage_setting' => 'Minimum fleet destruction percentage:',
    'lifetime_setting' => 'Wreck field lifetime (hours):',
    'repair_max_time_setting' => 'Maximum repair time (hours):',
    'repair_min_time_setting' => 'Minimum repair time (minutes):',

    // Errors and Warnings
    'error_no_wreck_field' => 'No wreck field found at this location.',
    'error_not_owner' => 'You do not own this wreck field.',
    'error_already_repairing' => 'Repairs are already in progress.',
    'error_no_ships' => 'No ships available for repair.',
    'error_space_dock_required' => 'Space Dock level 1 is required to repair wreck fields.',
    'warning_auto_return' => 'Repaired ships will be automatically returned to service {hours} hours after repair completion.',

    // Time Remaining
    'time_remaining' => '{hours}h {minutes}m remaining',
    'expires_soon' => 'Expires soon',
    'repair_time_remaining' => 'Repair completion: {time}',

    // Status Messages
    'status_active' => 'Active',
    'status_repairing' => 'Repairing',
    'status_completed' => 'Completed',
    'status_burned' => 'Burned',
    'status_expired' => 'Expired',
];
