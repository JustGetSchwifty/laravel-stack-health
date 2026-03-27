<?php

namespace JustGetSchwifty\LaravelStackHealth\Tests\Feature;

use JustGetSchwifty\LaravelStackHealth\Tests\TestCase;

/**
 * Verifies {@see STACK_HEALTH_PATH} is applied when the application boots (route registration matches config).
 */
class StackHealthDashboardPathFeatureTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        app()->setLocale('en');
        @file_put_contents(storage_path('app/.scheduler-heartbeat'), (string) time());
    }

    public function test_stack_health_is_served_under_custom_path(): void
    {
        config(['stack-health.enabled' => true]);

        $this->assertSame('/healthcheck', config('stack-health.dashboard_uri'));

        $this->get('/healthcheck')
            ->assertOk()
            ->assertSee(__('stack-health::stack-health.heading'), false);

        $this->get('/')->assertNotFound();
    }
}
