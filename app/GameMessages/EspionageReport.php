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
     * Load espionage report model from database. If already loaded, return the cached model.
     *
     * @return \OGame\Models\EspionageReport
     */
    private function loadEspionageReportModel(): \OGame\Models\EspionageReport
    {
        if ($this->espionageReportModel !== null) {
            // Already loaded.
            return $this->espionageReportModel;
        }

        // Load espionage report model from database associated with the message.
        $espionageReport = \OGame\Models\EspionageReport::where('id', $this->message->espionage_report_id)->first();
        if ($espionageReport === null) {
            // If espionage report is not found, we use an empty model. This is for testing purposes.
            $this->espionageReportModel = new \OGame\Models\EspionageReport();
        } else {
            $this->espionageReportModel = $espionageReport;
        }

        return $this->espionageReportModel;
    }

    /**
     * @inheritdoc
     */
    public function getSubject(): string
    {
        $espionageReportModel = $this->loadEspionageReportModel();

        // Load the planet name from the references table and return the subject filled with the planet name.
        $coordinate = new Coordinate($espionageReportModel->planet_galaxy, $espionageReportModel->planet_system, $espionageReportModel->planet_position);
        $planet = $this->planetServiceFactory->makeForCoordinate($coordinate, true, PlanetType::from($espionageReportModel->planet_type));
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
        $espionageReportModel = $this->loadEspionageReportModel();

        // Load planet by coordinate.
        $coordinate = new Coordinate($espionageReportModel->planet_galaxy, $espionageReportModel->planet_system, $espionageReportModel->planet_position);
        $planet = $this->planetServiceFactory->makeForCoordinate($coordinate, true, PlanetType::from($espionageReportModel->planet_type));

        if ($planet === null) {
            return __('Planet has been deleted and espionage report is no longer available.');
        }

        $params = $this->getEspionageReportParams();
        return view('ingame.messages.templates.espionage_report', $params)->render();
    }

    /**
     * @inheritdoc
     */
    public function getBodyFull(): string
    {
        $espionageReportModel = $this->loadEspionageReportModel();

        // Load planet by coordinate.
        $coordinate = new Coordinate($espionageReportModel->planet_galaxy, $espionageReportModel->planet_system, $espionageReportModel->planet_position);
        $planet = $this->planetServiceFactory->makeForCoordinate($coordinate, true, PlanetType::from($espionageReportModel->planet_type));

        if ($planet === null) {
            return __('Planet has been deleted and espionage report is no longer available.');
        }

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
                   href="' . $this->getFullMessageUrl() . '"
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
        // Sanity check to make sure the espionage report model is loaded.
        $espionageReportModel = $this->loadEspionageReportModel();

        // TODO: add feature test for code below and check edgecases, such as when the planet has been deleted and
        // does not exist anymore. What should we show in that case?

        // Load planet by coordinate.
        $coordinate = new Coordinate($espionageReportModel->planet_galaxy, $espionageReportModel->planet_system, $espionageReportModel->planet_position);
        $planet = $this->planetServiceFactory->makeForCoordinate($coordinate, true, PlanetType::from($espionageReportModel->planet_type));

        // If planet owner is the same as the player, we load the player by planet owner which is already loaded.
        $planetPlayer = $planet?->getPlayer();
        if ($planetPlayer !== null && $espionageReportModel->planet_user_id === $planetPlayer->getId()) {
            $player = $this->playerServiceFactory->make($planetPlayer->getId());
        } else {
            // It is theoretically possible that the original player has deleted their planet and another user has
            // colonized the same position of the original planet. In that case, we should load the player by user_id
            // from the espionage report.
            $player = $this->playerServiceFactory->make($espionageReportModel->planet_user_id);
        }

        // Extract resources
        $resources = new Resources($espionageReportModel->resources['metal'] ?? 0, $espionageReportModel->resources['crystal'] ?? 0, $espionageReportModel->resources['deuterium'] ?? 0, $espionageReportModel->resources['energy'] ?? 0);

        // Extract debris if available.
        $debris = new Resources(0, 0, 0, 0);
        if ($espionageReportModel->debris !== null) {
            $debris = new Resources($espionageReportModel->debris['metal'] ?? 0, $espionageReportModel->debris['crystal'] ?? 0, $espionageReportModel->debris['deuterium'] ?? 0, 0);
        }

        // Extract ships
        $ships = [];
        if ($espionageReportModel->ships !== null) {
            foreach ($espionageReportModel->ships as $machine_name => $amount) {
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
        if ($espionageReportModel->defense !== null) {
            foreach ($espionageReportModel->defense as $machine_name => $amount) {
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
        if ($espionageReportModel->buildings !== null) {
            foreach ($espionageReportModel->buildings as $machine_name => $amount) {
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
        if ($espionageReportModel->research !== null) {
            foreach ($espionageReportModel->research as $machine_name => $amount) {
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
            'date' => $this->getDateFormatted(),
            'resources' => $resources,
            'debris' => $debris,
            'ships' => $ships,
            'defense' => $defense,
            'buildings' => $buildings,
            'research' => $research,
            'counter_espionage_chance' => $espionageReportModel->counter_espionage_chance ?? 0,
            'galaxy' => $espionageReportModel->planet_galaxy,
            'system' => $espionageReportModel->planet_system,
            'position' => $espionageReportModel->planet_position,
            'planet_type' => $espionageReportModel->planet_type,
        ];
    }
}
