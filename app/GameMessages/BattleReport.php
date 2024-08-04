<?php

namespace OGame\GameMessages;

use OGame\Facades\AppUtil;
use OGame\GameMessages\Abstracts\GameMessage;
use OGame\Models\Planet\Coordinate;
use OGame\Models\Resources;

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
        $planet = $this->planetServiceFactory->makeForCoordinate($coordinate);
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
        $params = $this->getBattleReportParams();
        return view('ingame.messages.templates.battle_report', $params)->render();
    }

    /**
     * @inheritdoc
     */
    public function getBodyFull(): string
    {
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
        $this->loadBattleReportModel();

        // TODO: add feature test for code below and check edgecases, such as when the planet has been deleted and
        // does not exist anymore. What should we show in that case?

        // Load planet by coordinate.
        $coordinate = new Coordinate($this->battleReportModel->planet_galaxy, $this->battleReportModel->planet_system, $this->battleReportModel->planet_position);
        $planet = $this->planetServiceFactory->makeForCoordinate($coordinate);

        // If planet owner is the same as the player, we load the player by planet owner which is already loaded.
        if ($this->battleReportModel->planet_user_id === $planet->getPlayer()->getId()) {
            $defender = $this->playerServiceFactory->make($planet->getPlayer()->getId());
        } else {
            // It is theoretically possible that the original player has deleted their planet and another user has
            // colonized the same position of the original planet. In that case, we should load the player by user_id
            // from the espionage report.
            $defender = $this->playerServiceFactory->make($this->battleReportModel->planet_user_id);
        }

        // Extract params from the battle report model.
        $attackerPlayerId = $this->battleReportModel->attacker['player_id'];
        $attackerLosses = $this->battleReportModel->attacker['resource_loss'];
        $defenderLosses = $this->battleReportModel->defender['resource_loss'];

        $lootPercentage = $this->battleReportModel->loot['percentage'];
        $lootMetal = $this->battleReportModel->loot['metal'];
        $lootCrystal = $this->battleReportModel->loot['crystal'];
        $lootDeuterium = $this->battleReportModel->loot['deuterium'];
        $lootResources= new Resources($lootMetal, $lootCrystal, $lootDeuterium, 0);

        $debrisMetal = $this->battleReportModel->debris['metal'];
        $debrisCrystal = $this->battleReportModel->debris['crystal'];
        $debrisDeuterium = $this->battleReportModel->debris['deuterium'];
        $debrisResources = new Resources($debrisMetal, $debrisCrystal, $debrisDeuterium, 0);

        $repairedDefensesCount = 0;
        if (!empty($this->battleReportModel->repaired_defenses)) {
            foreach ($this->battleReportModel->repaired_defenses as $defense_key => $defense_count) {
                $repairedDefensesCount += $defense_count;
            }
        }

        // Load attacker player
        $attacker = $this->playerServiceFactory->make($attackerPlayerId);

        return [
            'subject' => $this->getSubject(),
            'from' => $this->getFrom(),
            'attacker_name' => $attacker->getUsername(false),
            'defender_name' => $defender->getUsername(false),
            'attacker_losses' => AppUtil::formatNumberShort($attackerLosses),
            'defender_losses' => AppUtil::formatNumberShort($defenderLosses),
            'loot' => AppUtil::formatNumberShort($lootResources->sum()),
            'loot_percentage' => $lootPercentage,
            'debris' => AppUtil::formatNumberShort($debrisResources->sum()),
            'repaired_defenses_count' => $repairedDefensesCount,
            //'metal' => $resources->metal->getFormatted(),
            //'crystal' => $resources->crystal->getFormatted(),
            //'deuterium' => $resources->deuterium->getFormatted(),
            //'energy' => $resources->energy->getFormatted(),
            //'resources_sum' => AppUtil::formatNumber($resources->sum()),
            //'ships' => $ships,
            //'defense' => $defense,
            //'buildings' => $buildings,
            //'research' => $research,
        ];
    }
}
