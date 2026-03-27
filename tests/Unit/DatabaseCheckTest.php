<?php

namespace JustGetSchwifty\LaravelStackHealth\Tests\Unit;

use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks\DatabaseCheck;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\StackHealthMessageRedactor;
use Illuminate\Support\Facades\DB;
use JustGetSchwifty\LaravelStackHealth\Tests\TestCase;

class DatabaseCheckTest extends TestCase
{
    public function test_passes_when_database_query_succeeds(): void
    {
        config(['stack-health.redact_sensitive_messages' => false]);

        $check = new DatabaseCheck(app(StackHealthMessageRedactor::class));
        $items = $check->run();

        $this->assertCount(1, $items);
        $this->assertTrue($items[0]->ok);
        $this->assertStringContainsString('sqlite', strtolower($items[0]->message));
    }

    public function test_fails_when_connection_throws(): void
    {
        config(['stack-health.redact_sensitive_messages' => false]);

        DB::shouldReceive('connection')->once()->andThrow(new \RuntimeException('connection refused'));

        $check = new DatabaseCheck(app(StackHealthMessageRedactor::class));
        $items = $check->run();

        $this->assertCount(1, $items);
        $this->assertFalse($items[0]->ok);
        $this->assertStringContainsString('connection refused', $items[0]->message);
    }
}
