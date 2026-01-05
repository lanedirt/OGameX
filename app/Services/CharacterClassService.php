<?php

namespace OGame\Services;

use OGame\Models\Planet;
use Exception;
use OGame\Enums\CharacterClass;
use OGame\Enums\DarkMatterTransactionType;
use OGame\Models\FleetMission;
use OGame\Models\User;

/**
 * Class CharacterClassService.
 *
 * Primary service for all Character Class operations.
 *
 * @package OGame\Services
 */
class CharacterClassService
{
    /**
     * CharacterClassService constructor.
     *
     * @param DarkMatterService $darkMatterService
     * @param SettingsService $settingsService
     */
    public function __construct(
        private DarkMatterService $darkMatterService,
        private SettingsService $settingsService
    ) {
    }

    /**
     * Check if user has a character class selected.
     *
     * @param User $user
     * @return bool
     */
    public function hasCharacterClass(User $user): bool
    {
        return $user->character_class !== null;
    }

    /**
     * Get the user's character class.
     *
     * @param User $user
     * @return CharacterClass|null
     */
    public function getCharacterClass(User $user): CharacterClass|null
    {
        if ($user->character_class === null) {
            return null;
        }

        return CharacterClass::tryFrom($user->character_class);
    }

    /**
     * Check if user is a Collector.
     *
     * @param User $user
     * @return bool
     */
    public function isCollector(User $user): bool
    {
        return $user->character_class === CharacterClass::COLLECTOR->value;
    }

    /**
     * Check if user is a General.
     *
     * @param User $user
     * @return bool
     */
    public function isGeneral(User $user): bool
    {
        return $user->character_class === CharacterClass::GENERAL->value;
    }

    /**
     * Check if user is a Discoverer.
     *
     * @param User $user
     * @return bool
     */
    public function isDiscoverer(User $user): bool
    {
        return $user->character_class === CharacterClass::DISCOVERER->value;
    }

    /**
     * Get the cost to change/select a character class.
     *
     * @param User $user
     * @return int Dark Matter cost (0 if free selection available)
     */
    public function getChangeCost(User $user): int
    {
        // TODO: Refactor this to use a dark matter add/subtract feature in developer shortcuts instead of bypassing the cost flow
        // This would reduce complexity by removing the special case handling for dev_free_class_changes
        // Check developer setting for free class changes
        $freeClassChanges = $this->settingsService->get('dev_free_class_changes', '0');
        if ($freeClassChanges !== '0') {
            return 0;
        }

        // First selection is free
        if (!$user->character_class_free_used) {
            return 0;
        }

        return 500000;
    }

    /**
     * Check if user can afford to change class.
     *
     * @param User $user
     * @param CharacterClass $newClass
     * @return bool
     */
    public function canChangeClass(User $user, CharacterClass $newClass): bool
    {
        $cost = $this->getChangeCost($user);

        if ($cost === 0) {
            return true;
        }

        return $this->darkMatterService->canAfford($user, $cost);
    }

    /**
     * Select or change character class.
     *
     * @param User $user
     * @param CharacterClass $newClass
     * @return void
     * @throws Exception
     */
    public function selectClass(User $user, CharacterClass $newClass): void
    {
        $cost = $this->getChangeCost($user);

        // Check if user can afford the change
        if ($cost > 0 && !$this->darkMatterService->canAfford($user, $cost)) {
            throw new Exception('Not enough Dark Matter to change class');
        }

        // Check if trying to select the same class
        if ($user->character_class === $newClass->value) {
            throw new Exception('This class is already selected');
        }

        // Check if user has active fleet missions
        if ($this->hasActiveFleetMissions($user)) {
            throw new Exception('Cannot change character class while fleet missions are active. Please wait for all fleets to return.');
        }

        // Deduct Dark Matter if not free
        if ($cost > 0) {
            $this->darkMatterService->debit(
                $user,
                $cost,
                DarkMatterTransactionType::PLAYER_CLASS->value,
                'Changed character class to ' . $newClass->getName()
            );
        }

        // Update user's character class
        $user->character_class = $newClass->value;
        $user->character_class_free_used = true;
        $user->character_class_changed_at = now();
        $user->save();

        // Reset crawler overload if switching away from Collector
        // Non-Collector classes can only use up to 100% (value 10)
        if ($newClass !== CharacterClass::COLLECTOR) {
            $this->resetCrawlerOverload($user);
        }
    }

    /**
     * Deselect/deactivate character class.
     *
     * @param User $user
     * @return void
     * @throws Exception
     */
    public function deselectClass(User $user): void
    {
        if ($user->character_class === null) {
            throw new Exception('No character class selected');
        }

        // Check if user has active fleet missions
        if ($this->hasActiveFleetMissions($user)) {
            throw new Exception('Cannot deactivate character class while fleet missions are active. Please wait for all fleets to return.');
        }

        $user->character_class = null;
        $user->character_class_changed_at = now();
        $user->save();

        // Reset crawler overload when deactivating class
        $this->resetCrawlerOverload($user);
    }

