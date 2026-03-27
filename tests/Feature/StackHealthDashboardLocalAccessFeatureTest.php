<?php

namespace JustGetSchwifty\LaravelStackHealth\Tests\Feature;

use JustGetSchwifty\LaravelStackHealth\Tests\TestCase;

class StackHealthDashboardLocalAccessFeatureTest extends TestCase
{
    protected function tearDown(): void
    {
        putenv('APP_ENV=testing');
        $_ENV['APP_ENV'] = 'testing';
        $_SERVER['APP_ENV'] = 'testing';

        parent::tearDown();
    }

    public function test_dashboard_is_reachable_in_local_even_when_disabled_flag_is_false(): void
    {
        putenv('APP_ENV=local');
        $_ENV['APP_ENV'] = 'local';
        $_SERVER['APP_ENV'] = 'local';
        config(['app.env' => 'local', 'stack-health.enabled' => false]);

        $this->get(config('stack-health.dashboard_uri'))->assertOk();
    }
}
