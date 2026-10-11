<?php

namespace OGame\Services\NpcEmpire;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use InvalidArgumentException;
use OGame\Factories\PlanetServiceFactory;
use OGame\Factories\PlayerServiceFactory;
use OGame\Models\NpcEmpire;
use OGame\Models\Planet;
use OGame\Models\Resources;
use OGame\Models\User;
use OGame\Models\UserTech;
use OGame\Services\PlanetService;
use OGame\Services\SettingsService;
use Throwable;

/**
 * Class NpcEmpireManager.
 *
 * Life cycle manager for AI-controlled NPC empires: spawning camouflaged
 * user accounts, removing them and running their scheduled processing tick.
 *
 * @package OGame\Services\NpcEmpire
 */
class NpcEmpireManager
{
    /**
     * Realistic username pool for NPC camouflage. NPC empires must look like
     * regular players to maintain competitive immersion.
     *
     * @var list<string>
     */
    private const USERNAME_POOL = [
        'Vorgath', 'Nebularis', 'KaldorPrime', 'XenonWraith', 'AstroSentinel',
        'Voidrunner', 'Nyxhunter', 'Stellarforge', 'Ironlight', 'Quantumblade',
        'Orionis', 'Zephyrion', 'Darkpulsar', 'EventHorizon', 'NovaCore',
        'VandalisPrime', 'Crimsontide', 'Solarxin', 'Drakonus', 'MekaniSan',
        'Thermalis', 'GhostOfWar', 'PhotonRaider', 'Ironclaw', 'SolarFlareX',
        'NebulaNomad', 'Vortexial', 'GravitasX', 'Lumenaris', 'EntropyLord',
        'StarforgeAC', 'RavagerPrime', 'Helscream', 'VoidHunter', 'AstroKing',
        'NebulonF', 'Zenithra', 'Corvaxis', 'DeltaVee', 'Pulseline',
        'Supernovae', 'Aurorarion', 'MeteorMist', 'Cosmotron', 'HalcyonMk',
        'Wildstar', 'FrostbyteK', 'Emberon', 'TachyonL', 'Wraithborn',
    ];

    /**
     * Realistic planet name pool for NPC camouflage.
     *
     * @var list<string>
     */
    private const PLANET_NAME_POOL = [
        'Terra', 'Valhalla', 'Midgard', 'Sankta', 'Elysium',
        'Kharak', 'Duna', 'Avalon', 'Deimos', 'Thera',
        'NovaPrime', 'Sector9', 'Kambique', 'Tarnhelm', 'Zaltys',
        'Muspelheim', 'Niflheim', 'Albion', 'Hyperion', 'Cairn',
        'Obsidian', 'Meridian', 'Sanctum', 'Verloren', 'Karst',
    ];

    /**
     * AI decision cooldown window in minutes (randomized per decision).
     *
     * @var array{min: int, max: int}
     */
    private const DECISION_COOLDOWN_MINUTES = ['min' => 5, 'max' => 15];

    public function __construct(
        private PlayerServiceFactory $playerServiceFactory,
        private PlanetServiceFactory $planetServiceFactory,
        private SettingsService $settingsService,
        private NpcDevelopmentAi $developmentAi,
        private NpcFleetAi $fleetAi,
        private NpcExpansionAi $expansionAi,
    ) {
    }

    /**
     * Spawn a new NPC empire with a camouflaged user account, randomized
     * personality traits and one initial planet.
     *
     * @param string|null $customName Optional custom username override.
     * @return NpcEmpire The newly created empire record.
     * @throws Throwable When the user, planet or empire setup fails.
     */
    public function spawn(string|null $customName = null): NpcEmpire
    {
        if ($customName !== null && User::query()->where('username', $customName)->exists()) {
            throw new InvalidArgumentException("Username '{$customName}' is already taken.");
        }

        return DB::transaction(function () use ($customName) {
            $user = new User();
            $user->username = $customName ?? $this->generateUniqueUsername();
            $user->email = 'npc.' . Str::random(10) . '@ogamex.local';
            $user->password = Hash::make(Str::random(32));
            $user->lang = 'en';
            $user->is_npc = true;
            $user->time = (string)Date::now()->timestamp;
            $user->save();

            $tech = new UserTech();
            $tech->user_id = $user->id;
            $tech->save();

            $player = $this->playerServiceFactory->make($user->id);
            $planet = $this->planetServiceFactory->createInitialPlanetForPlayer($player, $this->generatePlanetName());

            $user->planet_current = $planet->getPlanetId();
            $user->save();

            $empire = new NpcEmpire();
            $empire->user_id = $user->id;
            $empire->development_speed = $this->randomTrait(
                $this->settingsService->npcDevelopmentSpeedMin(),
                $this->settingsService->npcDevelopmentSpeedMax()
            );
            $empire->aggression = $this->randomTrait(
                $this->settingsService->npcAggressionMin(),
                $this->settingsService->npcAggressionMax()
            );
            $empire->expansion_rate = $this->randomTrait(
                $this->settingsService->npcExpansionRateMin(),
                $this->settingsService->npcExpansionRateMax()
            );
            $empire->ai_state = ['next_decision_at' => 0];
            $empire->save();

            return $empire;
        });
    }

