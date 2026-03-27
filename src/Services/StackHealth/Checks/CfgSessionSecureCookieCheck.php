<?php

namespace JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks;

use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Contracts\StackHealthCheckContract;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Data\StackHealthItemResult;

/**
 * Warns when session.secure is not true outside local, because cookies would be sent over cleartext HTTP.
 */
final class CfgSessionSecureCookieCheck implements StackHealthCheckContract
{
    public static function id(): string
    {
        return 'cfg_session_secure_cookie';
    }

    /**
     * @return list<StackHealthItemResult>
     */
    public function run(): array
    {
        $secure = config('session.secure');
        $valueLabel = $secure === null
            ? __('stack-health::stack-health.messages.session_secure_unset')
            : ($secure ? 'true' : 'false');

        if (app()->environment('local')) {
            return [
                new StackHealthItemResult(
                    __('stack-health::stack-health.checks.cfg_session_secure_cookie'),
                    true,
                    __('stack-health::stack-health.messages.session_secure_cookie_local', ['value' => $valueLabel]),
                ),
            ];
        }

        if ($secure === true) {
            return [
                new StackHealthItemResult(
                    __('stack-health::stack-health.checks.cfg_session_secure_cookie'),
                    true,
                    __('stack-health::stack-health.messages.session_secure_cookie_ok', ['value' => $valueLabel]),
                ),
            ];
        }

        return [
            new StackHealthItemResult(
                __('stack-health::stack-health.checks.cfg_session_secure_cookie'),
                null,
                __('stack-health::stack-health.messages.session_secure_cookie_warn', ['value' => $valueLabel]),
            ),
        ];
    }
}
