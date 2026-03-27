<?php

use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks\CacheStoreCheck;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks\CfgAppDebugCheck;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks\CfgAppEnvCheck;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks\CfgAppKeyCheck;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks\CfgAppUrlCheck;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks\CfgBroadcastCheck;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks\CfgCacheCheck;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks\CfgConfigFileCacheCheck;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks\CfgFilesystemCheck;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks\CfgLocalesCheck;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks\CfgLogCheck;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks\CfgQueueCheck;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks\CfgSessionCheck;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks\CfgSessionCookiesCheck;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks\CfgSessionSecureCookieCheck;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks\CfgTimezoneCheck;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks\DatabaseCheck;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks\GdCheck;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks\Geoip2ComposerCheck;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks\GeoipDatabaseCheck;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks\HorizonCheck;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks\ImagickCheck;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks\MailTransportCheck;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks\OpcacheCheck;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks\OutboundHttpCheck;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks\PhpExtensionsCheck;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks\PhpVersionCheck;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks\RedisCheck;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks\SapiWebCheck;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks\SchedulerHeartbeatCheck;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks\TimezoneDataCheck;

$toBool = static fn (string $key, bool $default): bool => filter_var(env($key, $default), FILTER_VALIDATE_BOOLEAN);
$dashboardPath = trim((string) env('STACK_HEALTH_PATH', ''), '/');
$dashboardUri = $dashboardPath === '' ? '/healthcheck' : '/'.$dashboardPath;
$disabledChecks = array_values(array_filter(array_map(
    static fn (string $id): string => trim($id),
    explode(',', (string) env('STACK_HEALTH_CHECKS_DISABLED', ''))
)));
$optionalChecksEnabled = $toBool('STACK_HEALTH_ENABLE_OPTIONAL_CHECKS', false);
$phpExtensions = array_values(array_filter(array_map(
    static fn (string $ext): string => trim($ext),
    explode(',', (string) env('STACK_HEALTH_PHP_EXTENSIONS', 'curl,mbstring,zip,bcmath,intl,pdo_mysql,mysqli,redis'))
)));

return [
    'enabled' => env('STACK_HEALTH_DASHBOARD', false),

    /*
    | Normalized path prefix for the HTML dashboard (e.g. "/" or "/healthcheck").
    */
    'dashboard_uri' => $dashboardUri,

    /*
    | Dashboard route metadata.
    */
    'dashboard_route_name' => env('STACK_HEALTH_ROUTE_NAME', 'stack-health.dashboard'),
    'dashboard_route_middleware' => env('STACK_HEALTH_ROUTE_MIDDLEWARE', 'stack.health'),

    /*
    | Comma-separated env value parsed into a normalized list.
    */
    'checks_disabled' => $disabledChecks,

    /*
    | Dashboard section order and check order within each section (FQCN).
    */
    'sections' => [
        [
            'title_key' => 'stack-health::stack-health.sections.runtime',
            'checks' => [
                PhpVersionCheck::class,
                SapiWebCheck::class,
                OpcacheCheck::class,
                TimezoneDataCheck::class,
            ],
        ],
        [
            'title_key' => 'stack-health::stack-health.sections.integrations',
            'checks' => array_values(array_filter([
                DatabaseCheck::class,
                $optionalChecksEnabled ? RedisCheck::class : null,
                CacheStoreCheck::class,
                OutboundHttpCheck::class,
                SchedulerHeartbeatCheck::class,
                $optionalChecksEnabled ? HorizonCheck::class : null,
                $optionalChecksEnabled ? GdCheck::class : null,
                $optionalChecksEnabled ? ImagickCheck::class : null,
            ])),
        ],
        [
            'title_key' => 'stack-health::stack-health.sections.configuration',
            'checks' => array_values(array_filter([
                CfgAppKeyCheck::class,
                CfgAppEnvCheck::class,
                CfgAppDebugCheck::class,
                CfgAppUrlCheck::class,
                CfgTimezoneCheck::class,
                CfgLocalesCheck::class,
                CfgCacheCheck::class,
                CfgQueueCheck::class,
                CfgSessionCheck::class,
                CfgSessionSecureCookieCheck::class,
                CfgSessionCookiesCheck::class,
                CfgLogCheck::class,
                CfgBroadcastCheck::class,
                CfgFilesystemCheck::class,
                CfgConfigFileCacheCheck::class,
                $optionalChecksEnabled ? MailTransportCheck::class : null,
            ])),
        ],
        [
            'title_key' => 'stack-health::stack-health.sections.extensions',
            'checks' => array_values(array_filter([
                PhpExtensionsCheck::class,
                $optionalChecksEnabled ? Geoip2ComposerCheck::class : null,
                $optionalChecksEnabled ? GeoipDatabaseCheck::class : null,
            ])),
        ],
    ],

    /*
    | When true (default) and not running in the local environment, exception details,
    | internal hostnames, and full URLs are hidden on the dashboard UI (full error is reported()).
    */
    'redact_sensitive_messages' => $toBool('STACK_HEALTH_REDACT_SENSITIVE', true),

    /*
    | When true, GET /up runs a lightweight dependency probe (throws on failure → HTTP 500).
    */
    'up_monitoring_enabled' => $toBool('STACK_HEALTH_UP_MONITORING', true),

    'geoip_database' => env('GEOIP_DATABASE_PATH')
        ?: storage_path('app/geoip/GeoLite2-City.mmdb'),

    'geoip_probe_ip' => env('GEOIP_PROBE_IP', '8.8.4.4'),

    'scheduler_heartbeat_path' => storage_path('app/.scheduler-heartbeat'),

    'scheduler_heartbeat_max_age_seconds' => (int) env('STACK_HEALTH_SCHEDULER_MAX_AGE', 180),

    /*
    | When true, the package registers an every-minute scheduled callback that writes
    | the scheduler heartbeat file (see scheduler_heartbeat_path). Disable if you register it yourself.
    */
    'register_scheduler_heartbeat' => $toBool('STACK_HEALTH_REGISTER_SCHEDULER_HEARTBEAT', true),

    'outbound_http' => $toBool('STACK_HEALTH_OUTBOUND_HTTP', false),

    'outbound_http_url' => env('STACK_HEALTH_OUTBOUND_URL', 'https://www.google.com'),

    'outbound_http_timeout' => (float) env('STACK_HEALTH_OUTBOUND_TIMEOUT', 5),

    'smtp_probe_timeout' => (float) env('STACK_HEALTH_SMTP_TIMEOUT', 8),

    'smtp_probe_verify_ssl' => $toBool('STACK_HEALTH_SMTP_VERIFY_SSL', true),

    'horizon_redis_connection' => env('STACK_HEALTH_HORIZON_REDIS_CONNECTION', 'horizon'),

    /*
    | Comma separated list of extensions checked by PhpExtensionsCheck.
    */
    'php_extensions' => $phpExtensions,
];
