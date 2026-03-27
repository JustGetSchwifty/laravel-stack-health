<?php

namespace JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks;

use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Contracts\StackHealthCheckContract;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Data\StackHealthItemResult;

/**
 * Shows default and fallback locales to catch mis-set translation config after deploy.
 */
final class CfgLocalesCheck implements StackHealthCheckContract
{
    public static function id(): string
    {
        return 'cfg_locales';
    }

    /**
     * @return list<StackHealthItemResult>
     */
    public function run(): array
    {
        return [
            new StackHealthItemResult(
                __('stack-health::stack-health.checks.cfg_locales'),
                true,
                __('stack-health::stack-health.messages.cfg_locales', [
                    'locale' => config('app.locale'),
                    'fallback' => config('app.fallback_locale'),
                ]),
            ),
        ];
    }
}
