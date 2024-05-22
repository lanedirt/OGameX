<?php

namespace OGame\GameMessages;

use OGame\Facades\AppUtil;
use OGame\GameMessages\Abstracts\GameMessage;
use OGame\Models\Message;
use OGame\Models\Planet\Coordinate;
use OGame\Models\Resources;
use OGame\ViewModels\UnitViewModel;

class EspionageReport extends GameMessage
{
    protected function initialize(): void
    {
        $this->key = 'espionage_report';
        $this->params = [];
        $this->tab = 'fleets';
        $this->subtab = 'espionage';
    }

    /**
     * Returns the subject of the message.
     *
     * @param Message $message
     * @return string
     */
    public function getSubject(Message $message): string
    {
        // Load the planet name from the references table and return the subject filled with the planet name.
        // TODO: we are loading the espionage report twice in this class, once in getSubject and once in getBody. We should
        // load it once and pass it to both methods.
        $espionageReport = \OGame\Models\EspionageReport::where('id', $message->espionage_report_id)->first();
        if (!$espionageReport) {
            return __('Espionage report not found');
        }

        // Load planet by coordinate.
        $coordinate = new Coordinate($espionageReport->planet_galaxy, $espionageReport->planet_system, $espionageReport->planet_position);
        $planet = $this->planetServiceFactory->makeForCoordinate($coordinate);
        $subject = '';
        if ($planet) {
            $subject = __('Espionage report from :planet', ['planet' => '[planet]' . $planet->getPlanetId() . '[/planet]']);
        } else {
            $subject = __('Espionage report from :planet', ['planet' => '[coordinates]' . $coordinate->asString() . '[/coordinates]']);
        }

        return $this->replacePlaceholders($subject);
    }

    /**
     * Get the body of the message filled with provided params.
     *
     * @param Message $message
     * @return string
     */
    public function getBody(Message $message): string
    {
        $params = $this->getEspionageReportParams($message);
        return view('ingame.messages.templates.espionage_report', $params)->render();
    }

    /**
     * Get the body of the message filled with provided params.
     *
     * @param Message $message
     * @return string
     */
    public function getBodyFull(Message $message): string
    {
        $params = $this->getEspionageReportParams($message);
        return view('ingame.messages.templates.espionage_report_full', $params)->render();
    }

    /**
     * @inheritdoc
     */
    public function getFooterDetails(Message $message): string
    {
        // Show more details link in the footer of the espionage report.
        return ' <a class="fright txt_link msg_action_link overlay"
                   href="' . route('messages.showMessage', ['messageId' => $message->id])  .'"
                   data-overlay-title="More details">
                    More details
                </a>';
    }

    /**
     * Get the espionage report params.
     *
     * @param Message $message
     * @return array
     */
    private function getEspionageReportParams(Message $message): array
    {
        // Dynamically define all params that are required for this message by loading them from the
        // references espionage_report record.
        $espionageReport = \OGame\Models\EspionageReport::where('id', $message->espionage_report_id)->first();
        if (!$espionageReport) {
            return __('Espionage report not found');
        }

        // TODO: add feature test for code below and check edgecases, such as when the planet has been deleted and
        // does not exist anymore. What should we show in that case?

        // Load planet by coordinate.
        $coordinate = new Coordinate($espionageReport->planet_galaxy, $espionageReport->planet_system, $espionageReport->planet_position);
        $planet = $this->planetServiceFactory->makeForCoordinate($coordinate);

        // Load player by player_id.
        $player = $this->playerServiceFactory->make($espionageReport->planet_user_id);

        // Extract resources
        $resources = new Resources($espionageReport->resources['metal'], $espionageReport->resources['crystal'], $espionageReport->resources['deuterium'], $espionageReport->resources['energy']);

        // Extract ships
        $ships = [];
        if ($espionageReport->ships !== null) {
            foreach ($espionageReport->ships as $machine_name => $amount) {
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
        if ($espionageReport->defense !== null) {
            foreach ($espionageReport->defense as $machine_name => $amount) {
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
        if ($espionageReport->buildings !== null) {
            foreach ($espionageReport->buildings as $machine_name => $amount) {
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
        if ($espionageReport->research !== null) {
            foreach ($espionageReport->research as $machine_name => $amount) {
                // Get object
                $unit = $planet->objects->getObjectByMachineName($machine_name);

                $unitViewModel = new UnitViewModel();
                $unitViewModel->amount = $amount;
                $unitViewModel->object = $unit;

                $research[$unit->machine_name] = $unitViewModel;
            }
        }

        // TODO: for espionage_report we do not want to rely on simple language files as what we show is too complex.
        // We want to show a table with all the information in a nice way and therefore override the default behavior
        // and use a blade template for this.
        return [
            'subject' => $this->getSubject($message),
            'from' => $this->getFrom(),
            'playername' => $player->getUsername(),
            'metal' => $resources->metal->getFormatted(),
            'crystal' => $resources->crystal->getFormatted(),
            'deuterium' => $resources->deuterium->getFormatted(),
            'energy' => $resources->energy->getFormatted(),
            'resources_sum' => AppUtil::formatNumber($resources->sum()),
            'ships' => $ships,
            'defense' => $defense,
            'buildings' => $buildings,
            'research' => $research,
        ];
    }
}
