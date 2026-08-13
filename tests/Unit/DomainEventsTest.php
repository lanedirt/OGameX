<?php

namespace Tests\Unit;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use OGame\Events\BuildingCompleted;
use OGame\Events\FleetArrived;
use OGame\Events\FleetDeparted;
use OGame\Events\FleetReturned;
use OGame\Events\MissionResolved;
use OGame\Events\PlanetColonized;
use OGame\Events\ResearchCompleted;
use Tests\TestCase;

class DomainEventsTest extends TestCase
{
    public function test_module_facing_domain_events_dispatch_after_commit(): void
    {
        $events = [
            BuildingCompleted::class,
            FleetArrived::class,
            FleetDeparted::class,
            FleetReturned::class,
            MissionResolved::class,
            PlanetColonized::class,
            ResearchCompleted::class,
        ];

        foreach ($events as $event) {
            $this->assertContains(ShouldDispatchAfterCommit::class, class_implements($event));
        }
    }
}
