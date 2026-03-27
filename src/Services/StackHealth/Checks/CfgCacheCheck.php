<?php

namespace JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks;

use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Contracts\StackHealthCheckContract;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Data\StackHealthItemResult;

/**
 * Displays default cache store name (does not probe the store; see {@see CacheStoreCheck}).
 */
final class CfgCacheCheck implements StackHealthCheckContract
{
    public static function id(): string
    {
        return 'cfg_cache';
    }

    /**
     * @return list<StackHealthItemResult>
     */
    public function run(): array
    {
        return [
            new StackHealthItemResult(
                __('stack-health::stack-health.checks.cfg_cache'),
                true,
                __('stack-health::stack-health.messages.cfg_cache', ['store' => config('cache.default')]),
            ),
        ];
    }
}
