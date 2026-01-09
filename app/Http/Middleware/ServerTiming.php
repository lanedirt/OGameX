<?php

namespace OGame\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ServerTiming
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Add server timing metrics for performance monitoring.
        if (method_exists($response, 'header')) {
            $processingTime = defined('LARAVEL_START') ? round((microtime(true) - LARAVEL_START) * 1000, 2) : 0;
            $response->header('Server-Timing', 'app;dur=' . $processingTime . ', cdn;desc="miss", origin;desc="local"');
        }

        return $response;
    }
}
