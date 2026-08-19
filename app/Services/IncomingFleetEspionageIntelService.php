<?php

namespace OGame\Services;

use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Resources;

class IncomingFleetEspionageIntelService
{
    /**
     * Returns the fraction of hostile fleet details that are revealed to the spying player.
     *
     * The thresholds are intentionally discrete to match gameplay expectations and unit tests.
     */
    public function getHostileRevealRatio(int $espionageLevel): float
    {
        return match (true) {
            $espionageLevel >= 8 => 1.0,
            $espionageLevel >= 4 => 0.75,
            $espionageLevel >= 2 => 0.5,
            default => 0.0,
        };
    }

    /**
     * Applies hostile fleet redaction to the provided ship collection.
     *
     * Redaction is implemented by scaling each unit count by the hostile reveal ratio and
     * removing units that end up at 0.
     */
    public function redactUnits(UnitCollection $units, int $espionageLevel): UnitCollection
    {
        $ratio = $this->getHostileRevealRatio($espionageLevel);
        if ($ratio <= 0) {
            return new UnitCollection();
        }

        $redacted = clone $units;

        foreach ($redacted->units as $key => $entry) {
            $entry->amount = (int) floor($entry->amount * $ratio);
            if ($entry->amount <= 0) {
                unset($redacted->units[$key]);
            }
        }

        $redacted->units = array_values($redacted->units);

        return $redacted;
    }

    /**
     * Applies hostile fleet redaction to the provided resources payload.
     */
    public function redactResources(Resources $resources, int $espionageLevel): Resources
    {
        $ratio = $this->getHostileRevealRatio($espionageLevel);
        if ($ratio <= 0) {
            return new Resources(0, 0, 0, 0);
        }

        return new Resources(
            (int) floor($resources->metal->get() * $ratio),
            (int) floor($resources->crystal->get() * $ratio),
            (int) floor($resources->deuterium->get() * $ratio),
            0
        );
    }
}
