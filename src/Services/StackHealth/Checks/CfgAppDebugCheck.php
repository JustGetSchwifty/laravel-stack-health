<?php

namespace JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks;

use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Contracts\StackHealthCheckContract;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Data\StackHealthItemResult;

/**
 * Flags APP_DEBUG in non-local environments because it leaks stack traces and hurts performance.
 */
final class CfgAppDebugCheck implements StackHealthCheckContract
{
    public static function id(): string
    {
        return 'cfg_app_debug';
    }

    /**
     * @return list<StackHealthItemResult>
     */
    public function run(): array
    {
        $debug = (bool) config('app.debug');

        if (app()->environment('local')) {
            return [
                new StackHealthItemResult(
                    __('stack-health::stack-health.checks.cfg_app_debug'),
                    true,
                    $debug
                        ? __('stack-health::stack-health.messages.app_debug_true_local')
                        : __('stack-health::stack-health.messages.app_debug_false'),
                ),
            ];
        }

        if ($debug) {
            return [
                new StackHealthItemResult(
                    __('stack-health::stack-health.checks.cfg_app_debug'),
                    null,
                    __('stack-health::stack-health.messages.app_debug_warn_non_local'),
                ),
            ];
        }

        return [
            new StackHealthItemResult(
                __('stack-health::stack-health.checks.cfg_app_debug'),
                true,
                __('stack-health::stack-health.messages.app_debug_false'),
            ),
        ];
    }
}