    /**
     * Remove an NPC empire (user account + all owned data).
     *
     * Player missions that are still in flight towards a removed NPC planet
     * are safely auto-returned by the game mission logic at processing time.
     *
     * @param User $user The NPC user to remove.
     * @throws Throwable
     */
    public function delete(User $user): void
    {
        if (!$user->is_npc) {
            throw new InvalidArgumentException('User is not an NPC empire.');
        }

        DB::transaction(function () use ($user) {
            $planetIds = Planet::where('user_id', $user->id)->pluck('id')->all();

            // Fleet missions dispatched by this NPC.
            DB::table('fleet_missions')->whereIn('user_id', [$user->id])->delete();
            // Incoming player missions against NPC planets are left in place:
            // the mission handler auto-returns them when the planet is gone.

            // User level data + messages.
            DB::table('messages')->where('user_id', $user->id)->delete();
            DB::table('users_tech')->where('user_id', $user->id)->delete();

            // Planet level data (queues reference planets).
            if ($planetIds !== []) {
                DB::table('building_queues')->whereIn('planet_id', $planetIds)->delete();
                DB::table('research_queues')->whereIn('planet_id', $planetIds)->delete();
                DB::table('unit_queues')->whereIn('planet_id', $planetIds)->delete();
            }
            // Null out the current-planet pointer before the planets row,
            // it otherwise blocks deletion via FK constraint.
            DB::table('users')->where('id', $user->id)->update(['planet_current' => null]);
            DB::table('planets')->where('user_id', $user->id)->delete();

            // Grudges should not bind them: detach roles before removal.
            $user->roles()->detach();

            // npc_empires and highscore rows are FK-cascaded.
            $user->delete();
        });
    }

    /**
     * Get all NPC empires with their users, ordered by spawn order.
     *
     * @return Collection<int, NpcEmpire>
     */
    public function listEmpires(): Collection
    {
        return NpcEmpire::query()->with('user')->orderBy('user_id')->get();
    }

