<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\GameMessage;
use OGame\Models\Planet\Coordinate;

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

        // Params
        $attackerPlayerId = $this->battleReportModel->attacker['player_id'];

        // Load attacker player
        $attacker = $this->playerServiceFactory->make($attackerPlayerId);


        // Extract resources
        //$resources = new Resources($this->battleReportModel->resources['metal'], $this->battleReportModel->resources['crystal'], $this->battleReportModel->resources['deuterium'], $this->battleReportModel->resources['energy']);

        // Extract ships
        /*$ships = [];
        if ($this->espionageReportModel->ships !== null) {
            foreach ($this->espionageReportModel->ships as $machine_name => $amount) {
                // Get object
                $unit = $planet->objects->getUnitObjectByMachineName($machine_name);

                $unitViewModel = new UnitViewModel();
                $unitViewModel->amount = $amount;
                $unitViewModel->object = $unit;

                $ships[$unit->machine_name] = $unitViewModel;
            }
        }

        // Extract defense
        $defense = [];
        if ($this->espionageReportModel->defense !== null) {
            foreach ($this->espionageReportModel->defense as $machine_name => $amount) {
                // Get object
                $unit = $planet->objects->getUnitObjectByMachineName($machine_name);

                $unitViewModel = new UnitViewModel();
                $unitViewModel->amount = $amount;
                $unitViewModel->object = $unit;

                $defense[$unit->machine_name] = $unitViewModel;
            }
        }

        // Extract buildings
        $buildings = [];
        if ($this->espionageReportModel->buildings !== null) {
            foreach ($this->espionageReportModel->buildings as $machine_name => $amount) {
                // Get object
                $unit = $planet->objects->getObjectByMachineName($machine_name);

                $unitViewModel = new UnitViewModel();
                $unitViewModel->amount = $amount;
                $unitViewModel->object = $unit;

                $buildings[$unit->machine_name] = $unitViewModel;
            }
        }

        // Extract research
        $research = [];
        if ($this->espionageReportModel->research !== null) {
            foreach ($this->espionageReportModel->research as $machine_name => $amount) {
                // Get object
                $unit = $planet->objects->getObjectByMachineName($machine_name);

                $unitViewModel = new UnitViewModel();
                $unitViewModel->amount = $amount;
                $unitViewModel->object = $unit;

                $research[$unit->machine_name] = $unitViewModel;
            }
        }*/

        return [
            'subject' => $this->getSubject(),
            'from' => $this->getFrom(),
            'attacker_name' => $attacker->getUsername(false),
            'defender_name' => $defender->getUsername(false),
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
