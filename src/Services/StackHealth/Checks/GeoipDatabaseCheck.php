<?php

namespace JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks;

use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Contracts\StackHealthCheckContract;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Data\StackHealthItemResult;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\StackHealthMessageRedactor;
use GeoIp2\Database\Reader;
use Throwable;

/**
 * Validates GeoLite DB path readability and performs a sample city lookup to prove the file is usable.
 */
final class GeoipDatabaseCheck implements StackHealthCheckContract
{
    public function __construct(
        private StackHealthMessageRedactor $redactor,
    ) {}

    public static function id(): string
    {
        return 'geoip_database';
    }

    /**
     * @return list<StackHealthItemResult>
     */
    public function run(): array
    {
        $path = config('stack-health.geoip_database');
        $hasKey = filled(config('maxmind.license_key'));
        $fileLabel = __('stack-health::stack-health.checks.geoip_database_file');

        if ($path === null || $path === '') {
            return [
                new StackHealthItemResult(
                    $fileLabel,
                    null,
                    __('stack-health::stack-health.messages.geoip_path_unconfigured'),
                ),
            ];
        }

        if (! is_readable($path)) {
            $hint = $hasKey
                ? __('stack-health::stack-health.messages.geoip_hint_scheduled')
                : __('stack-health::stack-health.messages.geoip_hint_manual');

            return [
                new StackHealthItemResult(
                    $fileLabel,
                    null,
                    $this->redactor->shouldRedact()
                        ? __('stack-health::stack-health.messages.geoip_file_missing_redacted', ['hint' => $hint])
                        : __('stack-health::stack-health.messages.geoip_file_missing', ['path' => $path, 'hint' => $hint]),
                ),
            ];
        }

        try {
            $ip = config('stack-health.geoip_probe_ip', '8.8.4.4');
            if (! is_string($ip) || filter_var($ip, FILTER_VALIDATE_IP) === false) {
                $ip = '8.8.4.4';
            }

            $reader = new Reader($path);
            try {
                $record = $reader->city($ip);

                $country = $record->country->name
                    ?? $record->registeredCountry->name
                    ?? $record->continent->name
                    ?? $record->country->isoCode
                    ?? $record->registeredCountry->isoCode;

                $locality = $record->city->name
                    ?? $record->mostSpecificSubdivision->name;

                $countryLabel = ($country !== null && $country !== '') ? $country : null;
                $localityLabel = ($locality !== null && $locality !== '') ? $locality : null;
                $dash = __('stack-health::stack-health.messages.geoip_placeholder');
                $sep = __('stack-health::stack-health.messages.geoip_result_separator');

                $message = $countryLabel === null && $localityLabel === null
                    ? __('stack-health::stack-health.messages.geoip_no_place')
                    : ($countryLabel ?? $dash).$sep.($localityLabel ?? $dash);

                return [
                    new StackHealthItemResult(
                        __('stack-health::stack-health.checks.geoip_lookup', ['ip' => $ip]),
                        true,
                        $message,
                    ),
                ];
            } finally {
                $reader->close();
            }
        } catch (Throwable $e) {
            return [
                new StackHealthItemResult(
                    __('stack-health::stack-health.checks.geoip_lookup_generic'),
                    false,
                    $this->redactor->exceptionMessageForUi($e, 'stack-health::stack-health.messages.error_geoip_lookup'),
                ),
            ];
        }
    }
}
