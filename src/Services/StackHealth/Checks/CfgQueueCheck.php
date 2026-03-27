<?php

namespace JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks;

use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Contracts\StackHealthCheckContract;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Data\StackHealthItemResult;

/**
 * Shows default queue connection label from config.
 */
final class CfgQueueCheck implements StackHealthCheckContract
{
    public static function id(): string
    {
        return 'cfg_queue';
    }

    /**
     * @return list<StackHealthItemResult>
     */
    public function run(): array
    {
        return [
            new StackHealthItemResult(
                __('stack-health::stack-health.checks.cfg_queue'),
                true,
                __('stack-health::stack-health.messages.cfg_queue', ['connection' => config('queue.default')]),
            ),
        ];
    }
}
