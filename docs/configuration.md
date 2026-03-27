# Configuration

Main file: `config/stack-health.php`.

## Core Flags

- `STACK_HEALTH_DASHBOARD`: enable dashboard outside local.
- `STACK_HEALTH_PATH`: dashboard URI path (default `/healthcheck`).
- `STACK_HEALTH_ROUTE_NAME`: route name.
- `STACK_HEALTH_ROUTE_MIDDLEWARE`: comma-separated middleware.
- `STACK_HEALTH_CHECKS_DISABLED`: comma-separated check IDs.
- `STACK_HEALTH_REDACT_SENSITIVE`: hide sensitive output.
- `STACK_HEALTH_UP_MONITORING`: run DB probe on `DiagnosingHealth`.

## Optional Checks

- `STACK_HEALTH_ENABLE_OPTIONAL_CHECKS=false` by default.
- Enables Horizon, GD, Imagick, Mail transport, and GeoIP checks.

## Runtime Tunables

- `STACK_HEALTH_OUTBOUND_HTTP`
- `STACK_HEALTH_OUTBOUND_URL`
- `STACK_HEALTH_OUTBOUND_TIMEOUT`
- `STACK_HEALTH_SCHEDULER_MAX_AGE`
- `STACK_HEALTH_REGISTER_SCHEDULER_HEARTBEAT` (default `true`): register the every-minute heartbeat writer; set `false` only if you write the file yourself.
- `STACK_HEALTH_SMTP_TIMEOUT`
- `STACK_HEALTH_SMTP_VERIFY_SSL`
- `STACK_HEALTH_HORIZON_REDIS_CONNECTION`
- `STACK_HEALTH_PHP_EXTENSIONS`

## Secure Production Baseline

```env
STACK_HEALTH_DASHBOARD=true
STACK_HEALTH_REDACT_SENSITIVE=true
STACK_HEALTH_ENABLE_OPTIONAL_CHECKS=false
STACK_HEALTH_OUTBOUND_HTTP=true
```
