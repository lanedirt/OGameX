<?php

namespace OGame\Services;

use OGame\Enums\IncomingFleetIntelLevel;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\FleetMission;
use OGame\Models\Resources;
use OGame\ViewModels\FleetEventRowViewModel;

/**
 * Redacts foreign fleet composition on event-list / movement view models
 * according to the viewing player's Espionage Technology level.
 *
 * Redaction only applies to hostile/enemy missions (attack, ACS attack, espionage, etc.).
 * Friendly and neutral missions (transport, ACS defend, etc.) are always shown in full,
 * with cargo visibility gated on whether the viewer has the Commander officer.
 */
class IncomingFleetIntelService
{
    /** Mission types considered hostile/enemy (attack, ACS attack, espionage, destroy, missile). */
    private const HOSTILE_MISSION_TYPES = [1, 2, 6, 9, 10];

    /**
     * Shape the visible intel for a single incoming fleet mission.
     *
     * - Own fleets: always fully visible including cargo.
     * - Foreign hostile fleets (attack, ACS attack, etc.): unit composition and cargo
     *   are redacted according to the viewer's Espionage Technology level.
     * - Foreign friendly/neutral fleets (transport, ACS defend, etc.): composition is
     *   fully visible; cargo is shown only when the viewer has the Commander officer.
     *
     * Returned keys:
     *   - units       UnitCollection (possibly redacted)
     *   - ship_count  int            (total visible ship count; 0 when level hides count)
     *   - resources   Resources      (cargo; shown for own/friendly-with-commander, else zeroed)
     *   - intel_level IncomingFleetIntelLevel (tier resolved from espionage research)
     *   - show_shipment bool         (whether cargo row should be rendered)
     *
     * @param FleetMission $mission
     * @param PlayerService $viewer
     * @param FleetMissionService|null $fleetMissionService  Required when called from controllers;
     *   if omitted the method reads unit/resource data directly from the mission model.
     * @return array{units: UnitCollection, ship_count: int, resources: Resources, intel_level: IncomingFleetIntelLevel, show_shipment: bool}
     */
    public function shapeIncomingFleetIntel(
        FleetMission $mission,
        PlayerService $viewer,
        FleetMissionService|null $fleetMissionService = null
    ): array {
        // Build raw unit collection and resources from the mission record.
        if ($fleetMissionService !== null) {
            $units = $fleetMissionService->getFleetUnits($mission);
            $resources = $fleetMissionService->getResources($mission);
        } else {
            $units = new UnitCollection();
            foreach (ObjectService::getShipObjects() as $ship) {
                $amount = $mission->{$ship->machine_name} ?? 0;
                if ($amount > 0) {
                    $units->addUnit($ship, $amount);
                }
            }
            $resources = new Resources(
                $mission->metal ?? 0,
                $mission->crystal ?? 0,
                ($mission->deuterium ?? 0) + (($mission->deuterium_consumption ?? 0) / 2),
                0
            );
        }

        // Own fleets are always shown in full.
        if ($mission->user_id === $viewer->getId()) {
            return [
                'units' => $units,
                'ship_count' => $units->getAmount(),
                'resources' => $resources,
                'intel_level' => IncomingFleetIntelLevel::Full,
                'show_shipment' => true,
            ];
        }

        // Foreign hostile fleets: apply espionage-technology-based tier redaction.
        if (in_array($mission->mission_type, self::HOSTILE_MISSION_TYPES, true)) {
            $level = IncomingFleetIntelLevel::fromEspionageLevel(
                $viewer->getResearchLevel('espionage_technology')
            );

            $redactedUnits = match ($level) {
                IncomingFleetIntelLevel::None => new UnitCollection(),
                IncomingFleetIntelLevel::TotalCount => new UnitCollection(),
                IncomingFleetIntelLevel::ShipTypes => $this->stripUnitAmounts($units),
                IncomingFleetIntelLevel::Full => $units,
            };

            $shipCount = $level->showsTotalCount() ? $units->getAmount() : 0;

            return [
                'units' => $redactedUnits,
                'ship_count' => $shipCount,
                'resources' => new Resources(0, 0, 0, 0),
                'intel_level' => $level,
                'show_shipment' => false,
            ];
        }

        // Foreign friendly/neutral fleets: full composition is visible; cargo depends on Commander.
        $showShipment = $viewer->hasCommander();

        return [
            'units' => $units,
            'ship_count' => $units->getAmount(),
            'resources' => $showShipment ? $resources : new Resources(0, 0, 0, 0),
            'intel_level' => IncomingFleetIntelLevel::Full,
            'show_shipment' => $showShipment,
        ];
    }

    /**
     * Return a copy of the unit collection with all amounts set to 0 (for ShipTypes tier).
     */
    private function stripUnitAmounts(UnitCollection $units): UnitCollection
    {
        $stripped = new UnitCollection();
        foreach ($units->units as $entry) {
            $stripped->addUnit($entry->unitObject, 0);
        }
        return $stripped;
    }

    /**
     * Return a human-readable fleet direction label for Phalanx scan results.
     *
     * @param FleetMission $mission
     * @param PlayerService $scannerPlayer
     * @return string
     */
    public function getFleetDirectionLabel(FleetMission $mission, PlayerService $scannerPlayer): string
    {
        if ($mission->user_id === $scannerPlayer->getId()) {
            return 'Own fleet';
        }

        // Attack (1) and ACS Attack (2) are enemy fleets.
        if (in_array($mission->mission_type, [1, 2], true)) {
            return 'Enemy fleet';
        }

        return 'Friendly fleet';
    }

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
                $origin['ship_count'] = 0;
                $row->union_player_breakdown[$playerIndex]['origins'][$originIndex] = $origin;
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
