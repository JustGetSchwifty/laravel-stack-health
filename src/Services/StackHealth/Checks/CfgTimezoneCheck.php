<?php

namespace JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks;

use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Contracts\StackHealthCheckContract;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Data\StackHealthItemResult;

/**
 * Surfaces configured app timezone string for cross-check with OS and DB session TZ.
 */
final class CfgTimezoneCheck implements StackHealthCheckContract
{
    public static function id(): string
    {
        return 'cfg_timezone';
    }

    /**
     * @return list<StackHealthItemResult>
     */
    public function run(): array
    {
        return [
            new StackHealthItemResult(
                __('stack-health::stack-health.checks.cfg_timezone'),
                true,
                __('stack-health::stack-health.messages.cfg_timezone', ['tz' => config('app.timezone')]),
            ),
        ];
    }
}
