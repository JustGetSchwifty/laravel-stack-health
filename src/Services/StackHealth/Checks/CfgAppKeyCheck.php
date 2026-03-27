<?php

namespace JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks;

use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Contracts\StackHealthCheckContract;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Data\StackHealthItemResult;

/**
 * Validates APP_KEY shape (base64:32 bytes or 32-char legacy) without exposing the secret in messages.
 */
final class CfgAppKeyCheck implements StackHealthCheckContract
{
    public static function id(): string
    {
        return 'cfg_app_key';
    }

    /**
     * @return list<StackHealthItemResult>
     */
    public function run(): array
    {
        $key = config('app.key');

        if (! is_string($key) || $key === '') {
            return [
                new StackHealthItemResult(
                    __('stack-health::stack-health.checks.cfg_app_key'),
                    false,
                    __('stack-health::stack-health.messages.app_key_missing'),
                ),
            ];
        }

        $valid = false;
        if (str_starts_with($key, 'base64:')) {
            $raw = base64_decode(substr($key, 7), true);
            $valid = $raw !== false && strlen($raw) === 32;
        } else {
            $valid = strlen($key) === 32;
        }

        return [
            new StackHealthItemResult(
                __('stack-health::stack-health.checks.cfg_app_key'),
                $valid,
                $valid
                    ? __('stack-health::stack-health.messages.app_key_ok')
                    : __('stack-health::stack-health.messages.app_key_invalid'),
            ),
        ];
    }
}
