<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\GameMessage;
use OGame\Models\Enums\PlanetType;
use OGame\Models\Planet\Coordinate;
use OGame\Models\Resources;
use OGame\Services\ObjectService;
use OGame\ViewModels\UnitViewModel;

class EspionageReport extends GameMessage
{
    /**
     * @var \OGame\Models\EspionageReport|null The espionage report model from database.
     */
    private \OGame\Models\EspionageReport|null $espionageReportModel = null;

    protected function initialize(): void
    {
        $this->key = 'espionage_report';
        $this->params = [];
        $this->tab = 'fleets';
        $this->subtab = 'espionage';
    }

    /**
     * Load espionage report model from database. If already loaded, do nothing.
     *
     * @return void
     */
    private function loadEspionageReportModel(): void
    {
        if ($this->espionageReportModel !== null) {
            // Already loaded.
            return ;
        }

        // Load espionage report model from database associated with the message.
        $espionageReport = \OGame\Models\EspionageReport::where('id', $this->message->espionage_report_id)->first();
        if ($espionageReport === null) {
            // If espionage report is not found, we use an empty model. This is for testing purposes.
            $this->espionageReportModel = new \OGame\Models\EspionageReport();
        } else {
            $this->espionageReportModel = $espionageReport;
        }
    }

    /**
     * @inheritdoc
     */
    public function getSubject(): string
    {
        $this->loadEspionageReportModel();

        // Load the planet name from the references table and return the subject filled with the planet name.
        $coordinate = new Coordinate($this->espionageReportModel->planet_galaxy, $this->espionageReportModel->planet_system, $this->espionageReportModel->planet_position);
        $planet = $this->planetServiceFactory->makeForCoordinate($coordinate, true, PlanetType::from($this->espionageReportModel->planet_type));
        if ($planet) {
            $subject = __('Espionage report from :planet', ['planet' => '[planet]' . $planet->getPlanetId() . '[/planet]']);
        } else {
            $subject = __('Espionage report from :planet', ['planet' => '[coordinates]' . $coordinate->asString() . '[/coordinates]']);
        }

        return $this->replacePlaceholders($subject);
    }

    /**
     * @inheritdoc
     */
    public function getBody(): string
    {
        $params = $this->getEspionageReportParams();
        return view('ingame.messages.templates.espionage_report', $params)->render();
    }

    /**
     * @inheritdoc
     */
    public function getBodyFull(): string
    {
        $params = $this->getEspionageReportParams();
        return view('ingame.messages.templates.espionage_report_full', $params)->render();
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
     * Get the espionage report params.
     *
     * @return array<string, mixed>
     */
    private function getEspionageReportParams(): array
    {
        $this->loadEspionageReportModel();

        // TODO: add feature test for code below and check edgecases, such as when the planet has been deleted and
        // does not exist anymore. What should we show in that case?

        // Load planet by coordinate.
        $coordinate = new Coordinate($this->espionageReportModel->planet_galaxy, $this->espionageReportModel->planet_system, $this->espionageReportModel->planet_position);
        $planet = $this->planetServiceFactory->makeForCoordinate($coordinate, true, PlanetType::from($this->espionageReportModel->planet_type));

        // If planet owner is the same as the player, we load the player by planet owner which is already loaded.
        if ($this->espionageReportModel->planet_user_id === $planet->getPlayer()->getId()) {
            $player = $this->playerServiceFactory->make($planet->getPlayer()->getId());
        } else {
            // It is theoretically possible that the original player has deleted their planet and another user has
            // colonized the same position of the original planet. In that case, we should load the player by user_id
            // from the espionage report.
            $player = $this->playerServiceFactory->make($this->espionageReportModel->planet_user_id);
        }

        // Extract resources
        $resources = new Resources($this->espionageReportModel->resources['metal'], $this->espionageReportModel->resources['crystal'], $this->espionageReportModel->resources['deuterium'], $this->espionageReportModel->resources['energy']);

        // Extract debris if available.
        $debris = new Resources(0, 0, 0, 0);
        if ($this->espionageReportModel->debris !== null) {
            $debris = new Resources($this->espionageReportModel->debris['metal'], $this->espionageReportModel->debris['crystal'], $this->espionageReportModel->debris['deuterium'], 0);
        }

        // Extract ships
        $ships = [];
        if ($this->espionageReportModel->ships !== null) {
            foreach ($this->espionageReportModel->ships as $machine_name => $amount) {
                // Get object
                $unit = ObjectService::getUnitObjectByMachineName($machine_name);

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
                $unit = ObjectService::getUnitObjectByMachineName($machine_name);

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
                $unit = ObjectService::getObjectByMachineName($machine_name);

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
                $unit = ObjectService::getObjectByMachineName($machine_name);

                $unitViewModel = new UnitViewModel();
                $unitViewModel->amount = $amount;
                $unitViewModel->object = $unit;

                $research[$unit->machine_name] = $unitViewModel;
            }
        }

        return [
            'subject' => $this->getSubject(),
            'from' => $this->getFrom(),
            'playername' => $player->getUsername(),
            'resources' => $resources,
            'debris' => $debris,
            'ships' => $ships,
            'defense' => $defense,
            'buildings' => $buildings,
            'research' => $research,
        ];
    }
}
