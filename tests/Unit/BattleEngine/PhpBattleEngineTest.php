<?php

namespace Tests\Unit\BattleEngine;

use OGame\GameMissions\BattleEngine\BattleEngine;
use OGame\GameMissions\BattleEngine\Models\BattleResultRound;
use OGame\GameMissions\BattleEngine\Models\BattleUnit;
use OGame\GameMissions\BattleEngine\Models\DefenderFleet;
use OGame\GameMissions\BattleEngine\PhpBattleEngine;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Services\ObjectService;
use ReflectionMethod;

/**
 * Test class for the PHP BattleEngine. The actual tests that create the simulated battles
 * are defined in the abstract test class.
 */
class PhpBattleEngineTest extends BattleEngineTestAbstract
{
    /**
     * Create a new BattleEngine instance. This allows the test class itself to change the BattleEngine
     * that is used for the actual tests defined in the abstract test class.
     *
     * @inheritdoc
     */
    protected function createBattleEngineForAttackers(array $attackers): BattleEngine
    {
        // Create defenders array with planet's stationary forces
        $defenders = [DefenderFleet::fromPlanet($this->planetService)];

        return new PhpBattleEngine(
            $attackers,
            $this->planetService,
            $defenders,
            $this->settingsService
        );
    }

    /**
     * Test that a bounced shot never triggers the hull explosion roll of a damaged unit.
     *
     * This is tested on the individual shot instead of on a full battle: a unit only has a
     * damaged hull once its shield has been stripped, so within a battle the combination of
     * "damaged hull, intact shield and incoming bounced shots" only occurs after the shield
     * regenerated in a later round, which cannot be set up deterministically.
     */
    public function testBouncedShotNeverTriggersHullExplosionRoll(): void
    {
        $lightFighter = ObjectService::getUnitObjectByMachineName('light_fighter');
        $shieldDome = ObjectService::getUnitObjectByMachineName('large_shield_dome');

        // The light fighter deals 50 damage, which is below 1% of the dome's 10.000 max shield
        // points, so all of its shots bounce off the intact shield.
        $attacker = new BattleUnit($lightFighter, 4000, 10, 50, 0, 0);
        $defender = new BattleUnit($shieldDome, 100000, 10000, 1, 0, 0);

        // Damage the hull of the dome to 10% of its original value. Every explosion roll on it
        // would succeed with a 90% chance, so 300 bounced shots would destroy it with certainty
        // if bounced shots were allowed to trigger the roll.
        $defender->currentHullPlating = $defender->originalHullPlating * 0.1;

        $round = new BattleResultRound();
        $this->fireShots(300, $round, $attacker, $defender);

        // The dome is untouched: no hull damage, no depleted shield and no explosion.
        $this->assertEquals(1000, $defender->currentHullPlating);
        $this->assertEquals(10000, $defender->currentShieldPoints);

        // The bounced shots do count towards the round statistics.
        $this->assertEquals(300, $round->hitsAttacker);
        $this->assertEquals(300 * 50, $round->fullStrengthAttacker);
    }

    /**
     * Test that a shot which is not bounced does trigger the hull explosion roll of a damaged
     * unit. This guards the counterpart of testBouncedShotNeverTriggersHullExplosionRoll: the
     * explosion roll is skipped for bounced shots only, not for weak shots in general.
     */
    public function testShotOnStrippedShieldStillTriggersHullExplosionRoll(): void
    {
        $lightFighter = ObjectService::getUnitObjectByMachineName('light_fighter');
        $shieldDome = ObjectService::getUnitObjectByMachineName('large_shield_dome');

        $attacker = new BattleUnit($lightFighter, 4000, 10, 50, 0, 0);
        $defender = new BattleUnit($shieldDome, 100000, 10000, 1, 0, 0);

        // Same damaged hull as above, but with the shield already stripped. The shots now hit
        // the hull in full and are therefore not bounced, so they do roll for an explosion.
        $defender->currentHullPlating = $defender->originalHullPlating * 0.1;
        $defender->currentShieldPoints = 0;

        $round = new BattleResultRound();
        $this->fireShots(10, $round, $attacker, $defender);

        // The dome must be destroyed: 10 shots x 50 damage only remove half of the remaining
        // 1.000 hull plating, so only a successful explosion roll (~90% chance per shot) can
        // have destroyed it.
        $this->assertEquals(0, $defender->currentHullPlating);
    }

    /**
     * Let an attacking unit fire the given amount of shots at a defending unit.
     *
     * The engine has no public entry point for a single shot, so the private method is called
     * through reflection to be able to test shots in isolation.
     */
    private function fireShots(int $amount, BattleResultRound $round, BattleUnit $attacker, BattleUnit $defender): void
    {
        $engine = $this->createBattleEngine(new UnitCollection());
        $attackUnit = new ReflectionMethod(PhpBattleEngine::class, 'attackUnit');

        for ($i = 0; $i < $amount; $i++) {
            $attackUnit->invoke($engine, true, $round, $attacker, $defender);
        }
    }
}
