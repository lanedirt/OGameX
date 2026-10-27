<?php

namespace OGame\Services\NpcEmpire;

use OGame\Models\NpcEmpire;
use OGame\Models\UnitQueue;
use OGame\Services\ObjectService;
use OGame\Services\PlanetService;
use OGame\Services\PlayerService;
use OGame\Services\UnitQueueService;

/**
 * Class NpcFleetAi.
 *
 * Autonomous fleet and defense production for NPC empires. Composition
 * scales with the empire's economy, research and its randomized
 * aggression trait. Uses the regular player unit queue, so all OGame
 * rules (requirements, costs, build times) apply identically.
 *
 * @package OGame\Services\NpcEmpire
 */
class NpcFleetAi
{
    /**
     * Espionage probes kept ready on every planet (used by the war AI).
     */
    private const PROBE_TARGET = 6;

    /**
     * Max pending unit queue items the AI maintains per planet.
     */
    private const MAX_PENDING_UNIT_QUEUES = 1;

    /**
     * Max batch size queued per unit type per decision cycle. Keeps
     * resources available for the development AI on later cycles.
     */
    private const MAX_BATCH_SIZE = 30;

    /**
     * Decide and enqueue unit production for all planets of the player.
     *
     * @param PlayerService $player
     * @param NpcEmpire $empire
     */
    public function decide(PlayerService $player, NpcEmpire $empire): void
    {
        foreach ($player->planets->allPlanets() as $planet) {
            $this->planPlanetUnits($planet, $empire);
        }
    }

    /**
     * Try to enqueue one production batch for the first unit type with a
     * deficit (target above current amount).
     *
     * @param PlanetService $planet
     * @param NpcEmpire $empire
     */
    private function planPlanetUnits(PlanetService $planet, NpcEmpire $empire): void
    {
        // Without a shipyard no units can be built at all.
        if ($planet->getObjectLevel('shipyard') < 1) {
            return;
        }

        // Keep at most one pending order per planet; anything more would
        // lock resources away from the development AI.
        $pending = UnitQueue::where('planet_id', $planet->getPlanetId())
            ->where('processed', 0)
            ->count();
        if ($pending >= self::MAX_PENDING_UNIT_QUEUES) {
            return;
        }

        $queue = resolve(UnitQueueService::class);

        foreach ($this->getCompositionTargets($planet, $empire) as $machineName => $target) {
            $current = $planet->getObjectAmount($machineName);

            if ($current >= $target) {
                continue;
            }

            // Only build units the empire actually has the requirements
            // for; locked units are skipped.
            if (!ObjectService::objectRequirementsMet($machineName, $planet)) {
                continue;
            }

            $object = ObjectService::getObjectByMachineName($machineName);

            // add() auto-reduces the amount to what is affordable and
            // silently no-ops when nothing is affordable.
            $queue->add($planet, $object->id, min($target - $current, self::MAX_BATCH_SIZE));

            // One production batch per planet per decision cycle.
            return;
        }
    }

    /**
     * Desired unit composition for a planet, in build-priority order.
     * Targets are evaluated at decision time against current state.
     *
     * @param PlanetService $planet
     * @param NpcEmpire $empire
     * @return array<string, int>
     */
    private function getCompositionTargets(PlanetService $planet, NpcEmpire $empire): array
    {
        return [
            // Recon capability for the war AI.
            'espionage_probe' => self::PROBE_TARGET,

            // Cargo capacity follows the economy: planets with deep mines
            // get haulers for future raiding transports.
            'small_cargo' => min(
                40,
                $planet->getObjectLevel('metal_mine') + $planet->getObjectLevel('crystal_mine')
            ),

            // Military fleet scales with economic output and aggression.
            'light_fighter' => $this->desiredLightFighters($planet, $empire),

            // Passive base defense follows aggression.
            'rocket_launcher' => (int)($empire->aggression / 4),
        ];
    }

    /**
     * Desired light fighter count: scales with hourly resource output and
     * the empire's aggression trait (1.4x at aggression 20, 2.6x at 80).
     *
     * @param PlanetService $planet
     * @param NpcEmpire $empire
     * @return int
     */
    private function desiredLightFighters(PlanetService $planet, NpcEmpire $empire): int
    {
        $hourlyOutput = (int)max(0, floor(
            $planet->getMetalProductionPerHour()
            + $planet->getCrystalProductionPerHour()
            + $planet->getDeuteriumProductionPerHour()
        ));

        $aggressionFactor = 1 + $empire->aggression / 50;

        // ~1 fighter per 600 units of hourly output, multiplied by the
        // aggression factor.
        return (int)min(400, $hourlyOutput / 600 * $aggressionFactor);
    }
}
