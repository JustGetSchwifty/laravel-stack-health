<?php

namespace JustGetSchwifty\LaravelStackHealth\Tests\Feature;

use JustGetSchwifty\LaravelStackHealth\Tests\TestCase;

class StackHealthDashboardRouteConfigFeatureTest extends TestCase
{
    public function test_dashboard_route_uses_configured_route_name(): void
    {
        config(['stack-health.enabled' => true]);
        $route = app('router')->getRoutes()->getByName('stack-health.dashboard');

        $this->assertNotNull($route);
        $this->assertSame('healthcheck', $route->uri());
    }
}
