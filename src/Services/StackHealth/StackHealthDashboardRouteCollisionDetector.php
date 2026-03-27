<?php

namespace JustGetSchwifty\LaravelStackHealth\Services\StackHealth;

use Illuminate\Routing\Router;
use LogicException;

/**
 * Fails fast before registering the stack-health GET route when another route already owns the same URI.
 *
 * Laravel's {@see \Illuminate\Routing\RouteCollection} overwrites earlier routes for the same method+URI key,
 * which hides misconfiguration; this detector surfaces clashes at boot instead.
 */
final class StackHealthDashboardRouteCollisionDetector
{
    /**
     * @param  string  $routePath  Same value passed to {@see Router::get()} for the dashboard (e.g. "/" or "healthcheck").
     *
     * @throws LogicException When a non-fallback GET route without a custom domain already uses this path.
     */
    public static function assertGetUriAvailable(Router $router, string $routePath): void
    {
        $target = self::normalizeComparableUri($routePath);

        foreach ($router->getRoutes() as $existing) {
            if ($existing->isFallback) {
                continue;
            }

            if ($existing->getDomain() !== null && $existing->getDomain() !== '') {
                continue;
            }

            if (! in_array('GET', $existing->methods(), true)) {
                continue;
            }

            if (self::normalizeComparableUri($existing->uri()) !== $target) {
                continue;
            }

            $action = $existing->getAction();
            $uses = $action['uses'] ?? $action['controller'] ?? null;
            $actionLabel = match (true) {
                is_string($uses) => $uses,
                $uses instanceof \Closure => 'Closure',
                is_object($uses) => $uses::class,
                default => json_encode($uses),
            };

            throw new LogicException(
                sprintf(
                    'Stack health dashboard cannot register GET [%s]: that URI is already taken (existing route URI [%s], action [%s]). Change STACK_HEALTH_PATH or remove the conflicting route.',
                    $routePath === '/' ? '/' : $routePath,
                    $existing->uri(),
                    $actionLabel
                )
            );
        }
    }

    /**
     * Normalize URIs the same way operators think about them: root vs path segments, ignoring outer slashes.
     */
    private static function normalizeComparableUri(string $uri): string
    {
        $t = trim($uri);

        if ($t === '' || $t === '/') {
            return '/';
        }

        return trim($t, '/');
    }
}
