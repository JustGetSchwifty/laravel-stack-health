<?php

namespace JustGetSchwifty\LaravelStackHealth\Tests\Unit;

use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\StackHealthDashboardRouteCollisionDetector;
use Illuminate\Routing\Router;
use LogicException;
use JustGetSchwifty\LaravelStackHealth\Tests\TestCase;

class StackHealthDashboardRouteCollisionDetectorTest extends TestCase
{
    public function test_passes_when_no_conflicting_get_route(): void
    {
        $router = $this->app->make(Router::class);
        $router->get('unique-stack-health-collision-path', fn () => 'noop');

        StackHealthDashboardRouteCollisionDetector::assertGetUriAvailable($router, 'other-free-path');

        $this->addToAssertionCount(1);
    }

    public function test_throws_when_get_route_already_uses_same_uri(): void
    {
        $router = $this->app->make(Router::class);
        $router->get('duplicate-uri-check', fn () => 'noop');

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('duplicate-uri-check');

        StackHealthDashboardRouteCollisionDetector::assertGetUriAvailable($router, 'duplicate-uri-check');
    }

    public function test_throws_for_root_when_slash_route_exists(): void
    {
        $router = $this->app->make(Router::class);
        $router->get('/', fn () => 'home');

        $this->expectException(LogicException::class);

        StackHealthDashboardRouteCollisionDetector::assertGetUriAvailable($router, '/');
    }

    public function test_ignores_fallback_routes(): void
    {
        $router = $this->app->make(Router::class);
        $router->fallback(fn () => 'fallback');

        StackHealthDashboardRouteCollisionDetector::assertGetUriAvailable($router, 'not-fallback');

        $this->addToAssertionCount(1);
    }
}
