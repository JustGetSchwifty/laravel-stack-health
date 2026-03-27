<?php

namespace JustGetSchwifty\LaravelStackHealth\Tests\Feature;

use JustGetSchwifty\LaravelStackHealth\Tests\TestCase;

class StackHealthDisabledChecksEnvFeatureTest extends TestCase
{
    public function test_disabled_check_from_env_is_not_rendered(): void
    {
        config([
            'stack-health.enabled' => true,
            'stack-health.checks_disabled' => ['php_version'],
        ]);

        $response = $this->get(config('stack-health.dashboard_uri'));

        $response->assertOk();
        $response->assertDontSee(__('stack-health::stack-health.checks.php_version'), false);
    }
}
