<?php

namespace JustGetSchwifty\LaravelStackHealth\Tests\Feature;

use JustGetSchwifty\LaravelStackHealth\Tests\TestCase;

class StackHealthDashboardAccessTest extends TestCase
{
    protected function tearDown(): void
    {
        config(['stack-health.enabled' => true]);

        parent::tearDown();
    }

    public function test_stack_health_root_returns_not_found_when_disabled_outside_local(): void
    {
        config(['stack-health.enabled' => false]);

        $this->get($this->stackHealthDashboardUri())->assertNotFound();
    }
}
