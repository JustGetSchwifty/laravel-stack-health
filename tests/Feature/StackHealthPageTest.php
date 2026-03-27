<?php

namespace JustGetSchwifty\LaravelStackHealth\Tests\Feature;

use JustGetSchwifty\LaravelStackHealth\Tests\TestCase;

class StackHealthPageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        app()->setLocale('en');
        @file_put_contents(storage_path('app/.scheduler-heartbeat'), (string) time());
    }

    public function test_stack_health_page_is_reachable_when_flag_enabled(): void
    {
        config(['stack-health.enabled' => true]);

        $response = $this->get($this->stackHealthDashboardUri());

        $response->assertOk();
        $response->assertSee(__('stack-health::stack-health.heading'), false);
        $response->assertSee(__('stack-health::stack-health.summary.checks_passed'), false);
        $response->assertSee(__('stack-health::stack-health.sections.configuration'), false);
        $response->assertSee(__('stack-health::stack-health.checks.cfg_config_file_cache'), false);
        $response->assertSee(__('stack-health::stack-health.checks.database'), false);
        $response->assertSee(__('stack-health::stack-health.checks.task_scheduler'), false);
        $response->assertSee(__('stack-health::stack-health.checks.outbound_guzzle'), false);
    }

    public function test_dashboard_route_is_registered_with_expected_default_path(): void
    {
        $route = app('router')->getRoutes()->getByName('stack-health.dashboard');

        $this->assertNotNull($route);
        $this->assertSame('healthcheck', $route->uri());
    }
}
