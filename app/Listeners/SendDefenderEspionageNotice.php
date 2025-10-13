<?php

namespace OGame\Listeners;

use OGame\Events\EspionageReportCreated;
use OGame\Models\Message;
use OGame\Models\User;

class SendDefenderEspionageNotice
{
    public function handle(EspionageReportCreated $event): void
    {
        // Find the attacker's espionage message for this report
        $attackerMsg = Message::where('espionage_report_id', $event->espionageReportId)
                              ->where('key', 'espionage_report')
                              ->orderByDesc('id')
                              ->first();
        if (!$attackerMsg) {
            return; // nothing to mirror -> bail safely
        }

        // Attacker & defender
        $attacker = User::find($attackerMsg->user_id);
        $attackerName = $attacker?->username ?? 'Unknown';

        // Read coords + defender from the espionage_report row via the attacker's message link
        // (Message::espionage_report_id is the foreign key; pull coordinates from the report params on that message)
        // The EspionageReport GameMessage stores coords on the related DB row and shows them; for the defender notice
        // we send raw "g:s:p" and let the message class wrap into [coordinates]…[/coordinates].
        $reportId = (int)$attackerMsg->espionage_report_id;

        // Pull coords straight from the espionage_reports table via a tiny query to avoid model name guessing
        $row = \DB::table('espionage_reports')->where('id', $reportId)->first();
        if (!$row) {
            return;
        }
        $coords = sprintf('%d:%d:%d', (int)$row->planet_galaxy, (int)$row->planet_system, (int)$row->planet_position);

        // Defender is the planet owner on the report row
        $defender = User::find($row->planet_user_id);
        if (!$defender) {
            return;
        }

        // Create defender-facing message (key + params = shows under Fleets → Espionage)
        $msg = new Message();
        $msg->user_id = $defender->id;
        $msg->key     = 'espionage_detected';
        $msg->params  = [
            'attacker' => $attackerName,
            'coords'   => $coords,
        ];
        $msg->save();
    }
}