    /**
     * Process all NPC empires once. Called from the scheduler command.
     */
    public function processAll(): void
    {
        if (!$this->settingsService->npcEmpiresEnabled()) {
            return;
        }

        $empires = $this->listEmpires();

        foreach ($empires as $empire) {
            try {
                $this->processEmpire($empire);
            } catch (Throwable $e) {
                // One broken empire must not stall the whole batch.
                logger()->error('NPC empire processing failed', [
                    'npc_user_id' => $empire->user_id,
                    'error'       => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Run one processing tick for a single NPC empire: advance time-based
     * game state (resources, queues, missions) and let the AI take its
     * next decision if the randomized cooldown has elapsed.
     *
     * @param NpcEmpire $empire
     */
    public function processEmpire(NpcEmpire $empire): void
    {
        $player = $this->playerServiceFactory->make($empire->user_id);

        // 1. Advance research (user level) and fleet missions (arrivals,
        //    returns) exactly like the GlobalGame middleware does for
        //    human players on page load.
        $player->updateResearchQueue();
        $player->updateFleetMissions();

        // 2. Advance each planet: building queue, resources, unit queue.
        foreach ($player->planets->allPlanets() as $planet) {
            $this->updatePlanet($planet, $empire);
        }

        // 3. Simulate natural activity so the NPC does not show up as
        //    permanently inactive in the galaxy view.
        $this->touchUserActivity($empire->user);

        // 4. AI decision cycle.
        if ($this->shouldMakeDecision($empire)) {
            $this->developmentAi->decide($player);
            $this->fleetAi->decide($player, $empire);
            $this->expansionAi->decide($player, $empire);
            $empire->last_ai_decision_at = Date::now();
            $empire->save();
        }
    }

    /**
     * Check whether the randomized decision cooldown for this empire has
     * elapsed, and schedule the next cooldown when so.
     *
     * @param NpcEmpire $empire
     * @return bool
     */
    private function shouldMakeDecision(NpcEmpire $empire): bool
    {
        $now = (int)Date::now()->timestamp;
        $state = $empire->ai_state ?? [];

        if ($now < (int)($state['next_decision_at'] ?? 0)) {
            return false;
        }

        // Schedule the next decision with jitter so that NPC empires do not
        // act in machine-precise intervals.
        $state['next_decision_at'] = $now + random_int(
            self::DECISION_COOLDOWN_MINUTES['min'] * 60,
            self::DECISION_COOLDOWN_MINUTES['max'] * 60
        );

        // Reassign the whole array so the JSON cast is reliably persisted.
        $empire->ai_state = $state;

        return true;
    }

    /**
     * Advance a planet's timed state and apply the empire's development
     * speed bonus on top of the normal production.
     *
     * @param PlanetService $planet
     * @param NpcEmpire $empire
     */
    private function updatePlanet(PlanetService $planet, NpcEmpire $empire): void
    {
        $lastUpdate = (int)$planet->getUpdatedAt()->timestamp;

        // Normal timed update: building queue, resources up to storage cap,
        // unit queue, production and storage stats. Locked + idempotent.
        $planet->update();

        // Bonus production: development_speed is an extra fraction of the
        // normal production rate (e.g. 1.5 = +150% production) accumulated
        // over the elapsed time window this planet just caught up on.
        $elapsedSeconds = max(0, (int)Date::now()->timestamp - $lastUpdate);
        if ($elapsedSeconds === 0 || $empire->development_speed <= 0) {
            return;
        }

        $elapsedHours = $elapsedSeconds / 3600;
        $bonus = new Resources(
            $planet->getMetalProductionPerHour() * $elapsedHours * $empire->development_speed,
            $planet->getCrystalProductionPerHour() * $elapsedHours * $empire->development_speed,
            $planet->getDeuteriumProductionPerHour() * $elapsedHours * $empire->development_speed,
        );

        // Clip the bonus at storage capacity so storage on fast-developing
        // empires still works like regular OGame storage overflow rules.
        $bonus->metal->set(max(0.0, min(
            $bonus->metal->get(),
            $planet->metalStorage()->get() - $planet->metal()->get()
        )));
        $bonus->crystal->set(max(0.0, min(
            $bonus->crystal->get(),
            $planet->crystalStorage()->get() - $planet->crystal()->get()
        )));
        $bonus->deuterium->set(max(0.0, min(
            $bonus->deuterium->get(),
            $planet->deuteriumStorage()->get() - $planet->deuterium()->get()
        )));

        if ($bonus->metal->get() > 0 || $bonus->crystal->get() > 0 || $bonus->deuterium->get() > 0) {
            $planet->addResources($bonus);
        }
    }

    /**
     * Simulate human-like activity timestamps with jitter.
     *
     * @param User $user
     */
    private function touchUserActivity(User $user): void
    {
        // Randomized backdating within the last 10 minutes so NPC empires
        // flicker between "online" and "just left" like human players do.
        $user->time = (string)((int)Date::now()->timestamp - random_int(0, 600));
        $user->save();
    }

    /**
     * Generate a random unique username from the camouflage pool. Falls
     * back to a randomized name when the pool is exhausted.
     *
     * @return string
     */
    private function generateUniqueUsername(): string
    {
        $names = self::USERNAME_POOL;
        shuffle($names);

        foreach ($names as $name) {
            if (!User::query()->where('username', $name)->exists()) {
                return $name;
            }
        }

        // Pool exhausted: append random suffix until unique.
        do {
            $name = $names[0] . Str::upper(Str::random(4));
        } while (User::query()->where('username', $name)->exists());

        return $name;
    }

    /**
     * Generate a random planet name from the camouflage pool. Planet names
     * are not unique in-game, so a plain random pick suffices.
     *
     * @return string
     */
    private function generatePlanetName(): string
    {
        return self::PLANET_NAME_POOL[array_rand(self::PLANET_NAME_POOL)];
    }

    /**
     * Roll a random trait value within the configured global range,
     * with 0.01 resolution.
     *
     * @param float $min
     * @param float $max
     * @return float
     */
    private function randomTrait(float $min, float $max): float
    {
        $minScaled = (int)round($min * 100);
        $maxScaled = (int)round($max * 100);

        if ($maxScaled < $minScaled) {
            $maxScaled = $minScaled;
        }

        return mt_rand($minScaled, $maxScaled) / 100;
    }
}
