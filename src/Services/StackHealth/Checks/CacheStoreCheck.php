<?php

namespace JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks;

use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Contracts\StackHealthCheckContract;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Data\StackHealthItemResult;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\StackHealthMessageRedactor;
use Illuminate\Support\Facades\Cache;
use Throwable;

/**
 * Writes and reads a short-lived key through the default cache store to verify the driver works end-to-end.
 */
final class CacheStoreCheck implements StackHealthCheckContract
{
    public function __construct(
        private StackHealthMessageRedactor $redactor,
    ) {}

    public static function id(): string
    {
        return 'cache_store';
    }

    /**
     * @return list<StackHealthItemResult>
     */
    public function run(): array
    {
        try {
            $key = 'stack_health_'.bin2hex(random_bytes(4));
            Cache::put($key, 'ok', 60);
            $ok = Cache::get($key) === 'ok';
            Cache::forget($key);

            return [
                new StackHealthItemResult(
                    __('stack-health::stack-health.checks.cache_store'),
                    $ok,
                    __('stack-health::stack-health.messages.cache_ok', ['driver' => config('cache.default')]),
                ),
            ];
        } catch (Throwable $e) {
            return [
                new StackHealthItemResult(
                    __('stack-health::stack-health.checks.cache_store'),
                    false,
                    $this->redactor->exceptionMessageForUi($e, 'stack-health::stack-health.messages.error_cache_store'),
                ),
            ];
        }
    }
}
