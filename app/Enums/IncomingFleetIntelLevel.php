<?php

namespace OGame\Enums;

/**
 * How much composition detail a player can see for a foreign incoming fleet,
 * based on their Espionage Technology level.
 *
 * @see https://github.com/lanedirt/OGameX/issues/668
 */
enum IncomingFleetIntelLevel: string
{
    /** Levels 0–1: fleet approaching only — no count, no tooltip composition. */
    case None = 'none';

    /** Levels 2–3: exact total ship count only. */
    case TotalCount = 'total_count';

    /** Levels 4–7: total count + ship types (amounts hidden as "?"). */
    case ShipTypes = 'ship_types';

    /** Levels 8+: full per-type counts. */
    case Full = 'full';

    /**
     * Resolve intel level from the viewer's espionage technology level.
     */
    public static function fromEspionageLevel(int $level): self
    {
        return match (true) {
            $level >= 8 => self::Full,
            $level >= 4 => self::ShipTypes,
            $level >= 2 => self::TotalCount,
            default => self::None,
        };
    }

    public function showsTotalCount(): bool
    {
        return $this !== self::None;
    }

    public function showsShipTypes(): bool
    {
        return $this === self::ShipTypes || $this === self::Full;
    }

    public function showsShipAmounts(): bool
    {
        return $this === self::Full;
    }

    public function showsCompositionTooltip(): bool
    {
        return $this !== self::None;
    }
}
