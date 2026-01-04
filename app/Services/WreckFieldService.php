<?php

namespace OGame\Services;

use Illuminate\Support\Carbon;
use Exception;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Planet;
use OGame\Models\Planet\Coordinate;
use OGame\Models\Resources;
use OGame\Models\WreckField;

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
     * WreckFieldService constructor.
     *
     * @param PlayerService $playerService
     * @param SettingsService $settingsService
     */
    public function __construct(private PlayerService $playerService, private SettingsService $settingsService)
    {
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
     * Load an active or blocked wreck field for the given coordinates.
     * Prefers active over blocked, and skips repairing wreck fields.
     *
     * @param Coordinate $coordinate
     * @return bool True if a non-repairing wreck field was loaded successfully, false otherwise.
     */
    public function loadActiveOrBlockedForCoordinates(Coordinate $coordinate): bool
    {
        // Fetch active or blocked wreck field model
        // Prefer active over blocked
        $wreckField = WreckField::where('galaxy', $coordinate->galaxy)
            ->where('system', $coordinate->system)
            ->where('planet', $coordinate->position)
            ->whereIn('status', ['active', 'blocked'])
            ->orderByRaw("FIELD(status, 'active', 'blocked')")
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

        if ($totalFleetValue == 0) {
            return false;
        }

        $destroyedPercentage = ($destroyedFleetValue / $totalFleetValue) * 100;
        if ($destroyedPercentage < (float) $this->settingsService->wreckFieldMinFleetPercentage()) {
            return false;
        }

        return true;
    }

    /**
     * Calculate ships that go into wreck field based on debris field settings.
     * Wreck field percentage = 100% - debris_field_percentage
     *
     * @param UnitCollection $destroyedShips
     * @return array
     */
    public function calculateShipsForWreckField(UnitCollection $destroyedShips): array
    {
        $wreckFieldPercentage = (100.0 - $this->settingsService->debrisFieldFromShips()) / 100;
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
     * Behavior:
     * - If no existing wreck field: create new one
     * - If existing wreck field is active (repairs not started): combine ships and reset expiration timer
     * - If existing wreck field is repairing or blocked: create a separate blocked wreck field
     *
     * @param Coordinate $coordinate
     * @param array $shipData
     * @param int $ownerPlayerId
     * @return WreckField
     */
    public function createWreckField(Coordinate $coordinate, array $shipData, int $ownerPlayerId): WreckField
    {
        // Check if wreck field already exists at this location
        $existingWreckField = WreckField::where('galaxy', $coordinate->galaxy)
            ->where('system', $coordinate->system)
            ->where('planet', $coordinate->position)
            ->where('owner_player_id', $ownerPlayerId)
            ->whereIn('status', ['active', 'repairing', 'blocked'])
            ->first();

        if ($existingWreckField) {
            if ($existingWreckField->status === 'active') {
                // Repairs haven't started - combine and reset expiration timer
                $this->extendWreckFieldWithReset($existingWreckField, $shipData);
                return $existingWreckField;
            } else {
                // Repairs in progress or already blocked - create a separate blocked wreck field
                return $this->createBlockedWreckField($coordinate, $shipData, $ownerPlayerId);
            }
        } else {
            // Create new active wreck field
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
                $currentShipData[] = $newShip;
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
     * Extend an existing wreck field with new ships and reset expiration timer.
     * Used when a new wreck field is created at the same location and repairs haven't started.
     *
     * @param WreckField $wreckField
     * @param array $newShipData
     * @return void
     */
    public function extendWreckFieldWithReset(WreckField $wreckField, array $newShipData): void
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
                $currentShipData[] = $newShip;
            }
        }

        $wreckField->ship_data = $currentShipData;

        // Reset expiration timer to full duration
        $wreckField->expires_at = now()->addHours($this->settingsService->wreckFieldLifetimeHours());
        $wreckField->created_at = now();

        $wreckField->save();
    }

    /**
     * Add ships to an ongoing repair job.
     * Used when a new wreck field is created at the same location while repairs are in progress.
     * The new ships are automatically added to the ongoing repairs without changing the repair completion time.
     *
     * IMPORTANT: Ships added during ongoing repairs are marked as 'late_added' and CANNOT be collected
     * via the "put partially finished repair back into service" button. Players must wait until ALL
     * ships (including late-added ones) are automatically put back into service.
     *
     * @param WreckField $wreckField
     * @param array $newShipData
     * @return void
     */
    public function addShipsToOngoingRepairs(WreckField $wreckField, array $newShipData): void
    {
        $currentShipData = $wreckField->ship_data ?? [];

        // Merge new ship data with existing
        foreach ($newShipData as $newShip) {
            $found = false;
            foreach ($currentShipData as &$currentShip) {
                if ($currentShip['machine_name'] === $newShip['machine_name']) {
                    $currentShip['quantity'] += $newShip['quantity'];
                    // Mark ships that are added to ongoing repairs as non-collectable
                    $currentShip['late_added'] = true;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                // Mark new ships as late_added
                $newShip['late_added'] = true;
                $currentShipData[] = $newShip;
            }
        }

        $wreckField->ship_data = $currentShipData;

        // Don't modify repair times - new ships are added to ongoing repairs
        // The new ships will be repaired at the same time as the existing ones

        $wreckField->save();
    }

    /**
     * Create a blocked wreck field when another wreck field is already being repaired.
     * The blocked wreck field can only be dismissed or start repairs when the first one completes.
     *
     * @param Coordinate $coordinate
     * @param array $shipData
     * @param int $ownerPlayerId
     * @return WreckField
     */
    public function createBlockedWreckField(Coordinate $coordinate, array $shipData, int $ownerPlayerId): WreckField
    {
        $wreckField = new WreckField();
        $wreckField->galaxy = $coordinate->galaxy;
        $wreckField->system = $coordinate->system;
        $wreckField->planet = $coordinate->position;
        $wreckField->owner_player_id = $ownerPlayerId;
        $wreckField->created_at = now();
        $wreckField->expires_at = now()->addHours($this->settingsService->wreckFieldLifetimeHours());
        $wreckField->status = 'blocked';
        $wreckField->ship_data = $shipData;
        $wreckField->save();

        return $wreckField;
    }

    /**
     * Check if there's a wreck field currently being repaired at the given coordinates.
     *
     * @param Coordinate $coordinate
     * @param int $ownerPlayerId
     * @param int|null $excludeWreckFieldId
     * @return bool
     */
    public function hasRepairingWreckFieldAt(Coordinate $coordinate, int $ownerPlayerId, int|null $excludeWreckFieldId = null): bool
    {
        $query = WreckField::where('galaxy', $coordinate->galaxy)
            ->where('system', $coordinate->system)
            ->where('planet', $coordinate->position)
            ->where('owner_player_id', $ownerPlayerId)
            ->where('status', 'repairing');

        if ($excludeWreckFieldId !== null) {
            $query->where('id', '!=', $excludeWreckFieldId);
        }

        return $query->exists();
    }

    /**
     * Unblock the next wreck field at the given coordinates (if any).
     * Called when a wreck field completes repairs or is burned.
     *
     * @param Coordinate $coordinate
     * @param int $ownerPlayerId
     * @return void
     */
    public function unblockNextWreckField(Coordinate $coordinate, int $ownerPlayerId): void
    {
        // Find the oldest blocked wreck field and change it to active
        $blockedWreckField = WreckField::where('galaxy', $coordinate->galaxy)
            ->where('system', $coordinate->system)
            ->where('planet', $coordinate->position)
            ->where('owner_player_id', $ownerPlayerId)
            ->where('status', 'blocked')
            ->orderBy('created_at', 'asc')
            ->first();

        if ($blockedWreckField) {
            $blockedWreckField->status = 'active';
            $blockedWreckField->save();
        }
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

        // Check if there's already a wreck field being repaired at this location
        $coordinates = $this->getCoordinates();
        if ($this->hasRepairingWreckFieldAt($coordinates, $this->wreckField->owner_player_id, $this->wreckField->id)) {
            throw new Exception('Another wreck field is already being repaired at this location');
        }

        $this->wreckField->status = 'repairing';
        $this->wreckField->repair_started_at = now();
        $this->wreckField->space_dock_level = $spaceDockLevel;

        // Calculate repair completion time
        // Formula based on ship count only (space dock level affects recovery %, not time)
        // Uses sqrt(shipCount * 30) * 10 to calculate reasonable repair times
        $shipCount = $this->wreckField->getTotalShips();
        $sqrtValue = sqrt($shipCount * 30);
        $repairDuration = (int)($sqrtValue * 10);

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

        // Unblock the next wreck field at this location
        $this->unblockNextWreckField($this->getCoordinates(), $this->wreckField->owner_player_id);

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

        // Unblock the next wreck field at this location
        $this->unblockNextWreckField($this->getCoordinates(), $this->wreckField->owner_player_id);

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
    public function getWreckField(): WreckField|null
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
     * @return Carbon|null
     */
    public function getRepairCompletionTime(): Carbon|null
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

        $remainingTime = (int) $this->wreckField->repair_completed_at->timestamp - (int) now()->timestamp;
        return max(0, (int) $remainingTime);
    }

    /**
     * Get repair progress percentage.
     *
     * @return int Repair progress percentage (0-100), capped by Space Dock level
     */
    public function getRepairProgress(): int
    {
        if (!$this->wreckField) {
            return 0;
        }

        if ($this->wreckField->status === 'completed') {
            return 100;
        }

        if ($this->wreckField->status !== 'repairing') {
            return 0;
        }

        $totalTime = (int) $this->wreckField->repair_completed_at->timestamp - (int) $this->wreckField->repair_started_at->timestamp;
        $elapsedTime = (int) now()->timestamp - (int) $this->wreckField->repair_started_at->timestamp;

        $timeBasedProgress = min(100, max(0, (int) (($elapsedTime / $totalTime) * 100)));

        $levelCap = $this->getMaxRecoverablePercentage();
        $cappedProgress = min($timeBasedProgress, $levelCap);

        return (int) $cappedProgress;
    }

    /**
     * Get maximum recoverable percentage based on debris field setting and Space Dock level.
     *
     * Base recoverable = 100% - debris_field_percentage
     * Space Dock level provides a bonus multiplier on top of the base.
     * Formula: base_recoverable * (1 + (space_dock_level - 1) * bonus_per_level)
     *
     * @return float Maximum recoverable percentage
     */
    public function getMaxRecoverablePercentage(): float
    {
        $debrisFieldPercentage = $this->settingsService->debrisFieldFromShips();
        $baseRecoverable = 100.0 - $debrisFieldPercentage;
        $spaceDockLevel = $this->wreckField->space_dock_level ?? 1;

        // Cap space dock level at 15
        if ($spaceDockLevel > 15) {
            $spaceDockLevel = 15;
        }

        // Space Dock bonus: 2.5% per level
        $spaceDockBonus = 1.0 + (($spaceDockLevel - 1) * 0.025);

        $maxRecoverable = $baseRecoverable * $spaceDockBonus;

        // Cap at 100%
        return min(100.0, $maxRecoverable);
    }

    /**
     * Get wreck field data for the current planet.
     * Returns the "primary" wreck field (active or repairing) for backward compatibility.
     *
     * @param PlanetService $planetService
     * @return array|null
     */
    public function getWreckFieldForCurrentPlanet(PlanetService $planetService): array|null
    {
        $wreckFields = $this->getAllWreckFieldsForCurrentPlanet($planetService);

        if (empty($wreckFields)) {
            return null;
        }

        // Return the first (primary) wreck field for backward compatibility
        return $wreckFields[0];
    }

    /**
     * Get all wreck fields for the current planet (including blocked ones).
     * Returns an array of wreck field data arrays, ordered by priority:
     * 1. Active or repairing wreck fields (primary)
     * 2. Blocked wreck fields (queued)
     * 3. Completed wreck fields (for collection)
     *
     * @param PlanetService $planetService
     * @return array
     */
    public function getAllWreckFieldsForCurrentPlanet(PlanetService $planetService): array
    {
        $coordinates = $planetService->getPlanetCoordinates();

        $wreckFields = WreckField::where('galaxy', $coordinates->galaxy)
            ->where('system', $coordinates->system)
            ->where('planet', $coordinates->position)
            ->where('owner_player_id', $this->playerService->getId())
            ->whereIn('status', ['active', 'repairing', 'blocked', 'completed'])
            ->orderByRaw("FIELD(status, 'repairing', 'active', 'blocked', 'completed')")
            ->orderBy('created_at', 'asc')
            ->get();

        $result = [];

        foreach ($wreckFields as $wreckField) {
            if ($wreckField->isExpired()) {
                continue;
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

            $timeRemaining = $wreckField->getTimeRemaining();

            // Calculate total repair time (in seconds)
            $totalRepairTime = 0;
            if ($wreckField->repair_started_at && $wreckField->getRepairCompletionTime()) {
                $totalRepairTime = (int) $wreckField->getRepairCompletionTime()->timestamp - (int) $wreckField->repair_started_at->timestamp;
            }

            // Temporarily load this wreck field to get its max recoverable percentage
            $previousWreckField = $this->wreckField;
            $this->wreckField = $wreckField;
            $maxRecoverablePercentage = $this->getMaxRecoverablePercentage();
            $this->wreckField = $previousWreckField;

            $result[] = [
                'wreck_field' => $wreckField,
                'ship_data' => $shipData,
                'time_remaining' => $timeRemaining,
                'can_repair' => $wreckField->canBeRepaired(),
                'is_repairing' => $wreckField->isRepairing(),
                'is_blocked' => $wreckField->isBlocked(),
                'is_completed' => $wreckField->isCompleted(),
                'repair_progress' => $wreckField->getRepairProgress(),
                'max_recoverable_percentage' => $maxRecoverablePercentage,
                'space_dock_level' => $wreckField->space_dock_level ?? 1,
                'repair_completion_time' => $wreckField->getRepairCompletionTime(),
                'repair_started_at' => $wreckField->repair_started_at,
                'total_repair_time' => $totalRepairTime,
                'remaining_repair_time' => $wreckField->getRepairCompletionTime() ?
                    max(0, (int) $wreckField->getRepairCompletionTime()->timestamp - (int) now()->timestamp) : 0,
            ];
        }

        return $result;
    }
}
