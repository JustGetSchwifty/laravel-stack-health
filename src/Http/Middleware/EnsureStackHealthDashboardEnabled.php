<?php

namespace JustGetSchwifty\LaravelStackHealth\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Hides the dashboard outside local unless {@see config('stack-health.enabled')} is true, to avoid exposing internals publicly.
 */
class EnsureStackHealthDashboardEnabled
{
    /**
     * @param  \Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isLocal = app()->environment('local')
            || (string) config('app.env') === 'local';

        $enabled = $isLocal
            || filter_var(config('stack-health.enabled'), FILTER_VALIDATE_BOOL);

        if (! $enabled) {
            abort(404);
        }

        return $next($request);
    }
}
