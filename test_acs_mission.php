<?php

/**
 * Test script for ACS Attack Mission
 *
 * This script tests:
 * 1. Mission registration in GameMissionFactory
 * 2. Mission instantiation
 * 3. Mission properties (name, type ID, return mission)
 *
 * Run with: docker compose exec ogamex-app php artisan tinker < test_acs_mission.php
 * Or: docker compose exec -T ogamex-app php test_acs_mission.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use OGame\Factories\GameMissionFactory;

echo "=== ACS Attack Mission Test ===\n\n";

// Test 1: Get all missions
echo "Test 1: Get all missions\n";
echo str_repeat('-', 50) . "\n";
try {
    $missions = GameMissionFactory::getAllMissions();
    echo "Total missions registered: " . count($missions) . "\n";

    if (isset($missions[2])) {
        echo "✓ Mission 2 (ACS Attack) is registered\n";
        echo "  Class: " . get_class($missions[2]) . "\n";
        echo "  Name: " . $missions[2]::getName() . "\n";
        echo "  Type ID: " . $missions[2]::getTypeId() . "\n";
    } else {
        echo "✗ Mission 2 (ACS Attack) is NOT registered\n";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 2: Get mission by ID
echo "Test 2: Get mission by ID\n";
echo str_repeat('-', 50) . "\n";
try {
    $mission = GameMissionFactory::getMissionById(2, []);
    echo "✓ Mission instantiated successfully!\n";
    echo "  Class: " . get_class($mission) . "\n";
    echo "  Name: " . $mission::getName() . "\n";
    echo "  Type ID: " . $mission::getTypeId() . "\n";
    echo "  Has return mission: " . ($mission::hasReturnMission() ? 'Yes' : 'No') . "\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 3: List all available missions
echo "Test 3: All registered missions\n";
echo str_repeat('-', 50) . "\n";
try {
    $missions = GameMissionFactory::getAllMissions();
    foreach ($missions as $id => $mission) {
        $name = $mission::getName();
        $hasReturn = $mission::hasReturnMission() ? 'Yes' : 'No';
        echo sprintf("  [%2d] %-20s (Return: %s)\n", $id, $name, $hasReturn);
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 4: Verify ACS models exist
echo "Test 4: Verify ACS models exist\n";
echo str_repeat('-', 50) . "\n";
try {
    $models = [
        'OGame\Models\AcsGroup',
        'OGame\Models\AcsFleetMember',
        'OGame\Models\AcsInvitation',
    ];

    foreach ($models as $model) {
        if (class_exists($model)) {
            echo "✓ Model exists: " . $model . "\n";
        } else {
            echo "✗ Model NOT found: " . $model . "\n";
        }
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 5: Verify ACSService exists
echo "Test 5: Verify ACSService exists\n";
echo str_repeat('-', 50) . "\n";
try {
    if (class_exists('OGame\Services\ACSService')) {
        echo "✓ ACSService exists\n";

        // Check for key methods
        $methods = [
            'createGroup',
            'findGroup',
            'canJoinGroup',
            'addFleetToGroup',
            'getGroupFleets',
            'allFleetsArrived',
        ];

        $reflection = new \ReflectionClass('OGame\Services\ACSService');
        foreach ($methods as $method) {
            if ($reflection->hasMethod($method)) {
                echo "  ✓ Method exists: {$method}()\n";
            } else {
                echo "  ✗ Method NOT found: {$method}()\n";
            }
        }
    } else {
        echo "✗ ACSService NOT found\n";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n";
echo "=== Test Complete ===\n";
