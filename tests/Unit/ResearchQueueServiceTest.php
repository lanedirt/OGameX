<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use OGame\Models\Planet;
use OGame\Models\ResearchQueue;
use OGame\Models\User;
use OGame\Services\ObjectService;
use OGame\Services\ResearchQueueService;
use Tests\UnitTestCase;

class ResearchQueueServiceTest extends UnitTestCase
{
    use DatabaseTransactions;

    protected ResearchQueueService $research_queue;

    /**
     * Set up common test components.
     *
     * @throws BindingResolutionException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $object_service = new ObjectService();
        $this->research_queue = new ResearchQueueService($object_service);

        $planet = Planet::factory()->make(['id' => 1]);
        $this->planetService->setPlanet($planet);

        User::factory()->make(['id' => 1]);
        $this->playerService->load(1);
    }

    /**
     * Tests object is found from research queue 
     */
    public function testIsObjectInResearchQueue(): void
    {
        // Add level 3 impulse drive to research queue
        $queue = new ResearchQueue();
        $queue->planet_id = $this->planetService->getPlanetId();
        $queue->object_id = 117;
        $queue->object_level_target = 3;
        $queue->save();

        $this->assertTrue($this->research_queue->objectInResearchQueue($this->playerService, 'impulse_drive', 3));
        $this->assertFalse($this->research_queue->objectInResearchQueue($this->playerService, 'impulse_drive', 4));
        $this->assertFalse($this->research_queue->objectInResearchQueue($this->playerService, 'energy_technology', 4));
    }

    /**
     * Tests research queue item is cancelled if requirements are not met.
     */
    public function testCancelItemMissingRequirements(): void
    {
        // Add level 1 energy technology to research queue
        $queue_energy_tech = new ResearchQueue();
        $queue_energy_tech->planet_id = $this->planetService->getPlanetId();
        $queue_energy_tech->object_id = 113;
        $queue_energy_tech->object_level_target = 1;
        $queue_energy_tech->save();

        // Add level 1 impulse drive to research queue
        $queue = new ResearchQueue();
        $queue->planet_id = $this->planetService->getPlanetId();
        $queue->object_id = 117;
        $queue->object_level_target = 1;
        $queue->save();

        // Assert that impulse drive is in research queue
        $this->assertTrue($this->research_queue->objectInResearchQueue($this->playerService, 'impulse_drive', 1));

        // Cancel energy technology
        $this->research_queue->cancel($this->playerService, $queue_energy_tech->id, 113);
        $this->research_queue->cancelItemMissingRequirements($this->playerService, $this->planetService);

        // Assert that impulse drive is in research queue
        $this->assertFalse($this->research_queue->objectInResearchQueue($this->playerService, 'impulse_drive', 1));       
    }
}
