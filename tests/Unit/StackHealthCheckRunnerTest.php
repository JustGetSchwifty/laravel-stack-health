<?php

namespace JustGetSchwifty\LaravelStackHealth\Tests\Unit;

use Exception;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Contracts\StackHealthCheckContract;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Data\StackHealthItemResult;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\StackHealthCheckRunner;
use JustGetSchwifty\LaravelStackHealth\Tests\TestCase;

class StackHealthCheckRunnerTest extends TestCase
{
    public function test_runner_returns_fallback_result_when_check_throws(): void
    {
        $runner = new StackHealthCheckRunner();
        $check = new class implements StackHealthCheckContract
        {
            public static function id(): string
            {
                return 'throwing_check';
            }

            public function run(): array
            {
                throw new Exception('boom');
            }
        };

        $results = $runner->run($check);

        $this->assertCount(1, $results);
        $this->assertInstanceOf(StackHealthItemResult::class, $results[0]);
        $this->assertFalse($results[0]->ok);
    }
}