    /**
     * Check if user has any active (unprocessed) fleet missions.
     *
     * @param User $user
     * @return bool
     */
    private function hasActiveFleetMissions(User $user): bool
    {
        return FleetMission::where('user_id', $user->id)
            ->where('processed', 0)
            ->exists();
    }

    /**
     * Get mine production bonus (Collector only).
     * Returns multiplier (e.g., 1.25 for +25%).
     *
     * @param User $user
     * @return float
     */
    public function getMineProductionBonus(User $user): float
    {
        if ($this->isCollector($user)) {
            return 1.25; // +25%
        }

        return 1.0;
    }

    /**
     * Get energy production bonus (Collector only).
     * Returns multiplier (e.g., 1.10 for +10%).
     *
     * @param User $user
     * @return float
     */
    public function getEnergyProductionBonus(User $user): float
    {
        if ($this->isCollector($user)) {
            return 1.10; // +10%
        }

        return 1.0;
    }

    /**
     * Get transporter speed bonus (Collector only).
     * Returns multiplier (e.g., 2.0 for +100%).
     *
     * @param User $user
     * @return float
     */
    public function getTransporterSpeedBonus(User $user): float
    {
        if ($this->isCollector($user)) {
            return 2.0; // +100%
        }

        return 1.0;
    }

    /**
     * Get transporter cargo bonus (Collector only).
     * Returns multiplier (e.g., 1.25 for +25%).
     *
     * @param User $user
     * @return float
     */
    public function getTransporterCargoBonus(User $user): float
    {
        if ($this->isCollector($user)) {
            return 1.25; // +25%
        }

        return 1.0;
    }

    /**
     * Get crawler bonus multiplier (Collector only).
     * Returns multiplier (e.g., 1.5 for +50%).
     *
     * @param User $user
     * @return float
     */
    public function getCrawlerBonusMultiplier(User $user): float
    {
        if ($this->isCollector($user)) {
            return 1.5; // +50%
        }

        return 1.0;
    }

    /**
     * Get maximum crawler overload percentage (Collector only).
     * Returns max percentage (e.g., 150 for 150%).
     *
     * @param User $user
     * @return int
     */
    public function getMaxCrawlerOverload(User $user): int
    {
        if ($this->isCollector($user)) {
            return 150; // Can overload up to 150%
        }

        return 100; // Normal max is 100%
    }

    /**
     * Reset crawler overload on all user's planets to max 100%.
     * Called when switching away from Collector class.
     *
     * @param User $user
     * @return void
     */
    private function resetCrawlerOverload(User $user): void
    {
        // Get all planets for this user using direct query
        $planets = Planet::where('user_id', $user->id)->get();

        foreach ($planets as $planet) {
            // If crawler percentage is above 10 (100%), reset to 10
            if ($planet->crawler_percent > 10) {
                $planet->crawler_percent = 10;
                $planet->save();
            }
        }
    }

    /**
     * Get combat ship speed bonus (General only).
     * Returns multiplier (e.g., 2.0 for +100%).
     *
     * @param User $user
     * @return float
     */
    public function getCombatShipSpeedBonus(User $user): float
    {
        if ($this->isGeneral($user)) {
            return 2.0; // +100%
        }

        return 1.0;
    }

    /**
     * Get recycler speed bonus (General only).
     * Returns multiplier (e.g., 2.0 for +100%).
     *
     * @param User $user
     * @return float
     */
    public function getRecyclerSpeedBonus(User $user): float
    {
        if ($this->isGeneral($user)) {
            return 2.0; // +100%
        }

        return 1.0;
    }

    /**
     * Get deuterium consumption reduction (General only).
     * Returns multiplier (e.g., 0.5 for -50%).
     *
     * @param User $user
     * @return float
     */
    public function getDeuteriumConsumptionMultiplier(User $user): float
    {
        if ($this->isGeneral($user)) {
            return 0.5; // -50% consumption
        }

        return 1.0;
    }

    /**
     * Get recycler and pathfinder cargo bonus (General only).
     * Returns multiplier (e.g., 1.20 for +20%).
     *
     * @param User $user
     * @return float
     */
    public function getRecyclerPathfinderCargoBonus(User $user): float
    {
        if ($this->isGeneral($user)) {
            return 1.20; // +20%
        }

        return 1.0;
    }

    /**
     * Get additional combat research levels (General only).
     *
     * @param User $user
     * @return int
     */
    public function getAdditionalCombatResearchLevels(User $user): int
    {
        if ($this->isGeneral($user)) {
            return 2;
        }

        return 0;
    }

    /**
     * Get additional fleet slots (General only).
     *
     * @param User $user
     * @return int
     */
    public function getAdditionalFleetSlots(User $user): int
    {
        if ($this->isGeneral($user)) {
            return 2;
        }

        return 0;
    }

