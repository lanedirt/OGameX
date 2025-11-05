<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Check for ACS groups
$groups = \OGame\Models\AcsGroup::all();

echo "=== ALL ACS GROUPS ===\n";
echo "Total groups: " . $groups->count() . "\n\n";

foreach ($groups as $group) {
    echo "Group ID: {$group->id}\n";
    echo "  Name: {$group->name}\n";
    echo "  Creator ID: {$group->creator_id}\n";
    echo "  Target: {$group->galaxy_to}:{$group->system_to}:{$group->position_to} (type: {$group->type_to})\n";
    echo "  Status: {$group->status}\n";
    echo "  Arrival time: " . date('Y-m-d H:i:s', $group->arrival_time) . " (" . $group->arrival_time . ")\n";
    echo "  Current time: " . date('Y-m-d H:i:s', time()) . " (" . time() . ")\n";
    echo "  Arrived: " . ($group->arrival_time <= time() ? 'YES (too late to join)' : 'NO (can join)') . "\n";
    echo "  Fleet count: " . $group->fleetMembers()->count() . "\n\n";
}

// Check what query conditions would match
if ($groups->count() > 0) {
    $firstGroup = $groups->first();

    echo "=== QUERY TEST ===\n";
    echo "Simulating query for target {$firstGroup->galaxy_to}:{$firstGroup->system_to}:{$firstGroup->position_to}\n\n";

    $matchingGroups = \OGame\Models\AcsGroup::where('galaxy_to', $firstGroup->galaxy_to)
        ->where('system_to', $firstGroup->system_to)
        ->where('position_to', $firstGroup->position_to)
        ->where('type_to', $firstGroup->type_to)
        ->whereIn('status', ['pending', 'active'])
        ->where('arrival_time', '>', time())
        ->get();

    echo "Matching groups: " . $matchingGroups->count() . "\n";
    if ($matchingGroups->count() === 0) {
        echo "Reasons groups might not match:\n";
        if (!in_array($firstGroup->status, ['pending', 'active'])) {
            echo "  - Status '{$firstGroup->status}' is not 'pending' or 'active'\n";
        }
        if ($firstGroup->arrival_time <= time()) {
            echo "  - Arrival time has already passed\n";
        }
    }
}
