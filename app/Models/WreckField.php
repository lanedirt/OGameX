<?php

namespace OGame\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Wreck Field Model
 *
 * @property int $id
 * @property int $galaxy
 * @property int $system
 * @property int $planet
 * @property int $owner_player_id
 * @property Carbon $created_at
 * @property Carbon $expires_at
 * @property Carbon|null $repair_started_at
 * @property Carbon|null $repair_completed_at
 * @property int|null $space_dock_level
 * @property string $status
 * @property array|null $ship_data
 * @property-read User $owner
 *
 * @method static Builder|WreckField newModelQuery()
 * @method static Builder|WreckField newQuery()
 * @method static Builder|WreckField query()
 * @method static Builder|WreckField whereId($value)
 * @method static Builder|WreckField whereGalaxy($value)
 * @method static Builder|WreckField whereSystem($value)
 * @method static Builder|WreckField wherePlanet($value)
 * @method static Builder|WreckField whereOwnerPlayerId($value)
 * @method static Builder|WreckField whereCreatedAt($value)
 * @method static Builder|WreckField whereExpiresAt($value)
 * @method static Builder|WreckField whereRepairStartedAt($value)
 * @method static Builder|WreckField whereRepairCompletedAt($value)
 * @method static Builder|WreckField whereSpaceDockLevel($value)
 * @method static Builder|WreckField whereStatus($value)
 * @method static Builder|WreckField whereShipData($value)
 * @mixin \Eloquent
 */
class WreckField extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'galaxy',
        'system',
        'planet',
        'owner_player_id',
        'created_at',
        'expires_at',
        'repair_started_at',
        'repair_completed_at',
        'space_dock_level',
        'status',
        'ship_data',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'expires_at' => 'datetime',
        'repair_started_at' => 'datetime',
        'repair_completed_at' => 'datetime',
        'ship_data' => 'array',
    ];

    /**
     * Get the owner of the wreck field.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_player_id');
    }

    /**
     * Check if the wreck field is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if repairs are currently in progress.
     */
    public function isRepairing(): bool
    {
        return $this->status === 'repairing';
    }

    /**
     * Check if repairs are completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the wreck field can be repaired (not expired and not already repairing).
     */
    public function canBeRepaired(): bool
    {
        return ($this->status === 'active' || $this->status === 'blocked') && !$this->isExpired();
    }

    /**
     * Check if the wreck field is blocked (waiting for another wreck field to complete).
     */
    public function isBlocked(): bool
    {
        return $this->status === 'blocked';
    }

    /**
     * Check if the wreck field can be burned (not currently repairing and not blocked).
     */
    public function canBeBurned(): bool
    {
        return $this->status !== 'repairing' && $this->status !== 'blocked';
    }

    /**
     * Check if the wreck field is burned.
     */
    public function isBurned(): bool
    {
        return $this->status === 'burned';
    }

    /**
     * Get the time remaining until the wreck field expires.
     */
    public function getTimeRemaining(): int
    {
        $now = now();
        $expires = $this->expires_at;

        $timeRemaining = max(0, (int) $now->diffInSeconds($expires));

        return $timeRemaining;
    }

    /**
     * Get the repair duration in seconds (30 minutes to 12 hours).
     */
    public function getRepairDuration(): int
    {
        // Default to minimum 30 minutes, maximum 12 hours
        return 30 * 60; // 30 minutes minimum
    }

    /**
     * Get the total number of ships in the wreck field.
     */
    public function getTotalShips(): int
    {
        if (!$this->ship_data) {
            return 0;
        }

        return array_sum(array_column($this->ship_data, 'quantity'));
    }

    /**
     * Get the ship data array for the wreck field.
     */
    public function getShipData(): array
    {
        return $this->ship_data ?? [];
    }

    /**
     * Get the repair completion time (null if not repairing).
     */
    public function getRepairCompletionTime(): Carbon|null
    {
        return $this->repair_completed_at;
    }

    /**
     * Get the repair progress percentage (0-100).
     */
    public function getRepairProgress(): int
    {
        // Completed wreck fields have 100% progress
        if ($this->status === 'completed') {
            return 100;
        }

        // Active or burned wreck fields with no repairs have 0% progress
        if ($this->status !== 'repairing' || !$this->repair_started_at || !$this->repair_completed_at) {
            return 0;
        }

        $totalTime = (int) $this->repair_completed_at->timestamp - (int) $this->repair_started_at->timestamp;
        $elapsedTime = (int) now()->timestamp - (int) $this->repair_started_at->timestamp;

        return min(100, max(0, (int) (($elapsedTime / $totalTime) * 100)));
    }
}
