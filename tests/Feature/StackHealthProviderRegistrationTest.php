<?php

namespace JustGetSchwifty\LaravelStackHealth\Tests\Feature;

use JustGetSchwifty\LaravelStackHealth\Tests\TestCase;

class StackHealthProviderRegistrationTest extends TestCase
{
    public function test_provider_registers_default_route_and_middleware_alias(): void
    {
        $route = app('router')->getRoutes()->getByName('stack-health.dashboard');

        $this->assertNotNull($route);
        $this->assertSame('healthcheck', $route->uri());
        $this->assertContains('stack.health', $route->gatherMiddleware());
    }
}
