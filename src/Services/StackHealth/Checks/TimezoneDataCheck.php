<?php

namespace JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks;

use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Contracts\StackHealthCheckContract;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Data\StackHealthItemResult;

/**
 * Summarizes bundled vs PECL timezone database sources because wrong TZ data causes subtle scheduling bugs.
 */
final class TimezoneDataCheck implements StackHealthCheckContract
{
    public static function id(): string
    {
        return 'timezone_data';
    }

    /**
     * @return list<StackHealthItemResult>
     */
    public function run(): array
    {
        $tzMsg = __('stack-health::stack-health.messages.tz_builtin');
        if (function_exists('timezone_version_get')) {
            $tzMsg = __('stack-health::stack-health.messages.tz_version', ['version' => timezone_version_get()]);
            if (extension_loaded('timezonedb')) {
                $tzMsg .= __('stack-health::stack-health.messages.tz_timezonedb_suffix');
            }
        } elseif (extension_loaded('timezonedb')) {
            $tzMsg = __('stack-health::stack-health.messages.tz_pecl_only');
        }

        return [
            new StackHealthItemResult(
                __('stack-health::stack-health.checks.timezone_data'),
                true,
                $tzMsg,
            ),
        ];
    }
}
