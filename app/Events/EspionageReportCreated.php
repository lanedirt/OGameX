<?php

namespace OGame\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class EspionageReportCreated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public int $espionageReportId)
    {
    }
}
