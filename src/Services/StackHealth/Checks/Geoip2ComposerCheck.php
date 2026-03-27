<?php

namespace JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks;

use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Contracts\StackHealthCheckContract;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Data\StackHealthItemResult;
use GeoIp2\Database\Reader;

/**
 * Verifies the GeoIP2 PHP library is installed via Composer; separate from the MMDB file probe.
 */
final class Geoip2ComposerCheck implements StackHealthCheckContract
{
    public static function id(): string
    {
        return 'geoip2_composer';
    }

    /**
     * @return list<StackHealthItemResult>
     */
    public function run(): array
    {
        $ok = class_exists(Reader::class);

        return [
            new StackHealthItemResult(
                __('stack-health::stack-health.checks.geoip2_composer'),
                $ok,
                $ok
                    ? __('stack-health::stack-health.messages.geoip2_installed')
                    : __('stack-health::stack-health.messages.geoip2_missing'),
            ),
        ];
    }
}
