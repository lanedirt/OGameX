<?php

namespace OGame\Listeners;

use Illuminate\Support\Facades\DB;
use OGame\Events\EspionageReportCreated;
use OGame\Models\Message;
use OGame\Models\User;

final class SendDefenderEspionageNotice
{
    /**
     * Handle the event when an espionage report is created.
     *
     * @param \OGame\Events\EspionageReportCreated $event
     */
    public function handle(EspionageReportCreated $event): void
    {
        /** @var Message|null $attackerMsg */
        $attackerMsg = Message::query()
            ->where('espionage_report_id', $event->espionageReportId)
            ->where('key', 'espionage_report')
            ->orderByDesc('id')
            ->first();

        if ($attackerMsg === null) {
            return;
        }

        /** @var User|null $attacker */
        $attacker = User::find($attackerMsg->user_id);
        $attackerName = $attacker ? $attacker->username : 'Unknown';

        $reportId = (int) $attackerMsg->espionage_report_id;

        /** @var object{planet_galaxy:int,planet_system:int,planet_position:int,planet_user_id:int}|null $row */
        $row = DB::table('espionage_reports')->where('id', $reportId)->first();
        if ($row === null) {
            return;
        }

        /** @var User|null $defender */
        $defender = User::find($row->planet_user_id);
        if ($defender === null) {
            return;
        }

        // raw coords (g:s:p)
        $coords = sprintf('%d:%d:%d', (int) $row->planet_galaxy, (int) $row->planet_system, (int) $row->planet_position);

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
