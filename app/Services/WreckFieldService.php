<?php

namespace OGame\Services;

use Exception;
use OGame\Models\WreckField;
use OGame\Models\Planet\Coordinate;
use OGame\Models\Planet;
use OGame\Models\Resources;
use OGame\Models\UnitCollection;
use OGame\Services\PlanetService;

/**
 * Class WreckFieldService.
 *
 * Wreck field object management service.
 *
 * @package OGame\Services
 */
class WreckFieldService
{
    /**
     * The wreck field object model.
     *
     * @var WreckField|null
     */
    private ?WreckField $wreckField = null;

    /**
     * @var PlayerService
     */
    private PlayerService $playerService;

    /**
     * @var SettingsService
     */
    private SettingsService $settingsService;

    /**
     * WreckFieldService constructor.
     *
     * @param PlayerService $playerService
     * @param SettingsService $settingsService
     */
    public function __construct(PlayerService $playerService, SettingsService $settingsService)
    {
        $this->playerService = $playerService;
        $this->settingsService = $settingsService;
    }

    /**
     * Load an existing wreck field or create a new empty one in memory for the given coordinates.
     *
     * @param Coordinate $coordinates
     */
    public function loadOrCreateForCoordinates(Coordinate $coordinates): void
    {
        $wreckField = WreckField::where('galaxy', $coordinates->galaxy)
            ->where('system', $coordinates->system)
            ->where('planet', $coordinates->position)
            ->first();

        if (!$wreckField) {
            $wreckField = new WreckField();
            $wreckField->galaxy = $coordinates->galaxy;
            $wreckField->system = $coordinates->system;
            $wreckField->planet = $coordinates->position;
            $wreckField->owner_player_id = $this->playerService->getId();
            $wreckField->created_at = now();
            $wreckField->expires_at = now()->addHours($this->settingsService->wreckFieldLifetimeHours());
            $wreckField->status = 'active';
            $wreckField->ship_data = [];
        }

        $this->wreckField = $wreckField;
    }

    /**
     * Load wreck field by coordinate only if it exists.
     *
     * @param Coordinate $coordinate
     * The coordinate of the wreck field.
     *
     * @return bool True if the wreck field exists and was loaded successfully, false otherwise.
     */
    public function loadForCoordinates(Coordinate $coordinate): bool
    {
        // Fetch wreck field model
        $wreckField = WreckField::where('galaxy', $coordinate->galaxy)
            ->where('system', $coordinate->system)
            ->where('planet', $coordinate->position)
            ->first();

        if ($wreckField !== null) {
            $this->wreckField = $wreckField;
            return true;
        }

        return false;
    }

    /**
     * Get the coordinates of the wreck field.
     */
    public function getCoordinates(): Coordinate
    {
        return new Coordinate($this->wreckField->galaxy, $this->wreckField->system, $this->wreckField->planet);
    }

    /**
     * Reloads the wreck field object from the database.
     *
     * @return void
     */
    public function reload(): void
    {
        if ($this->wreckField) {
            $this->loadForCoordinates($this->getCoordinates());
        }
    }

    /**
     * Check if wreck field conditions are met for creation.
     *
     * @param Resources $destroyedResources
     * @param UnitCollection $totalFleet
     * @param UnitCollection $destroyedShips
     * @return bool
     */
    public function canCreateWreckField(Resources $destroyedResources, UnitCollection $totalFleet, UnitCollection $destroyedShips): bool
    {
        // Check minimum resource loss (default 150,000)
        $totalDestroyedValue = $destroyedResources->metal->get() + $destroyedResources->crystal->get() + $destroyedResources->deuterium->get();
        if ($totalDestroyedValue < $this->settingsService->wreckFieldMinResourcesLoss()) {
            return false;
        }

        // Check minimum fleet percentage destroyed (default 5%)
        $totalFleetResources = $totalFleet->toResources();
        $destroyedFleetResources = $destroyedShips->toResources();
        $totalFleetValue = $totalFleetResources->metal->get() +
                          $totalFleetResources->crystal->get() +
                          $totalFleetResources->deuterium->get();
        $destroyedFleetValue = $destroyedFleetResources->metal->get() +
                             $destroyedFleetResources->crystal->get() +
                             $destroyedFleetResources->deuterium->get();

        if ($totalFleetValue === 0) {
            return false;
        }

        $destroyedPercentage = ($destroyedFleetValue / $totalFleetValue) * 100;
        if ($destroyedPercentage < $this->settingsService->wreckFieldMinFleetPercentage()) {
            return false;
        }

        return true;
    }

