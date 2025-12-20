<?php

return [
    // Space Dock Building
    'space_dock' => [
        'name' => 'Space Dock',
        'description' => 'Wreckages can be repaired in the Space Dock.',
        'description_long' => 'The Space Dock offers the possibility to repair ships destroyed in battle which left behind wreckage. The repair time takes a maximum of 12 hours, but it takes at least 30 minutes until the ships can be put back into service.

Since the Space Dock floats in orbit, it does not require a planet field.',
        'requirements' => 'Requires Shipyard level 2',
        'field_consumption' => 'Does not consume planet fields (floats in orbit)',

        // Space Dock Interface
        'wreck_field_section' => 'Wreck Field',
        'no_wreck_field' => 'No wreck field available at this location.',
        'wreck_field_info' => 'A wreck field is available containing ships that can be repaired.',
        'ships_available' => 'Ships available for repair: {count}',
        'repair_capacity' => 'Repair capacity based on Space Dock level {level}',

        // Repair Actions
        'start_repair' => 'Start repairing wreck field',
        'repair_in_progress' => 'Repairs in progress',
        'repair_completed' => 'Repairs completed',
        'deploy_ships' => 'Deploy repaired ships',
        'burn_wreck_field' => 'Burn wreck field',

        // Repair Information
        'repair_time' => 'Estimated repair time: {time}',
        'repair_progress' => 'Repair progress: {progress}%',
        'completion_time' => 'Completion: {time}',
        'auto_deploy_warning' => 'Ships will be automatically deployed {hours} hours after repair completion if not manually deployed.',

        // Level Effects
        'level_effects' => [
            'repair_speed' => 'Repair speed increased by {bonus}%',
            'capacity_increase' => 'Maximum repairable ships increased',
        ],

        // Status Messages
        'status' => [
            'no_dock' => 'Space Dock required to repair wreck fields',
            'level_too_low' => 'Space Dock level 1 required to repair wreck fields',
            'no_wreck_field' => 'No wreck field available',
            'repairing' => 'Currently repairing wreck field',
            'ready_to_deploy' => 'Repairs completed, ships ready for deployment',
        ],
    ],

    // General Facilities Messages
    'actions' => [
        'build' => 'Build',
        'upgrade' => 'Upgrade to level {level}',
        'downgrade' => 'Downgrade to level {level}',
        'demolish' => 'Demolish',
        'cancel' => 'Cancel',
    ],

    // Requirements
    'requirements' => [
        'met' => 'Requirements met',
        'not_met' => 'Requirements not met',
        'research' => 'Research: {requirement}',
        'building' => 'Building: {requirement} level {level}',
    ],

    // Resources
    'cost' => [
        'metal' => 'Metal: {amount}',
        'crystal' => 'Crystal: {amount}',
        'deuterium' => 'Deuterium: {amount}',
        'energy' => 'Energy: {amount}',
        'dark_matter' => 'Dark Matter: {amount}',
        'total' => 'Total cost: {amount}',
    ],

    // Time
    'construction_time' => 'Construction time: {time}',
    'upgrade_time' => 'Upgrade time: {time}',
];