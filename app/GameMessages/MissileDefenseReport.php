<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\GameMessage;

/**
 * Message sent to defender after being attacked by missiles
 */
class MissileDefenseReport extends GameMessage
{
    protected function initialize(): void
    {
        $this->key = 'missile_defense_report';
        $this->params = [
            'attacker_name',
            'planet_id',
            'planet_name',
            'planet_coords',
            'missiles_incoming',
            'missiles_intercepted',
            'missiles_hit',
            'defenses_destroyed'
            // Note: defenses_data is not in params because it's only used in custom getBody() method
        ];
        $this->tab = 'fleets';
        $this->subtab = 'combat_reports';
    }

    /**
     * Override getBody to provide custom formatted message with defense list
     */
    public function getBody(): string
    {
        $params = $this->message->params ?? [];

        $attackerName = $params['attacker_name'] ?? 'Unknown';
        $planetId = $params['planet_id'] ?? 0;
        $planetName = $params['planet_name'] ?? 'Unknown';
        $planetCoords = $params['planet_coords'] ?? '';
        $missilesIncoming = $params['missiles_incoming'] ?? 0;
        $missilesIntercepted = $params['missiles_intercepted'] ?? 0;
        $missilesHit = $params['missiles_hit'] ?? 0;

        // Handle both old (array) and new (JSON string) format
        $defensesDataRaw = $params['defenses_data'] ?? '[]';
        if (is_array($defensesDataRaw)) {
            // Old format - already an array
            $defensesData = $defensesDataRaw;
        } else {
            // New format - JSON string
            $defensesData = json_decode($defensesDataRaw, true) ?? [];
        }

        // Build message body
        $body = 'Your planet ';

        // Planet link
        if ($planetId > 0) {
            $body .= '[planet]' . $planetId . '[/planet]';
        } else {
            $body .= $planetName . ' [coordinates]' . $planetCoords . '[/coordinates]';
        }

        $body .= ' has been attacked by interplanetary missiles from <b>' . $attackerName . '</b>!';
        $body .= '<br><br>';

        // Missile statistics
        $body .= '<b>Incoming Missiles:</b> ' . $missilesIncoming . '<br>';
        if ($missilesIntercepted > 0) {
            $body .= '<b>Missiles Intercepted:</b> ' . $missilesIntercepted . '<br>';
        }
        $body .= '<br>';

        // Add defense list with bold header
        $body .= '<b>Defenses Hit</b><br>';

        if (!empty($defensesData)) {
            foreach ($defensesData as $defenseInfo) {
                $name = $defenseInfo['name'];
                $after = $defenseInfo['after'];
                $destroyed = $defenseInfo['destroyed'];

                // Format: Defense Name [spaces] Remaining(-Destroyed)
                $body .= '<span style="display:inline-block;min-width:200px;">' . $name . '</span>';
                $body .= $after . '(-' . $destroyed . ')<br>';
            }
        } else {
            $body .= 'None<br>';
        }

        return $this->replacePlaceholders($body);
    }
}
