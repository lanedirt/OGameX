<?php

namespace OGame\Services\NpcEmpire;

use Exception;
use OGame\Models\Resource;
use OGame\Services\BuildingQueueService;
use OGame\Services\ObjectService;
use OGame\Services\PlanetService;
use OGame\Services\PlayerService;
use OGame\Services\ResearchQueueService;

/**
 * Class NpcDevelopmentAi.
 *
 * Autonomous development strategy for NPC empires: upgrades mines, energy
 * plants, storage and facilities per planet, and keeps research running.
 * Uses the exact same queue services as the player UI, so all OGame rules
 * (requirements, costs, build times) apply identically.
 *
 * @package OGame\Services\NpcEmpire
 */
class NpcDevelopmentAi
{
    /**
     * Facility upgrade targets. The AI stops queueing facilities at these
     * levels (kept modest so early-universe empires stay balanced).
     *
     * @var array<string, int>
     */
    private const FACILITY_TARGETS = [
        'research_lab' => 8,
        'shipyard'     => 6,
        'robot_factory' => 6,
    ];

    /**
     * Order in which facilities are considered for the next upgrade.
     *
     * @var list<string>
     */
    private const FACILITY_ORDER = ['research_lab', 'shipyard', 'robot_factory'];

    /**
     * Research priority, evaluated top down: the first technology that
     * satisfies requirements (checked by the queue service) wins.
     *
     * @var list<string>
     */
    private const RESEARCH_PRIORITY = [
        'combustion_drive',
        'energy_technology',
        'espionage_technology',
        'computer_technology',
        // Astrophysics early: it gates the expansion AI's planet count.
        'astrophysics',
        'weapon_technology',
        'armor_technology',
        'shielding_technology',
        'laser_technology',
        'impulse_drive',
        'ion_technology',
        'hyperspace_technology',
        'plasma_technology',
        'hyperspace_drive',
        'intergalactic_research_network',
    ];

    /**
     * Storage fill ratio at which the AI expands storage preemptively.
     */
    private const STORAGE_EXPAND_RATIO = 0.9;

    /**
     * Decide and enqueue development actions for all planets of the player.
     *
     * @param PlayerService $player
     */
    public function decide(PlayerService $player): void
    {
        $this->planResearch($player);

        foreach ($player->planets->allPlanets() as $planet) {
            $this->planPlanetDevelopment($planet);
        }
    }

    /**
     * Try to enqueue the next research on the empire's research planet.
     * The research queue services reject unaffordable candidates with an
     * exception, which is used to fall through the priority list.
     *
     * @param PlayerService $player
     */
    private function planResearch(PlayerService $player): void
    {
        $queue = resolve(ResearchQueueService::class);
        $planet = $player->planets->first();

        if ($planet === null) {
            return;
        }

        try {
            if ($queue->retrieveQueue($planet)->isQueueFull()) {
                return;
            }
        } catch (Exception) {
            return;
        }

        foreach (self::RESEARCH_PRIORITY as $machineName) {
            try {
                $research = ObjectService::getResearchObjectByMachineName($machineName);
                $queue->add($player, $planet, $research->id);

                return;
            } catch (Exception) {
                // Requirements not met or planet cannot afford it yet:
                // fall through to the next candidate in the priority list.
                continue;
            }
        }
    }

    /**
     * Try to enqueue one building upgrade on the given planet following
     * the dynamic build priority for its current state.
     *
     * @param PlanetService $planet
     */
    private function planPlanetDevelopment(PlanetService $planet): void
    {
        $queue = resolve(BuildingQueueService::class);

        try {
            if ($queue->retrieveQueue($planet)->isQueueFull()) {
                return;
            }
        } catch (Exception) {
            return;
        }

        foreach ($this->getBuildPriority($planet) as $machineName) {
            try {
                $object = ObjectService::getObjectByMachineName($machineName);
                $queue->add($planet, $object->id);

                return;
            } catch (Exception) {
                // Not affordable, requirements missing or fields exhausted:
                // try the next candidate in the priority list.
                continue;
            }
        }
    }

    /**
     * Build the ordered candidate list for the next building upgrade,
     * based on the planet's current state:
     *  1. Energy balance first (production throttles below 100% otherwise)
     *  2. Mines, with metal weighted double (classic OGame growth advice)
     *  3. Facilities towards their target levels
     *  4. Storage expansion when close to the capacity cap
     *
     * @param PlanetService $planet
     * @return list<string>
     */
    private function getBuildPriority(PlanetService $planet): array
    {
        $priority = [];

        // 1. Restore full production efficiency first.
        if ($planet->getResourceProductionFactor() < 100) {
            $priority[] = 'solar_plant';
        }

        // 2. Mines (metal twice as often as crystal, deuterium least).
        $priority[] = 'metal_mine';
        $priority[] = 'crystal_mine';
        $priority[] = 'metal_mine';
        $priority[] = 'deuterium_synthesizer';

        // 3. Facilities below their target levels.
        foreach (self::FACILITY_ORDER as $facility) {
            if ($planet->getObjectLevel($facility) < self::FACILITY_TARGETS[$facility]) {
                $priority[] = $facility;
            }
        }

        // 4. Storage: expand before resource overflow throttles growth.
        if (self::isStorageNearCap($planet->metal(), $planet->metalStorage())) {
            $priority[] = 'metal_store';
        }
        if (self::isStorageNearCap($planet->crystal(), $planet->crystalStorage())) {
            $priority[] = 'crystal_store';
        }
        if (self::isStorageNearCap($planet->deuterium(), $planet->deuteriumStorage())) {
            $priority[] = 'deuterium_store';
        }

        return $priority;
    }

    /**
     * Check whether a resource is close to its storage cap.
     *
     * @param Resource $current Current amount of the resource.
     * @param Resource $capacity Maximum storage capacity of the resource.
     * @return bool
     */
    private function isStorageNearCap(Resource $current, Resource $capacity): bool
    {
        return $current->get() >= $capacity->get() * self::STORAGE_EXPAND_RATIO;
    }
}