    /**
     * Calculate ships that go into wreck field based on settings.
     *
     * @param UnitCollection $destroyedShips
     * @return array
     */
    public function calculateShipsForWreckField(UnitCollection $destroyedShips): array
    {
        $wreckFieldPercentage = $this->settingsService->wreckFieldFromShips() / 100;
        $shipData = [];

        foreach ($destroyedShips->units as $unit) {
            if ($unit->amount > 0) {
                $wreckFieldCount = (int) floor($unit->amount * $wreckFieldPercentage);
                if ($wreckFieldCount > 0) {
                    $shipData[] = [
                        'machine_name' => $unit->unitObject->machine_name,
                        'quantity' => $wreckFieldCount,
                        'repair_progress' => 0,
                    ];
                }
            }
        }

        return $shipData;
    }

    /**
     * Create or extend a wreck field with the given ships.
     *
     * @param Coordinate $coordinate
     * @param array $shipData
     * @param int $ownerPlayerId
     * @return WreckField
     */
    public function createWreckField(Coordinate $coordinate, array $shipData, int $ownerPlayerId): WreckField
    {
        // Check if wreck field already exists
        $existingWreckField = WreckField::where('galaxy', $coordinate->galaxy)
            ->where('system', $coordinate->system)
            ->where('planet', $coordinate->position)
            ->first();

        if ($existingWreckField) {
            // Extend existing wreck field
            $this->extendWreckField($existingWreckField, $shipData);
            return $existingWreckField;
        } else {
            // Create new wreck field
            $wreckField = new WreckField();
            $wreckField->galaxy = $coordinate->galaxy;
            $wreckField->system = $coordinate->system;
            $wreckField->planet = $coordinate->position;
            $wreckField->owner_player_id = $ownerPlayerId;
            $wreckField->created_at = now();
            $wreckField->expires_at = now()->addHours($this->settingsService->wreckFieldLifetimeHours());
            $wreckField->status = 'active';
            $wreckField->ship_data = $shipData;
            $wreckField->save();

            return $wreckField;
        }
    }

