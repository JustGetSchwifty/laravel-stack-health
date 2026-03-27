<?php

namespace JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks;

use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Contracts\StackHealthCheckContract;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Data\StackHealthItemResult;

/**
 * Shows http_only and same_site session cookie attributes for security review.
 */
final class CfgSessionCookiesCheck implements StackHealthCheckContract
{
    public static function id(): string
    {
        return 'cfg_session_cookies';
    }

    /**
     * @return list<StackHealthItemResult>
     */
    public function run(): array
    {
        return [
            new StackHealthItemResult(
                __('stack-health::stack-health.checks.cfg_session_cookies'),
                true,
                __('stack-health::stack-health.messages.cfg_session_cookies', [
                    'http_only' => config('session.http_only') ? 'true' : 'false',
                    'same_site' => config('session.same_site') ?? 'null',
                ]),
            ),
        ];
    }
}
