<?php

/**
 * Test script to verify ACS groups are created and fleets are linked
 *
 * This script checks:
 * 1. ACS groups exist in the database
 * 2. Fleet missions are linked to ACS groups
 * 3. ACS groups have the correct target and arrival time
 *
 * Run with: docker compose exec -T ogamex-app php test_acs_coordination.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use OGame\Models\AcsGroup;
use OGame\Models\AcsFleetMember;
use OGame\Models\FleetMission;

echo "=== ACS Coordination Test ===\n\n";

// Test 1: Check for ACS groups
echo "Test 1: Check for ACS groups\n";
echo str_repeat('-', 50) . "\n";
$acsGroups = AcsGroup::all();
echo "Total ACS groups in database: " . $acsGroups->count() . "\n";

if ($acsGroups->count() > 0) {
    echo "\nACS Groups:\n";
    foreach ($acsGroups as $group) {
        echo sprintf(
            "  [%d] %s - Target: %d:%d:%d (Type: %d)\n",
            $group->id,
            $group->name,
            $group->galaxy_to,
            $group->system_to,
            $group->position_to,
            $group->type_to
        );
        echo sprintf(
            "       Creator: %d, Status: %s, Arrival: %s\n",
            $group->creator_id,
            $group->status,
            date('Y-m-d H:i:s', $group->arrival_time)
        );
    }
} else {
    echo "  No ACS groups found. Send some ACS attacks to create groups.\n";
}

echo "\n";

// Test 2: Check for fleet members in ACS groups
echo "Test 2: Check for fleet members in ACS groups\n";
echo str_repeat('-', 50) . "\n";
$acsFleetMembers = AcsFleetMember::with('fleetMission')->get();
echo "Total fleet missions linked to ACS groups: " . $acsFleetMembers->count() . "\n";

if ($acsFleetMembers->count() > 0) {
    echo "\nFleet Members:\n";
    foreach ($acsFleetMembers as $member) {
        $mission = $member->fleetMission;
        if ($mission) {
            echo sprintf(
                "  Fleet Mission #%d (Player %d) -> ACS Group #%d\n",
                $mission->id,
                $mission->user_id,
                $member->acs_group_id
            );
            echo sprintf(
                "    From: %d:%d:%d -> To: %d:%d:%d\n",
                $mission->galaxy_from,
                $mission->system_from,
                $mission->position_from,
                $mission->galaxy_to,
                $mission->system_to,
                $mission->position_to
            );
            echo sprintf(
                "    Mission Type: %d, Status: %s, Arrival: %s\n",
                $mission->mission_type,
                $mission->processed ? 'Processed' : 'In flight',
                date('Y-m-d H:i:s', $mission->time_arrival)
            );
        }
    }
} else {
    echo "  No fleet missions linked to ACS groups.\n";
    echo "  Make sure you're selecting 'ACS Attack' (mission type 2) when sending fleets.\n";
}

echo "\n";

// Test 3: Check for orphaned ACS attack missions (not linked to groups)
echo "Test 3: Check for orphaned ACS attack missions\n";
echo str_repeat('-', 50) . "\n";
$acsAttackMissions = FleetMission::where('mission_type', 2)
    ->where('processed', 0)
    ->get();

$linkedMissionIds = $acsFleetMembers->pluck('fleet_mission_id')->toArray();
$orphanedMissions = $acsAttackMissions->filter(function ($mission) use ($linkedMissionIds) {
    return !in_array($mission->id, $linkedMissionIds);
});

echo "ACS Attack missions in flight: " . $acsAttackMissions->count() . "\n";
echo "Orphaned missions (not linked to ACS group): " . $orphanedMissions->count() . "\n";

if ($orphanedMissions->count() > 0) {
    echo "\n⚠️  Warning: Found orphaned ACS attack missions:\n";
    foreach ($orphanedMissions as $mission) {
        echo sprintf(
            "  Mission #%d from %d:%d:%d to %d:%d:%d\n",
            $mission->id,
            $mission->galaxy_from,
            $mission->system_from,
            $mission->position_from,
            $mission->galaxy_to,
            $mission->system_to,
            $mission->position_to
        );
    }
    echo "\n  These missions will be processed as single-fleet attacks.\n";
}

echo "\n";

// Test 4: Group fleets by target
echo "Test 4: Groups by target coordinate\n";
echo str_repeat('-', 50) . "\n";
$groupsByTarget = [];
foreach ($acsGroups as $group) {
    $targetKey = sprintf(
        "%d:%d:%d (Type %d) @ %s",
        $group->galaxy_to,
        $group->system_to,
        $group->position_to,
        $group->type_to,
        date('H:i:s', $group->arrival_time)
    );

    if (!isset($groupsByTarget[$targetKey])) {
        $groupsByTarget[$targetKey] = [
            'group' => $group,
            'fleets' => [],
        ];
    }

    $fleets = AcsFleetMember::where('acs_group_id', $group->id)->with('fleetMission')->get();
    $groupsByTarget[$targetKey]['fleets'] = $fleets;
}

if (count($groupsByTarget) > 0) {
    echo "Targets being attacked:\n";
    foreach ($groupsByTarget as $target => $data) {
        echo "  Target: $target\n";
        echo "    Group ID: " . $data['group']->id . "\n";
        echo "    Fleet count: " . $data['fleets']->count() . "\n";
        echo "    Status: " . $data['group']->status . "\n";

        if ($data['fleets']->count() > 1) {
            echo "    ✓ Multiple fleets will attack together!\n";
        } else {
            echo "    ⚠️  Only one fleet in this group (will be single attack)\n";
        }
    }
} else {
    echo "  No active ACS attacks.\n";
}

echo "\n";
echo "=== Test Complete ===\n";
echo "\n";
echo "To test ACS coordination:\n";
echo "1. Send an ACS attack from Planet A to a target\n";
echo "2. Send another ACS attack from Planet B to the SAME target\n";
echo "3. Make sure both arrive at the same time (same speed %)\n";
echo "4. Run this script to verify both fleets are in the same ACS group\n";
echo "5. Wait for arrival - you should get ONE battle report for the combined attack\n";