    /**
     * Extend an existing wreck field with new ships.
     *
     * @param WreckField $wreckField
     * @param array $newShipData
     * @return void
     */
    public function extendWreckField(WreckField $wreckField, array $newShipData): void
    {
        $currentShipData = $wreckField->ship_data ?? [];

        // Merge new ship data with existing
        foreach ($newShipData as $newShip) {
            $found = false;
            foreach ($currentShipData as &$currentShip) {
                if ($currentShip['machine_name'] === $newShip['machine_name']) {
                    $currentShip['quantity'] += $newShip['quantity'];
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $currentShip[] = $newShip;
            }
        }

        $wreckField->ship_data = $currentShipData;

        // Extend expiration time up to the maximum
        $newExpiresAt = now()->addHours($this->settingsService->wreckFieldLifetimeHours());
        if ($newExpiresAt->greaterThan($wreckField->expires_at)) {
            $wreckField->expires_at = $newExpiresAt;
        }

        $wreckField->save();
    }

    /**
     * Start repairs for the wreck field.
     *
     * @param int $spaceDockLevel
     * @return bool
     * @throws Exception
     */
    public function startRepairs(int $spaceDockLevel): bool
    {
        if (!$this->wreckField) {
            throw new Exception('No wreck field loaded');
        }

        if (!$this->wreckField->canBeRepaired()) {
            throw new Exception('Wreck field cannot be repaired');
        }

        if ($this->wreckField->getTotalShips() === 0) {
            throw new Exception('No ships to repair');
        }

        $this->wreckField->status = 'repairing';
        $this->wreckField->repair_started_at = now();
        $this->wreckField->space_dock_level = $spaceDockLevel;

        // Calculate repair completion time (30 min to 12 hours)
        $shipCount = $this->wreckField->getTotalShips();
        $baseRepairTime = max(
            $this->settingsService->wreckFieldRepairMinMinutes() * 60, // minimum 30 minutes
            min(
                $this->settingsService->wreckFieldRepairMaxHours() * 3600, // maximum 12 hours
                $shipCount * 60 // 1 minute per ship base calculation
            )
        );

        // Adjust for space dock level (higher level = faster repair)
        $levelMultiplier = max(0.5, 1 - ($spaceDockLevel - 1) * 0.1); // 10% faster per level, minimum 50%
        $repairDuration = (int)($baseRepairTime * $levelMultiplier);

        $this->wreckField->repair_completed_at = now()->addSeconds($repairDuration);
        $this->wreckField->save();

        return true;
    }

    /**
     * Complete repairs and return the ship data.
     *
     * @return array
     * @throws Exception
     */
    public function completeRepairs(): array
    {
        if (!$this->wreckField) {
            throw new Exception('No wreck field loaded');
        }

        if ($this->wreckField->status !== 'repairing') {
            throw new Exception('No repairs in progress');
        }

        $shipData = $this->wreckField->ship_data ?? [];

        // Mark all ships as repaired
        foreach ($shipData as &$ship) {
            $ship['repair_progress'] = 100;
        }

        $this->wreckField->ship_data = $shipData;
        $this->wreckField->status = 'completed';
        $this->wreckField->save();

        return $shipData;
    }

    /**
     * Burn/destroy the wreck field.
     *
     * @return bool
     * @throws Exception
     */
    public function burnWreckField(): bool
    {
        if (!$this->wreckField) {
            throw new Exception('No wreck field loaded');
        }

        if (!$this->wreckField->canBeBurned()) {
            throw new Exception('Wreck field cannot be burned while repairs are in progress');
        }

        $this->wreckField->status = 'burned';
        $this->wreckField->save();

        return true;
    }

    /**
     * Save the wreck field to the database.
     *
     * @return void
     */
    public function save(): void
    {
        if ($this->wreckField) {
            $this->wreckField->save();
        }
    }

    /**
     * Delete the wreck field from the database.
     *
     * @return void
     */
    public function delete(): void
    {
        if ($this->wreckField && $this->wreckField->exists) {
            $this->wreckField->delete();
            $this->wreckField = null;
        }
    }

    /**
     * Get the wreck field model.
     *
     * @return WreckField|null
     */
    public function getWreckField(): ?WreckField
    {
        return $this->wreckField;
    }

    /**
     * Get ship data for the wreck field.
     *
     * @return array
     */
    public function getShipData(): array
    {
        return $this->wreckField->ship_data ?? [];
    }

    /**
     * Get the estimated repair completion time.
     *
     * @return \Illuminate\Support\Carbon|null
     */
    public function getRepairCompletionTime(): ?\Illuminate\Support\Carbon
    {
        return $this->wreckField?->repair_completed_at;
    }

    /**
     * Get remaining repair time in seconds.
     *
     * @return int
     */
    public function getRemainingRepairTime(): int
    {
        if (!$this->wreckField || !$this->wreckField->repair_completed_at) {
            return 0;
        }

        $remainingTime = $this->wreckField->repair_completed_at->timestamp - now()->timestamp;
        return max(0, (int) $remainingTime);
    }

    /**
     * Get repair progress percentage.
     *
     * @return int
     */
    public function getRepairProgress(): int
    {
        if (!$this->wreckField || $this->wreckField->status !== 'repairing') {
            return 0;
        }

        $totalTime = $this->wreckField->repair_completed_at->timestamp - $this->wreckField->repair_started_at->timestamp;
        $elapsedTime = now()->timestamp - $this->wreckField->repair_started_at->timestamp;

        return min(100, max(0, (int) (($elapsedTime / $totalTime) * 100)));
    }

    /**
     * Get wreck field data for the current planet.
     *
     * @param PlanetService $planetService
     * @return array|null
     */
    public function getWreckFieldForCurrentPlanet(PlanetService $planetService): ?array
    {
        $coordinates = $planetService->getPlanetCoordinates();

        // Debug logging
        \Log::info('WreckFieldService::getWreckFieldForCurrentPlanet', [
            'player_id' => $this->playerService->getId(),
            'coordinates' => [
                'galaxy' => $coordinates->galaxy,
                'system' => $coordinates->system,
                'position' => $coordinates->position
            ]
        ]);

        $wreckField = WreckField::where('galaxy', $coordinates->galaxy)
            ->where('system', $coordinates->system)
            ->where('planet', $coordinates->position)
            ->where('owner_player_id', $this->playerService->getId())
            ->first();

        \Log::info('WreckField query result', [
            'found' => $wreckField ? true : false,
            'wreck_field_id' => $wreckField ? $wreckField->id : null
        ]);

        if (!$wreckField) {
            return null;
        }

        // Check if wreck field is expired
        if ($wreckField->isExpired()) {
            \Log::info('WreckField expired');
            return null;
        }

        // Get ship information without unit objects for now
        $shipData = [];

        foreach ($wreckField->getShipData() as $ship) {
            $shipData[] = [
                'machine_name' => $ship['machine_name'],
                'quantity' => $ship['quantity'],
                'repair_progress' => $ship['repair_progress'] ?? 0,
                'unit_object' => null, // TODO: Implement proper unit object creation
            ];
        }

        return [
            'wreck_field' => $wreckField,
            'ship_data' => $shipData,
            'time_remaining' => $wreckField->getTimeRemaining(),
            'can_repair' => $wreckField->canBeRepaired(),
            'is_repairing' => $wreckField->isRepairing(),
            'is_completed' => $wreckField->isCompleted(),
            'repair_progress' => $wreckField->getRepairProgress(),
            'repair_completion_time' => $wreckField->getRepairCompletionTime(),
            'remaining_repair_time' => $wreckField->getRepairCompletionTime() ?
                max(0, $wreckField->getRepairCompletionTime()->timestamp - now()->timestamp) : 0,
        ];
    }
}