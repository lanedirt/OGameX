<?php

namespace OGame\ViewModels\Queue;

use OGame\GameObjects\Models\Abstracts\GameObject;
use OGame\Services\PlanetService;
use OGame\ViewModels\Queue\Abstracts\QueueViewModel;

class ResearchQueueViewModel extends QueueViewModel
{
    /**
     * Planet service of the planet where this research queue item was started.
     *
     * @var PlanetService
     */
    public PlanetService $planet;

    public bool $building;
    public int $level_target;

    /**
     * Constructor.
     *
     * @param int $id
     * @param GameObject $object
     * @param int $time_countdown
     * @param int $time_total
     * @param PlanetService $planet
     * @param bool $building
     * @param int $level_target
     */
    public function __construct(
        int $id,
        GameObject $object,
        int $time_countdown,
        int $time_total,
        PlanetService $planet,
        bool $building,
        int $level_target
    ) {
        $this->planet = $planet;
        $this->building = $building;
        $this->level_target = $level_target;

        parent::__construct($id, $object, $time_countdown, $time_total);
    }
}