    /**
     * Get additional moon fields (General only).
     *
     * @param User $user
     * @return int
     */
    public function getAdditionalMoonFields(User $user): int
    {
        if ($this->isGeneral($user)) {
            return 5;
        }

        return 0;
    }

    /**
     * Check if user has detailed fleet speed settings (General only).
     *
     * @param User $user
     * @return bool
     */
    public function hasDetailedFleetSpeedSettings(User $user): bool
    {
        return $this->isGeneral($user);
    }

    /**
     * Get Reaper auto debris collection percentage.
     * Returns the percentage of debris that Reaper ships automatically collect
     * directly after an attack mission.
     *
     * Note: While only General class can BUILD Reapers, the debris collection ability
     * works for all classes if they have Reapers in their fleet.
     *
     * @param User $user
     * @return float Percentage as decimal (e.g., 0.30 for 30%)
     */
    public function getReaperDebrisCollectionPercentage(User $user): float
    {
        // Reaper debris collection works for all classes (30% of debris)
        // Only building the Reaper is exclusive to General class
        return 0.30;
    }

    /**
     * Get research time multiplier (Discoverer only).
     * Returns multiplier (e.g., 0.75 for -25%).
     *
     * @param User $user
     * @return float
     */
    public function getResearchTimeMultiplier(User $user): float
    {
        if ($this->isDiscoverer($user)) {
            return 0.75; // -25% research time
        }

        return 1.0;
    }

    /**
     * Get expedition resource multiplier (Discoverer only).
     * Returns multiplier to apply to expedition gains.
     *
     * @param User $user
     * @param float $universeEconomicSpeed
     * @return float
     */
    public function getExpeditionResourceMultiplier(User $user, float $universeEconomicSpeed): float
    {
        if ($this->isDiscoverer($user)) {
            return 1.5 * $universeEconomicSpeed;
        }

        return 1.0;
    }

    /**
     * Get planet size bonus on colonization (Discoverer only).
     * Returns multiplier (e.g., 1.10 for +10% = 21% more fields).
     *
     * @param User $user
     * @return float
     */
    public function getPlanetSizeBonus(User $user): float
    {
        if ($this->isDiscoverer($user)) {
            return 1.10; // +10% planet size
        }

        return 1.0;
    }

    /**
     * Get additional expeditions (Discoverer only).
     *
     * @param User $user
     * @return int
     */
    public function getAdditionalExpeditions(User $user): int
    {
        if ($this->isDiscoverer($user)) {
            return 2;
        }

        return 0;
    }

    /**
     * Get expedition enemy chance multiplier (Discoverer only).
     * Returns multiplier (e.g., 0.5 for -50%).
     *
     * @param User $user
     * @return float
     */
    public function getExpeditionEnemyChanceMultiplier(User $user): float
    {
        if ($this->isDiscoverer($user)) {
            return 0.5; // -50% chance
        }

        return 1.0;
    }

    /**
     * Get phalanx range bonus (Discoverer only).
     * Returns multiplier (e.g., 1.20 for +20%).
     *
     * @param User $user
     * @return float
     */
    public function getPhalanxRangeBonus(User $user): float
    {
        if ($this->isDiscoverer($user)) {
            return 1.20; // +20%
        }

        return 1.0;
    }

    /**
     * Get expedition slots bonus (Discoverer only).
     * Returns the number of additional expedition slots.
     *
     * @param User $user
     * @return int
     */
    public function getExpeditionSlotsBonus(User $user): int
    {
        if ($this->isDiscoverer($user)) {
            return 2; // +2 expedition slots
        }

        return 0;
    }

    /**
     * Get inactive player loot percentage (Discoverer only).
     * Returns percentage (e.g., 0.75 for 75%).
     *
     * @param User $user
     * @return float
     */
    public function getInactiveLootPercentage(User $user): float
    {
        if ($this->isDiscoverer($user)) {
            return 0.75; // 75% loot
        }

        return 0.5; // Default 50%
    }

    /**
     * Get speedup discount percentage for a specific type.
     *
     * @param User $user
     * @param string $type 'building', 'research', or 'shipyard'
     * @return float Discount multiplier (e.g., 0.9 for 10% discount)
     */
    public function getSpeedupDiscount(User $user, string $type): float
    {
        return match ($type) {
            'building' => $this->isCollector($user) ? 0.9 : 1.0,
            'research' => $this->isDiscoverer($user) ? 0.9 : 1.0,
            'shipyard' => $this->isGeneral($user) ? 0.9 : 1.0,
            default => 1.0,
        };
    }

    /**
     * Check if expedition debris fields are visible (Discoverer only).
     *
     * @param User $user
     * @return bool
     */
    public function hasExpeditionDebrisFieldsVisible(User $user): bool
    {
        return $this->isDiscoverer($user);
    }
}
