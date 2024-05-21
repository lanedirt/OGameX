<?php

namespace OGame\GameMessages;

use Illuminate\Contracts\Container\BindingResolutionException;
use OGame\Facades\AppUtil;
use OGame\GameMessages\Abstracts\GameMessage;
use OGame\Models\Message;
use OGame\Models\Planet\Coordinate;
use OGame\Models\Resources;

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
        }
        else {
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

        // TODO: for espionage_report we do not want to rely on simple language files as what we show is too complex.
        // We want to show a table with all the information in a nice way and therefore override the default behavior
        // and use a blade template for this.

        return view('ingame.messages.templates.espionage_report', [
            'playername' => $player->getUsername(),
            'metal' => $resources->metal->getFormatted(),
            'crystal' => $resources->crystal->getFormatted(),
            'deuterium' => $resources->deuterium->getFormatted(),
            'resources_sum' => AppUtil::formatNumber($resources->sum()),
        ])->render();
    }
}
