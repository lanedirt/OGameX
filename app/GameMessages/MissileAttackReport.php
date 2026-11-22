<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\GameMessage;

/**
 * Message sent to attacker after launching missiles
 */
class MissileAttackReport extends GameMessage
{
    protected function initialize(): void
    {
        $this->key = 'missile_attack_report';
        $this->params = [
            'origin_planet_id',
            'origin_planet_name',
            'origin_planet_coords',
            'target_planet_id',
            'target_planet_name',
            'target_coords',
            'target_type',
            'missiles_sent',
            'missiles_intercepted',
            'missiles_hit',
            'defenses_destroyed'
            // Note: defenses_data is not in params because it's only used in custom getBody() method
        ];
        $this->tab = 'fleets';
        $this->subtab = 'combat_reports';
    }

    /**
     * Override getBody to provide custom formatted message with hyperlinks
     */
    public function getBody(): string
    {
        $params = $this->message->params ?? [];

        $originPlanetId = $params['origin_planet_id'] ?? 0;
        $originPlanetName = $params['origin_planet_name'] ?? 'Unknown';
        $originPlanetCoords = $params['origin_planet_coords'] ?? '';

        $targetPlanetId = $params['target_planet_id'] ?? 0;
        $targetPlanetName = $params['target_planet_name'] ?? 'Unknown';
        $targetCoords = $params['target_coords'] ?? '';

        $missilesSent = $params['missiles_sent'] ?? 0;

        // Handle both old (array) and new (JSON string) format
        $defensesDataRaw = $params['defenses_data'] ?? '[]';
        if (is_array($defensesDataRaw)) {
            // Old format - already an array
            $defensesData = $defensesDataRaw;
        } else {
            // New format - JSON string
            $defensesData = json_decode($defensesDataRaw, true) ?? [];
        }

        // Singular/plural handling for missile
        $missileText = $missilesSent == 1 ? 'missile' : 'missiles';

        // Build message body with hyperlinks matching original OGame format
        // Format: "X missile(s) from your planet [Name] [Coords] smashed into the planet [Name] [Coords]!"
        $body = $missilesSent . ' ' . $missileText . ' from your planet ';

        // Planet link format: [planet]ID[/planet] automatically shows planet name and coordinates
        if ($originPlanetId > 0) {
            $body .= '[planet]' . $originPlanetId . '[/planet]';
        } else {
            $body .= $originPlanetName . ' [coordinates]' . $originPlanetCoords . '[/coordinates]';
        }

        $body .= ' smashed into the planet ';

        // Target planet link
        if ($targetPlanetId > 0) {
            $body .= '[planet]' . $targetPlanetId . '[/planet]';
        } else {
            $body .= $targetPlanetName . ' [coordinates]' . $targetCoords . '[/coordinates]';
        }

        $body .= '!';
        $body .= '<br><br>';

        // Add missile interception info (only if missiles were intercepted)
        $missilesIntercepted = $params['missiles_intercepted'] ?? 0;

        if ($missilesIntercepted > 0) {
            $body .= '<b>Missiles Intercepted:</b> ' . $missilesIntercepted . '<br><br>';
        }

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
