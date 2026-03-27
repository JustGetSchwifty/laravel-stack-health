<?php

namespace JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks;

use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Contracts\StackHealthCheckContract;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Data\StackHealthItemResult;

/**
 * Documents which SAPI serves the request; CLI during `artisan` is expected and called out to avoid false alarms.
 */
final class SapiWebCheck implements StackHealthCheckContract
{
    public static function id(): string
    {
        return 'sapi_web';
    }

    /**
     * @return list<StackHealthItemResult>
     */
    public function run(): array
    {
        return [
            new StackHealthItemResult(
                __('stack-health::stack-health.checks.sapi_web'),
                true,
                __('stack-health::stack-health.messages.sapi_cli_hint', ['sapi' => PHP_SAPI]),
            ),
        ];
    }
}
