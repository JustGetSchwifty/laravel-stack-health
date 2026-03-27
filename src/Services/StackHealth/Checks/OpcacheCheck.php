<?php

namespace JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks;

use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Contracts\StackHealthCheckContract;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Data\StackHealthItemResult;

/**
 * Reads Opcache status when the extension exposes it; null means the probe API is unavailable (not necessarily a failure).
 */
final class OpcacheCheck implements StackHealthCheckContract
{
    public static function id(): string
    {
        return 'opcache';
    }

    /**
     * @return list<StackHealthItemResult>
     */
    public function run(): array
    {
        $opcacheOk = null;
        $opcacheMsg = __('stack-health::stack-health.messages.opcache_not_available');
        if (function_exists('opcache_get_status')) {
            $status = @opcache_get_status(false);
            if (is_array($status)) {
                $opcacheOk = (bool) ($status['opcache_enabled'] ?? false);
                $opcacheMsg = $opcacheOk
                    ? __('stack-health::stack-health.messages.opcache_enabled')
                    : __('stack-health::stack-health.messages.opcache_disabled');
            }
        }

        return [
            new StackHealthItemResult(
                __('stack-health::stack-health.checks.opcache'),
                $opcacheOk,
                $opcacheMsg,
            ),
        ];
    }
}
