<?php

namespace JustGetSchwifty\LaravelStackHealth\Tests\Unit;

use JustGetSchwifty\LaravelStackHealth\Listeners\DiagnoseStackHealthForUpEndpoint;
use Illuminate\Foundation\Events\DiagnosingHealth;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use JustGetSchwifty\LaravelStackHealth\Tests\TestCase;

class DiagnoseStackHealthForUpEndpointTest extends TestCase
{
    public function test_listener_skips_database_probe_when_up_monitoring_is_disabled(): void
    {
        config(['stack-health.up_monitoring_enabled' => false]);
        DB::shouldReceive('connection')->never();
        DB::shouldReceive('select')->never();

        (new DiagnoseStackHealthForUpEndpoint)->handle(new DiagnosingHealth);

        $this->addToAssertionCount(1);
    }

    public function test_listener_runs_database_probe_when_up_monitoring_is_enabled(): void
    {
        config(['stack-health.up_monitoring_enabled' => true]);
        DB::shouldReceive('connection->getPdo')->once()->andReturn(new \stdClass);
        DB::shouldReceive('select')->once()->with('select 1')->andReturn([['ok' => 1]]);

        (new DiagnoseStackHealthForUpEndpoint)->handle(new DiagnosingHealth);

        $this->addToAssertionCount(1);
    }

    public function test_listener_throws_when_database_probe_fails(): void
    {
        config(['stack-health.up_monitoring_enabled' => true]);
        DB::shouldReceive('connection')->once()->andThrow(new RuntimeException('db unavailable'));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('db unavailable');

        (new DiagnoseStackHealthForUpEndpoint)->handle(new DiagnosingHealth);
    }
}
