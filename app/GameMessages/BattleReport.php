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
        // Show more details link in the footer of the espionage report.
        return ' <a class="fright txt_link msg_action_link overlay"
                   href="' . route('messages.ajax.getmessage', ['messageId' => $this->message->id])  .'"
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
        try {
            $attacker = $this->playerServiceFactory->make($attackerPlayerId, true);
            $attacker_name = $attacker->getUsername(false);
        } catch (Throwable $e) {
            // If attacker can't be loaded (e.g., user deleted), use "Unknown"
            $attacker = null;
            $attacker_name = __('Unknown');
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

        // Calculate the amount of recyclers needed using DebrisFieldService
        $debrisFieldService = resolve(DebrisFieldService::class);
        $debrisFieldService->appendResources($debrisResources);
        $debrisRecyclersNeeded = $debrisFieldService->calculateRequiredRecyclers();

        $repairedDefensesCount = 0;
        $repaired_defenses = new UnitCollection();
        // Only show repaired defenses to the defender, not the attacker
        $isDefender = ($this->message->user_id === $this->battleReportModel->planet_user_id);

        if ($isDefender && !empty($this->battleReportModel->repaired_defenses)) {
            foreach ($this->battleReportModel->repaired_defenses as $defense_key => $defense_count) {
                $repairedDefensesCount += $defense_count;
                $repaired_defenses->addUnit(ObjectService::getUnitObjectByMachineName($defense_key), $defense_count);
            }
        }

        $moonExisted = false;
        $moonChance = 0;
        $moonCreated = false;

        if (isset($this->battleReportModel->general['moon_existed'])) {
            $moonExisted = $this->battleReportModel->general['moon_existed'];
        }
        if (isset($this->battleReportModel->general['moon_chance'])) {
            $moonChance = $this->battleReportModel->general['moon_chance'];
        }
        if (isset($this->battleReportModel->general['moon_created'])) {
            $moonCreated = $this->battleReportModel->general['moon_created'];
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
            'attacker_name' => $attacker_name,
            'defender_name' => $defender_name,
            'attacker_class' => ($winner === 'attacker') ? 'undermark' : (($winner === 'draw') ? 'middlemark' : 'overmark'),
            'defender_class' => ($winner === 'defender') ? 'undermark' : (($winner === 'draw') ? 'middlemark' : 'overmark'),
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
            'repaired_defenses_count' => $repairedDefensesCount,
            'repaired_defenses' => $repaired_defenses,
            'moon_existed' => $moonExisted,
            'moon_chance' => $moonChance,
            'moon_created' => $moonCreated,
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
