<?php

namespace OGame\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property Carbon|null $commander_until
 * @property Carbon|null $admiral_until
 * @property Carbon|null $engineer_until
 * @property Carbon|null $geologist_until
 * @property Carbon|null $technocrat_until
 * @property Carbon|null $all_officers_until
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read User $user
 */
class Officer extends Model
{
    protected $fillable = [
        'user_id',
        'commander_until',
        'admiral_until',
        'engineer_until',
        'geologist_until',
        'technocrat_until',
        'all_officers_until',
    ];

    protected $casts = [
        'commander_until'    => 'datetime',
        'admiral_until'      => 'datetime',
        'engineer_until'     => 'datetime',
        'geologist_until'    => 'datetime',
        'technocrat_until'   => 'datetime',
        'all_officers_until' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isCommanderActive(): bool
    {
        return $this->isOfficerActive('commander');
    }

    public function isAdmiralActive(): bool
    {
        return $this->isOfficerActive('admiral');
    }

    public function isEngineerActive(): bool
    {
        return $this->isOfficerActive('engineer');
    }

    public function isGeologistActive(): bool
    {
        return $this->isOfficerActive('geologist');
    }

    public function isTechnocratActive(): bool
    {
        return $this->isOfficerActive('technocrat');
    }

    public function isAllOfficersActive(): bool
    {
        return $this->isOfficerActive('all_officers');
    }

    /**
     * Check if a specific officer type is active (either directly or via all_officers).
     */
    public function isOfficerActive(string $type): bool
    {
        $column      = $type . '_until';
        $directActive = $this->$column !== null && $this->$column->isFuture();

        if ($type === 'all_officers') {
            return $directActive;
        }

        // Un ufficiale è attivo se attivato singolarmente O se all_officers è attivo
        return $directActive || $this->isAllOfficersActive();
    }

    /**
     * Activate or extend an officer for a given number of days.
     */
    public function activate(string $type, int $days): void
    {
        $column  = $type . '_until';
        $current = $this->$column;

        if ($current !== null && $current->isFuture()) {
            // Estende dalla scadenza attuale (copy() evita mutazione in-place)
            $this->$column = $current->copy()->addDays($days);
        } else {
            // Nuova attivazione da adesso
            $this->$column = now()->addDays($days);
        }
    }

    /**
     * Get the number of individually active officers (excluding all_officers slot).
     */
    public function getActiveOfficerCount(): int
    {
        $count = 0;
        foreach (['commander', 'admiral', 'engineer', 'geologist', 'technocrat'] as $type) {
            if ($this->isOfficerActive($type)) {
                $count++;
            }
        }
        return $count;
    }
}
