<?php

namespace JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks;

use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Contracts\StackHealthCheckContract;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Data\StackHealthItemResult;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\StackHealthMessageRedactor;
use Illuminate\Support\Facades\Redis;
use Throwable;

/**
 * Pings the default Redis connection; accepts common PONG variants returned by different clients.
 */
final class RedisCheck implements StackHealthCheckContract
{
    public function __construct(
        private StackHealthMessageRedactor $redactor,
    ) {}

    public static function id(): string
    {
        return 'redis';
    }

    /**
     * @return list<StackHealthItemResult>
     */
    public function run(): array
    {
        try {
            $pong = Redis::connection()->ping();
            $ok = $pong === true || $pong === '+PONG' || $pong === 'PONG';

            return [
                new StackHealthItemResult(
                    __('stack-health::stack-health.checks.redis'),
                    $ok,
                    $ok
                        ? __('stack-health::stack-health.messages.redis_ping_ok')
                        : __('stack-health::stack-health.messages.redis_unexpected', ['response' => json_encode($pong)]),
                ),
            ];
        } catch (Throwable $e) {
            return [
                new StackHealthItemResult(
                    __('stack-health::stack-health.checks.redis'),
                    false,
                    $this->redactor->exceptionMessageForUi($e, 'stack-health::stack-health.messages.error_redis'),
                ),
            ];
        }
    }
}
