<?php

namespace JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks;

use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Contracts\StackHealthCheckContract;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Data\StackHealthItemResult;

/**
 * Summarizes session driver and encryption flag from config.
 */
final class CfgSessionCheck implements StackHealthCheckContract
{
    public static function id(): string
    {
        return 'cfg_session';
    }

    /**
     * @return list<StackHealthItemResult>
     */
    public function run(): array
    {
        return [
            new StackHealthItemResult(
                __('stack-health::stack-health.checks.cfg_session'),
                true,
                __('stack-health::stack-health.messages.cfg_session', [
                    'driver' => config('session.driver'),
                    'encrypt' => config('session.encrypt') ? 'true' : 'false',
                ]),
            ),
        ];
    }
}
