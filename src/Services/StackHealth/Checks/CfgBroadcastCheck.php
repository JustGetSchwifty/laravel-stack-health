<?php

namespace JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks;

use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Contracts\StackHealthCheckContract;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Data\StackHealthItemResult;

/**
 * Shows default broadcasting connection from config.
 */
final class CfgBroadcastCheck implements StackHealthCheckContract
{
    public static function id(): string
    {
        return 'cfg_broadcast';
    }

    /**
     * @return list<StackHealthItemResult>
     */
    public function run(): array
    {
        return [
            new StackHealthItemResult(
                __('stack-health::stack-health.checks.cfg_broadcast'),
                true,
                __('stack-health::stack-health.messages.cfg_broadcast', ['connection' => config('broadcasting.default')]),
            ),
        ];
    }
}
