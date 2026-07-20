<?php

namespace OGame\Services;

use OGame\Enums\IncomingFleetIntelLevel;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Resources;
use OGame\ViewModels\FleetEventRowViewModel;

/**
 * Redacts foreign fleet composition on event-list / movement view models
 * according to the viewing player's Espionage Technology level.
 */
class IncomingFleetIntelService
{
    /**
     * Resolve intel level from the viewer's espionage technology.
     */
    public function resolveLevel(PlayerService $viewer): IncomingFleetIntelLevel
    {
        return IncomingFleetIntelLevel::fromEspionageLevel(
            $viewer->getResearchLevel('espionage_technology')
        );
    }

    /**
     * Apply redaction to a foreign (non-own) fleet event row.
     *
     * Always hides shipment/cargo for foreign fleets. Ship count and composition
     * are stripped according to $level.
     */
    public function apply(FleetEventRowViewModel $row, IncomingFleetIntelLevel $level): void
    {
        $row->fleet_intel_level = $level;
        $row->show_shipment = false;

        // Foreign fleets never expose cargo via the event list.
        $row->resources = new Resources(0, 0, 0, 0);

        match ($level) {
            IncomingFleetIntelLevel::None => $this->applyNone($row),
            IncomingFleetIntelLevel::TotalCount => $this->applyTotalCount($row),
            IncomingFleetIntelLevel::ShipTypes => $this->applyShipTypes($row),
            IncomingFleetIntelLevel::Full => null, // keep units and count; resources already cleared
        };

        // Redact union summary breakdown ship counts when total is hidden.
        if (!$level->showsTotalCount()) {
            $this->redactUnionBreakdownShipCounts($row);
            $row->fleet_unit_count = 0;
        }
    }

    /**
     * Apply redaction to every foreign member of a union summary, then the summary itself.
     *
     * @param int $viewerUserId Current player ID (own member fleets stay unredacted).
     */
    public function applyToUnionSummary(FleetEventRowViewModel $summaryRow, IncomingFleetIntelLevel $level, int $viewerUserId): void
    {
        foreach ($summaryRow->union_member_fleets as $member) {
            if ($member->user_id !== null && $member->user_id !== $viewerUserId) {
                $this->apply($member, $level);
            }
        }

        // Rebuild summary totals from (possibly redacted) members so counts stay consistent.
        $totalUnits = 0;
        foreach ($summaryRow->union_member_fleets as $member) {
            $totalUnits += $member->fleet_unit_count;
        }
        $summaryRow->fleet_unit_count = $totalUnits;
        $summaryRow->fleet_intel_level = $level;
        $summaryRow->show_shipment = false;
        $summaryRow->resources = new Resources(0, 0, 0, 0);
        $summaryRow->fleet_units = $level->showsShipTypes()
            ? ($summaryRow->union_member_fleets[0]->fleet_units ?? new UnitCollection())
            : new UnitCollection();

        if (!$level->showsTotalCount()) {
            $this->redactUnionBreakdownShipCounts($summaryRow);
            $summaryRow->fleet_unit_count = 0;
        } else {
            // Rebuild breakdown ship counts from redacted members when totals are visible.
            $this->rebuildUnionBreakdownFromMembers($summaryRow);
        }
    }

    private function applyNone(FleetEventRowViewModel $row): void
    {
        $row->fleet_unit_count = 0;
        $row->fleet_units = new UnitCollection();
    }

    private function applyTotalCount(FleetEventRowViewModel $row): void
    {
        $row->fleet_units = new UnitCollection();
    }

    private function applyShipTypes(FleetEventRowViewModel $row): void
    {
        // Keep ship types but strip amounts so tooltips cannot leak real counts.
        $redacted = new UnitCollection();
        foreach ($row->fleet_units->units as $entry) {
            $redacted->addUnit($entry->unitObject, 0);
        }
        $row->fleet_units = $redacted;
    }

    private function redactUnionBreakdownShipCounts(FleetEventRowViewModel $row): void
    {
        foreach ($row->union_player_breakdown as $playerIndex => $playerInfo) {
            foreach ($playerInfo['origins'] as $originIndex => $origin) {
                $row->union_player_breakdown[$playerIndex]['origins'][$originIndex]['ship_count'] = 0;
            }
        }
    }

    private function rebuildUnionBreakdownFromMembers(FleetEventRowViewModel $summaryRow): void
    {
        $playerBreakdown = [];
        foreach ($summaryRow->union_member_fleets as $fleet) {
            $playerId = $fleet->user_id ?? 0;
            if (!isset($playerBreakdown[$playerId])) {
                $playerBreakdown[$playerId] = [
                    'player_name' => $fleet->player_name,
                    'origins' => [],
                ];
            }

            $originKey = $fleet->origin_planet_coords->asString();
            if (!isset($playerBreakdown[$playerId]['origins'][$originKey])) {
                $playerBreakdown[$playerId]['origins'][$originKey] = [
                    'planet_name' => $fleet->origin_planet_name,
                    'coords' => '[' . $originKey . ']',
                    'fleet_count' => 0,
                    'ship_count' => 0,
                ];
            }
            $playerBreakdown[$playerId]['origins'][$originKey]['fleet_count']++;
            $playerBreakdown[$playerId]['origins'][$originKey]['ship_count'] += $fleet->fleet_unit_count;
        }

        foreach ($playerBreakdown as $pId => $playerData) {
            $playerBreakdown[$pId]['origins'] = array_values($playerData['origins']);
        }

        $summaryRow->union_player_breakdown = array_values($playerBreakdown);
    }
}
