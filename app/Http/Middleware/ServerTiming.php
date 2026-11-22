<?php

namespace OGame\Http\Middleware;

use Closure;

use function defined;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ServerTiming
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): Response $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Add server timing metrics for performance monitoring.
        $processingTime = defined('LARAVEL_START') ? round((microtime(true) - LARAVEL_START) * 1000, 2) : 0;
        $response->headers->set('Server-Timing', 'app;dur=' . $processingTime . ', cdn;desc="miss", origin;desc="local"');

        return $response;
    }
}
