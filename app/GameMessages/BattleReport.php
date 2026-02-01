<?php

namespace OGame\GameMessages;

use OGame\Facades\AppUtil;
use OGame\GameMessages\Abstracts\GameMessage;
use OGame\GameMissions\BattleEngine\Models\BattleResultRound;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Enums\PlanetType;
use OGame\Models\Planet\Coordinate;
use OGame\Models\Resources;
use OGame\Services\DebrisFieldService;
use OGame\Services\ObjectService;
use Throwable;

class BattleReport extends GameMessage
{
    /**
     * @var \OGame\Models\BattleReport|null The battle report model from database.
     */
    private \OGame\Models\BattleReport|null $battleReportModel = null;

    protected function initialize(): void
    {
        $this->key = 'battle_report';
        $this->params = [];
        $this->tab = 'fleets';
        $this->subtab = 'combat_reports';
    }

    /**
     * Load battle report model from database. If already loaded, do nothing.
     *
     * @return void
     */
    private function loadBattleReportModel(): void
    {
        if ($this->battleReportModel !== null) {
            // Already loaded.
            return;
        }

        // Load battle report model from database associated with the message.
        $battleReport = \OGame\Models\BattleReport::where('id', $this->message->battle_report_id)->first();
        if ($battleReport === null) {
            // If battle report is not found, we use an empty model. This is for testing purposes.
            $this->battleReportModel = new \OGame\Models\BattleReport();
        } else {
            $this->battleReportModel = $battleReport;
        }
    }

    /**
     * @inheritdoc
     */
    public function getSubject(): string
    {
        $this->loadBattleReportModel();

        // Load the planet name from the references table and return the subject filled with the planet name.
        $coordinate = new Coordinate($this->battleReportModel->planet_galaxy, $this->battleReportModel->planet_system, $this->battleReportModel->planet_position);
        $planet = $this->planetServiceFactory->makeForCoordinate($coordinate, true, PlanetType::from($this->battleReportModel->planet_type));
        if ($planet) {
            $subject = __('Combat report :planet', ['planet' => '[planet]' . $planet->getPlanetId() . '[/planet]']);
        } else {
            $subject = __('Combat report  :planet', ['planet' => '[coordinates]' . $coordinate->asString() . '[/coordinates]']);
        }

        return $this->replacePlaceholders($subject);
    }

    /**
     * @inheritdoc
     */
    public function getBody(): string
    {
        $this->loadBattleReportModel();

        // Load planet by coordinate.
        $coordinate = new Coordinate($this->battleReportModel->planet_galaxy, $this->battleReportModel->planet_system, $this->battleReportModel->planet_position);
        $planet = $this->planetServiceFactory->makeForCoordinate($coordinate, true, PlanetType::from($this->battleReportModel->planet_type));

        if ($planet === null) {
            return __('Planet has been deleted and battle report is no longer available.');
        }

        $params = $this->getBattleReportParams();
        return view('ingame.messages.templates.battle_report', $params)->render();
    }

    /**
     * @inheritdoc
     */
    public function getBodyFull(): string
    {
        $this->loadBattleReportModel();

        // Load planet by coordinate.
        $coordinate = new Coordinate($this->battleReportModel->planet_galaxy, $this->battleReportModel->planet_system, $this->battleReportModel->planet_position);
        $planet = $this->planetServiceFactory->makeForCoordinate($coordinate, true, PlanetType::from($this->battleReportModel->planet_type));

        if ($planet === null) {
            // TODO: add feature test for this behavior to make sure deleting a planet
            // properly handles any existing battle reports by either deleting them or making
            // them unavailable. This also affects other messages that use the planet name.
            return __('Planet has been deleted and battle report is no longer available.');
        }

        $params = $this->getBattleReportParams();
        return view('ingame.messages.templates.battle_report_full', $params)->render();
    }

    /**
     * @inheritdoc
     */
    public function getFooterDetails(): string
    {
        // Show more details link in the footer of the battle report.
        return ' <a class="fright txt_link msg_action_link overlay"
                   href="' . $this->getFullMessageUrl() . '"
                   data-overlay-title="More details">
                    More details
                </a>';
    }

