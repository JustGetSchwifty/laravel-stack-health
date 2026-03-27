<?php

namespace JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks;

use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Contracts\StackHealthCheckContract;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Data\StackHealthItemResult;

/**
 * Detects missing `config:cache` in production because uncached config increases boot cost and drift risk.
 */
final class CfgConfigFileCacheCheck implements StackHealthCheckContract
{
    public static function id(): string
    {
        return 'cfg_config_file_cache';
    }

    /**
     * @return list<StackHealthItemResult>
     */
    public function run(): array
    {
        $cached = app()->configurationIsCached();

        if (app()->environment('production') && ! $cached) {
            return [
                new StackHealthItemResult(
                    __('stack-health::stack-health.checks.cfg_config_file_cache'),
                    null,
                    __('stack-health::stack-health.messages.cfg_config_not_cached_prod'),
                ),
            ];
        }

        return [
            new StackHealthItemResult(
                __('stack-health::stack-health.checks.cfg_config_file_cache'),
                true,
                $cached
                    ? __('stack-health::stack-health.messages.cfg_config_cached_yes')
                    : __('stack-health::stack-health.messages.cfg_config_cached_no'),
            ),
        ];
    }
}
