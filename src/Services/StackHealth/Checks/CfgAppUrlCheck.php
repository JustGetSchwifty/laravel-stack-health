<?php

namespace JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks;

use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Contracts\StackHealthCheckContract;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Data\StackHealthItemResult;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\StackHealthMessageRedactor;

/**
 * Checks APP_URL for HTTPS and localhost-in-production patterns; redacts the raw URL outside local when configured.
 */
final class CfgAppUrlCheck implements StackHealthCheckContract
{
    public function __construct(
        private StackHealthMessageRedactor $redactor,
    ) {}

    public static function id(): string
    {
        return 'cfg_app_url';
    }

    /**
     * @return list<StackHealthItemResult>
     */
    public function run(): array
    {
        $url = (string) config('app.url');
        $production = app()->environment('production');
        $localhost = str_contains($url, 'localhost') || str_contains($url, '127.0.0.1');
        $isHttps = str_starts_with(strtolower(trim($url)), 'https://');

        if (app()->environment('local')) {
            return [
                new StackHealthItemResult(
                    __('stack-health::stack-health.checks.cfg_app_url'),
                    true,
                    $url.' — '.__('stack-health::stack-health.messages.app_url_local_dev'),
                ),
            ];
        }

        $hints = [];
        if (! $isHttps) {
            $hints[] = __('stack-health::stack-health.messages.app_url_not_https');
        }
        if ($production && $localhost) {
            $hints[] = __('stack-health::stack-health.messages.app_url_localhost_prod');
        }

        if ($this->redactor->shouldRedact()) {
            return [
                new StackHealthItemResult(
                    __('stack-health::stack-health.checks.cfg_app_url'),
                    $hints === [] ? true : null,
                    $hints === []
                        ? __('stack-health::stack-health.messages.app_url_redacted_ok')
                        : __('stack-health::stack-health.messages.app_url_redacted_warn'),
                ),
            ];
        }

        return [
            new StackHealthItemResult(
                __('stack-health::stack-health.checks.cfg_app_url'),
                $hints === [] ? true : null,
                $hints === [] ? $url : $url.' — '.implode(' ', $hints),
            ),
        ];
    }
}