    /**
     * Get the battle report params.
     *
     * @return array<string, mixed>
     */
    private function getBattleReportParams(): array
    {
        // Sanity check to make sure the battle report model is loaded.
        $this->loadBattleReportModel();

        // TODO: add feature test for code below and check edgecases, such as when the planet has been deleted and
        // does not exist anymore. What should we show in that case?

        // Load planet by coordinate.
        $coordinate = new Coordinate($this->battleReportModel->planet_galaxy, $this->battleReportModel->planet_system, $this->battleReportModel->planet_position);
        $planet = $this->planetServiceFactory->makeForCoordinate($coordinate, true, PlanetType::from($this->battleReportModel->planet_type));

        // Handle defender
        if ($this->battleReportModel->planet_user_id === null) {
            $defender_name = __('Unknown');
            $defender = null;
        } else {
            // If planet owner is the same as the player, we load the player by planet owner which is already loaded.
            if ($this->battleReportModel->planet_user_id === $planet->getPlayer()->getId()) {
                $defender = $this->playerServiceFactory->make($planet->getPlayer()->getId());
            } else {
                // Load player by user_id from the battle report
                $defender = $this->playerServiceFactory->make($this->battleReportModel->planet_user_id);
            }
            $defender_name = $defender->getUsername(false);
        }

        // Handle attacker
        $attackerPlayerId = $this->battleReportModel->attacker['player_id'];
        $attackerPlanetId = $this->battleReportModel->attacker['planet_id'] ?? null;

        // NPC attacker - translate name based on player ID
        if ($attackerPlayerId < 0) {
            $attacker = null;
            $attacker_name = match($attackerPlayerId) {
                -1 => __('Pirates'),
                -2 => __('Aliens'),
                default => __('Unknown'),
            };
        } else {
            // Real player - try to load from database
            try {
                $attacker = $this->playerServiceFactory->make($attackerPlayerId, true);
                $attacker_name = $attacker->getUsername(false);
            } catch (Throwable $e) {
                // If attacker can't be loaded (e.g., user deleted), use "Unknown"
                $attacker = null;
                $attacker_name = __('Unknown');
            }
        }

        // Load attacker's origin planet info
        // For NPCs (planet_id is null), translate "Deep space" dynamically
        if ($attackerPlayerId < 0 && $attackerPlanetId === null) {
            $attacker_planet_name = __('Deep space');
            $attacker_planet_coords = $this->battleReportModel->attacker['planet_coords'] ?? '';
            $attacker_planet_type = '';
        } elseif ($attackerPlanetId !== null) {
            // Try to load from database for real players
            try {
                $attackerPlanet = $this->planetServiceFactory->make($attackerPlanetId, true);
                if ($attackerPlanet !== null) {
                    $attacker_planet_name = $attackerPlanet->getPlanetName();
                    $attacker_planet_coords = $attackerPlanet->getPlanetCoordinates()->asString();
                    $attacker_planet_type = $attackerPlanet->getPlanetType() === PlanetType::Moon ? 'Moon' : 'Planet';
                } else {
                    $attacker_planet_name = __('Unknown');
                    $attacker_planet_coords = '';
                    $attacker_planet_type = '';
                }
            } catch (Throwable $e) {
                // If attacker planet can't be loaded (e.g., planet deleted), use defaults
                $attacker_planet_name = __('Unknown');
                $attacker_planet_coords = '';
                $attacker_planet_type = '';
            }
        } else {
            // No planet info available
            $attacker_planet_name = __('Unknown');
            $attacker_planet_coords = '';
            $attacker_planet_type = '';
        }

        $defender_weapons = $this->battleReportModel->defender['weapon_technology'] * 10;
        $defender_shields = $this->battleReportModel->defender['shielding_technology'] * 10;
        $defender_armor = $this->battleReportModel->defender['armor_technology'] * 10;

        // Extract params from the battle report model.
        $attackerLosses = $this->battleReportModel->attacker['resource_loss'];
        $defenderLosses = $this->battleReportModel->defender['resource_loss'];

        $lootPercentage = $this->battleReportModel->loot['percentage'];
        $lootMetal = $this->battleReportModel->loot['metal'];
        $lootCrystal = $this->battleReportModel->loot['crystal'];
        $lootDeuterium = $this->battleReportModel->loot['deuterium'];
        $lootResources = new Resources($lootMetal, $lootCrystal, $lootDeuterium, 0);

        $debrisMetal = $this->battleReportModel->debris['metal'] ?? 0;
        $debrisCrystal = $this->battleReportModel->debris['crystal'] ?? 0;
        $debrisDeuterium = $this->battleReportModel->debris['deuterium'] ?? 0;
        $debrisResources = new Resources($debrisMetal, $debrisCrystal, $debrisDeuterium, 0);

        // Extract collected debris (Reaper auto-collection)
        $collectedDebrisMetal = $this->battleReportModel->debris['collected_metal'] ?? 0;
        $collectedDebrisCrystal = $this->battleReportModel->debris['collected_crystal'] ?? 0;
        $collectedDebrisDeuterium = $this->battleReportModel->debris['collected_deuterium'] ?? 0;
        $collectedDebrisResources = new Resources($collectedDebrisMetal, $collectedDebrisCrystal, $collectedDebrisDeuterium, 0);

        // Extract attacker collected debris
        $attackerCollectedMetal = $this->battleReportModel->debris['attacker_collected_metal'] ?? 0;
        $attackerCollectedCrystal = $this->battleReportModel->debris['attacker_collected_crystal'] ?? 0;
        $attackerCollectedDeuterium = $this->battleReportModel->debris['attacker_collected_deuterium'] ?? 0;
        $attackerCollectedDebrisResources = new Resources($attackerCollectedMetal, $attackerCollectedCrystal, $attackerCollectedDeuterium, 0);

        // Extract defender collected debris
        $defenderCollectedMetal = $this->battleReportModel->debris['defender_collected_metal'] ?? 0;
        $defenderCollectedCrystal = $this->battleReportModel->debris['defender_collected_crystal'] ?? 0;
        $defenderCollectedDeuterium = $this->battleReportModel->debris['defender_collected_deuterium'] ?? 0;
        $defenderCollectedDebrisResources = new Resources($defenderCollectedMetal, $defenderCollectedCrystal, $defenderCollectedDeuterium, 0);

        // Calculate remaining debris (debris created - collected by Reapers)
        $remainingDebrisMetal = $debrisMetal - $collectedDebrisMetal;
        $remainingDebrisCrystal = $debrisCrystal - $collectedDebrisCrystal;
        $remainingDebrisDeuterium = $debrisDeuterium - $collectedDebrisDeuterium;
        $remainingDebrisResources = new Resources($remainingDebrisMetal, $remainingDebrisCrystal, $remainingDebrisDeuterium, 0);

        // TODO: Expedition battle debris field collection
        // For expedition battles (when player classes are implemented with Discoverer class),
        // the debris field will be at position 16 and can only be collected by Pathfinders,
        // not Recyclers. Update this section to:
        // 1. Check if this is an expedition battle (check $this->battleReportModel->general['expedition_battle'])
        // 2. If yes, calculate and display Pathfinders needed instead of Recyclers
        // 3. If no, continue showing Recyclers as normal
        //
        // Calculate the amount of recyclers needed using DebrisFieldService
        $debrisFieldService = resolve(DebrisFieldService::class);
        $debrisFieldService->appendResources($debrisResources);
        $debrisRecyclersNeeded = $debrisFieldService->calculateRequiredRecyclers();

        // Calculate recyclers needed for remaining debris
        $remainingDebrisFieldService = resolve(DebrisFieldService::class);
        $remainingDebrisFieldService->appendResources($remainingDebrisResources);
        $remainingDebrisRecyclersNeeded = $remainingDebrisFieldService->calculateRequiredRecyclers();

        // Calculate Reapers that collected debris
        $reaperObject = ObjectService::getShipObjectByMachineName('reaper');
        $reaperCargoCapacity = $reaperObject->properties->capacity->rawValue;
        $attackerReapersUsed = $reaperCargoCapacity > 0 ? (int)ceil($attackerCollectedDebrisResources->sum() / $reaperCargoCapacity) : 0;
        $defenderReapersUsed = $reaperCargoCapacity > 0 ? (int)ceil($defenderCollectedDebrisResources->sum() / $reaperCargoCapacity) : 0;
        $totalReapersUsed = $attackerReapersUsed + $defenderReapersUsed;

        $repairedDefensesCount = 0;
        $repairedDefenses = new UnitCollection();
        if (!empty($this->battleReportModel->repaired_defenses)) {
            foreach ($this->battleReportModel->repaired_defenses as $defense_key => $defense_count) {
                $repairedDefensesCount += $defense_count;
                if ($defense_count > 0) {
                    $repairedDefenses->addUnit(ObjectService::getUnitObjectByMachineName($defense_key), $defense_count);
                }
            }
        }

        $moonExisted = false;
        $moonChance = 0;
        $moonCreated = false;
        $hamillManoeuvreTriggered = false;

        if (isset($this->battleReportModel->general['moon_existed'])) {
            $moonExisted = $this->battleReportModel->general['moon_existed'];
        }
        if (isset($this->battleReportModel->general['moon_chance'])) {
            $moonChance = $this->battleReportModel->general['moon_chance'];
        }
        if (isset($this->battleReportModel->general['moon_created'])) {
            $moonCreated = $this->battleReportModel->general['moon_created'];
        }
        if (isset($this->battleReportModel->general['hamill_manoeuvre_triggered'])) {
            $hamillManoeuvreTriggered = $this->battleReportModel->general['hamill_manoeuvre_triggered'];
        }

        // Load attacker player
        // TODO: add unit test for attacker/defender research levels.
        $attacker_weapons = $this->battleReportModel->attacker['weapon_technology'] * 10;
        $attacker_shields = $this->battleReportModel->attacker['shielding_technology'] * 10;
        $attacker_armor = $this->battleReportModel->attacker['armor_technology'] * 10;

        $attacker_units = new UnitCollection();
        foreach ($this->battleReportModel->attacker['units'] as $machine_name => $amount) {
            $attacker_units->addUnit(ObjectService::getUnitObjectByMachineName($machine_name), $amount);
        }

        $defender_units = new UnitCollection();
        foreach ($this->battleReportModel->defender['units'] as $machine_name => $amount) {
            $defender_units->addUnit(ObjectService::getUnitObjectByMachineName($machine_name), $amount);
        }

        // Load rounds and cast to battle result round object.
        $rounds = [];
        if ($this->battleReportModel->rounds !== null) {
            foreach ($this->battleReportModel->rounds as $round) {
                $obj = new BattleResultRound();
                $obj->fullStrengthAttacker = $round['full_strength_attacker'];
                $obj->fullStrengthDefender = $round['full_strength_defender'];
                $obj->absorbedDamageAttacker = $round['absorbed_damage_attacker'];
                $obj->absorbedDamageDefender = $round['absorbed_damage_defender'];
                $obj->hitsAttacker = $round['hits_attacker'];
                $obj->hitsDefender = $round['hits_defender'];
                $obj->defenderShips = new UnitCollection();
                foreach ($round['defender_ships'] as $machine_name => $amount) {
                    $unit = ObjectService::getUnitObjectByMachineName($machine_name);
                    $obj->defenderShips->addUnit($unit, $amount);
                }
                $obj->attackerShips = new UnitCollection();
                foreach ($round['attacker_ships'] as $machine_name => $amount) {
                    $unit = ObjectService::getUnitObjectByMachineName($machine_name);
                    $obj->attackerShips->addUnit($unit, $amount);
                }
                $obj->defenderLosses = new UnitCollection();
                foreach ($round['defender_losses'] as $machine_name => $amount) {
                    $unit = ObjectService::getUnitObjectByMachineName($machine_name);
                    $obj->defenderLosses->addUnit($unit, $amount);
                }
                $obj->attackerLosses = new UnitCollection();
                foreach ($round['attacker_losses'] as $machine_name => $amount) {
                    $unit = ObjectService::getUnitObjectByMachineName($machine_name);
                    $obj->attackerLosses->addUnit($unit, $amount);
                }
                $obj->defenderLossesInRound = new UnitCollection();
                foreach ($round['defender_losses_in_this_round'] as $machine_name => $amount) {
                    $unit = ObjectService::getUnitObjectByMachineName($machine_name);
                    $obj->defenderLossesInRound->addUnit($unit, $amount);
                }
                $obj->attackerLossesInRound = new UnitCollection();
                foreach ($round['attacker_losses_in_this_round'] as $machine_name => $amount) {
                    $unit = ObjectService::getUnitObjectByMachineName($machine_name);
                    $obj->attackerLossesInRound->addUnit($unit, $amount);
                }
                $rounds[] = $obj;
            }
        }

        // Determine if attacker or defender won the battle or if it was a draw.
        // We do this based on last round result.
        if (count($rounds) === 0) {
            // No rounds, attacker wins.
            $winner = 'attacker';
        } else {
            $lastRound = $rounds[count($rounds) - 1];
            if ($lastRound->attackerShips->getAmount() > 0 && $lastRound->defenderShips->getAmount() > 0) {
                // Both players have ships left, draw.
                $winner = 'draw';
            } elseif ($lastRound->attackerShips->getAmount() > 0) {
                // Attacker has ships left, attacker wins.
                $winner = 'attacker';
            } else {
                // Defender has ships left, defender wins.
                $winner = 'defender';
            }
        }

        return [
            'subject' => $this->getSubject(),
            'from' => $this->getFrom(),
            'report_datetime' => $this->getDateFormatted(),
            'attacker_name' => $attacker_name,
            'attacker_planet_name' => $attacker_planet_name,
            'attacker_planet_coords' => $attacker_planet_coords,
            'attacker_planet_type' => $attacker_planet_type,
            'defender_name' => $defender_name,
            'attacker_class' => ($winner === 'attacker') ? 'undermark' : (($winner === 'draw') ? 'middlemark' : 'overmark'),
            'defender_class' => ($winner === 'defender') ? 'undermark' : (($winner === 'draw') ? 'middlemark' : 'overmark'),
            'attacker_character_class' => isset($this->battleReportModel->attacker['character_class']) ? $this->battleReportModel->attacker['character_class'] : null,
            'defender_character_class' => isset($this->battleReportModel->defender['character_class']) ? $this->battleReportModel->defender['character_class'] : null,
            'defender_planet_name' => $planet->getPlanetName(),
            'defender_planet_coords' => $planet->getPlanetCoordinates()->asString(),
            'defender_planet_link' => route('galaxy.index', ['galaxy' => $planet->getPlanetCoordinates()->galaxy, 'system' => $planet->getPlanetCoordinates()->system, 'position' => $planet->getPlanetCoordinates()->position]),
            'attacker_losses' => AppUtil::formatNumberLong($attackerLosses),
            'defender_losses' => AppUtil::formatNumberLong($defenderLosses),
            'loot' => AppUtil::formatNumberShort($lootResources->sum()),
            'loot_resources' => $lootResources,
            'loot_percentage' => $lootPercentage,
            'debris_sum_formatted' => AppUtil::formatNumberLong($debrisResources->sum()),
            'debris_resources' => $debrisResources,
            'debris_recyclers_needed' => $debrisRecyclersNeeded,
            'collected_debris_resources' => $collectedDebrisResources,
            'attacker_collected_debris_resources' => $attackerCollectedDebrisResources,
            'defender_collected_debris_resources' => $defenderCollectedDebrisResources,
            'remaining_debris_resources' => $remainingDebrisResources,
            'remaining_debris_recyclers_needed' => $remainingDebrisRecyclersNeeded,
            'total_reapers_used' => $totalReapersUsed,
            'repaired_defenses_count' => $repairedDefensesCount,
            'repaired_defenses' => $repairedDefenses,
            'moon_existed' => $moonExisted,
            'moon_chance' => $moonChance,
            'moon_created' => $moonCreated,
            'hamill_manoeuvre_triggered' => $hamillManoeuvreTriggered,
            'attacker_weapons' => $attacker_weapons,
            'attacker_shields' => $attacker_shields,
            'attacker_armor' => $attacker_armor,
            'defender_weapons' => $defender_weapons,
            'defender_shields' => $defender_shields,
            'defender_armor' => $defender_armor,
            'military_objects' => ObjectService::getMilitaryShipObjects(),
            'civil_objects' => ObjectService::getCivilShipObjects(),
            'defense_objects' => ObjectService::getDefenseObjects(),
            'attacker_units_start' => $attacker_units,
            'defender_units_start' => $defender_units,
            'rounds' => $rounds,
        ];
    }
}
