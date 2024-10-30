<?php

namespace Tests\Unit;

use OGame\Models\ResearchQueue;
use OGame\Services\ObjectService;
use OGame\Services\ResearchQueueService;
use Tests\AccountTestCase;

class ResearchQueueServiceTest extends AccountTestCase
{
    protected ResearchQueueService $research_queue;

    /**
     * Set up common test components.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $object_service = new ObjectService();
        $this->research_queue = new ResearchQueueService($object_service);
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

        $this->assertTrue($this->research_queue->objectInResearchQueue($this->planetService->getPlayer(), 'impulse_drive', 3));
        $this->assertFalse($this->research_queue->objectInResearchQueue($this->planetService->getPlayer(), 'impulse_drive', 4));
        $this->assertFalse($this->research_queue->objectInResearchQueue($this->planetService->getPlayer(), 'energy_technology', 4));
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
        $this->assertTrue($this->research_queue->objectInResearchQueue($this->planetService->getPlayer(), 'impulse_drive', 1));

        // Cancel energy technology
        $this->research_queue->cancel($this->planetService->getPlayer(), $queue_energy_tech->id, 113);
        $this->research_queue->cancelItemMissingRequirements($this->planetService->getPlayer(), $this->planetService);

        // Assert that impulse drive is in research queue
        $this->assertFalse($this->research_queue->objectInResearchQueue($this->planetService->getPlayer(), 'impulse_drive', 1));
    }
}
