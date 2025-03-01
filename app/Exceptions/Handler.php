<?php

namespace OGame\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Spatie\DiscordAlerts\Facades\DiscordAlert;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * Taps into the render function allowing us to send to discord.
     *
     * @param  Request  $request
     *
     * @throws Throwable
     */
    public function render($request, Exception|Throwable $e): \Symfony\Component\HttpFoundation\Response
    {
        $this->sendToDiscord($e);

        return parent::render($request, $e);
    }

    /**
     * Send error to Discord, if webhook setup
     */
    protected function sendToDiscord(Throwable $e): void
    {
        if (config('app.discord_alert_webhook') && $this->shouldReport($e)) {
            $stackTrace = explode("\n", $e->getTraceAsString());

            // Limit to the first two stack trace lines
            $limitedStackTrace = implode("\n", array_slice($stackTrace, 0, 2));

            $message = sprintf(
                '🚨 **Exception in %s environment**
                ```%s```',
                app()->environment(),
                sprintf(
                    "\nMessage: %s\nFile: %s (Line: %d)\nStack Trace:\n%s\n",
                    $e->getMessage(),
                    $e->getFile(),
                    $e->getLine(),
                    $limitedStackTrace
                )
            );

            DiscordAlert::message($message);
        }
    }
}
