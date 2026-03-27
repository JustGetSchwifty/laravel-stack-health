<?php

namespace JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks;

use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Contracts\StackHealthCheckContract;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Data\StackHealthItemResult;

/**
 * Displays default logging channel name.
 */
final class CfgLogCheck implements StackHealthCheckContract
{
    public static function id(): string
    {
        return 'cfg_log';
    }

    /**
     * @return list<StackHealthItemResult>
     */
    public function run(): array
    {
        return [
            new StackHealthItemResult(
                __('stack-health::stack-health.checks.cfg_log'),
                true,
                __('stack-health::stack-health.messages.cfg_log', ['channel' => config('logging.default')]),
            ),
        ];
    }
}
