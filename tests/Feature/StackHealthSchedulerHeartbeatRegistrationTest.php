<?php

namespace JustGetSchwifty\LaravelStackHealth\Tests\Feature;

use Illuminate\Console\Scheduling\Schedule;
use JustGetSchwifty\LaravelStackHealth\Tests\TestCase;

class StackHealthSchedulerHeartbeatRegistrationTest extends TestCase
{
    public function test_package_registers_scheduler_heartbeat_event(): void
    {
        $schedule = $this->app->make(Schedule::class);

        $descriptions = array_map(
            static fn ($event): ?string => $event->description ?? null,
            $schedule->events()
        );

        $this->assertContains('stack-health-scheduler-heartbeat', $descriptions);
    }

    public function test_scheduler_heartbeat_registration_can_be_disabled_via_config(): void
    {
        config(['stack-health.register_scheduler_heartbeat' => false]);

        $schedule = $this->app->make(Schedule::class);

        $descriptions = array_map(
            static fn ($event): ?string => $event->description ?? null,
            $schedule->events()
        );

        $this->assertNotContains('stack-health-scheduler-heartbeat', $descriptions);
    }
}
