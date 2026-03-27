<?php

namespace JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks;

use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Contracts\StackHealthCheckContract;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Data\StackHealthItemResult;

/**
 * Shows current APP_ENV; local gets an explicit “OK” suffix so staging vs prod mistakes are easier to spot.
 */
final class CfgAppEnvCheck implements StackHealthCheckContract
{
    public static function id(): string
    {
        return 'cfg_app_env';
    }

    /**
     * @return list<StackHealthItemResult>
     */
    public function run(): array
    {
        $env = (string) config('app.env');

        if (app()->environment('local')) {
            return [
                new StackHealthItemResult(
                    __('stack-health::stack-health.checks.cfg_app_env'),
                    true,
                    $env.' — '.__('stack-health::stack-health.messages.app_env_local_ok'),
                ),
            ];
        }

        return [
            new StackHealthItemResult(
                __('stack-health::stack-health.checks.cfg_app_env'),
                true,
                $env,
            ),
        ];
    }
}
