# Laravel Stack Health

Operational health dashboard and `/up` diagnostics for Laravel applications.

Current release: `1.0.0` (see `VERSION` file for local tooling).

## Versioning Policy

- Source of truth for published package versions is Git tag (`vX.Y.Z`), not `composer.json`.
- `composer.json` intentionally does not include a fixed `version` field (Composer/Packagist best practice for VCS-based libraries).
- Local helper scripts read `VERSION` and pass it as `COMPOSER_ROOT_VERSION` to avoid root-version warnings in containerized checks.

## Why This Exists

This is a passion project. I originally built it for my own production needs, then decided to open source it because the same operational pain points are common across many Laravel teams.

The goal is simple: keep health signals actionable, safe, and easy to extend.

## Engineering Transparency

I am transparent about using AI tools in my daily workflow (Cursor included), but the ownership and responsibility stay with me:

- Product idea, architecture decisions, and implementation direction are mine.
- I review every change manually before merge.
- I test incrementally in small, controlled steps to avoid uncontrolled code generation.
- I treat AI as an assistant for speed, not as an autonomous decision-maker.

I am a senior engineer and I am comfortable with this workflow, but software is still software: even with strict review, occasional mistakes can happen. If you spot one, please open an issue or PR.

## AI Agent Instructions

To keep AI-assisted contributions consistent and safe, this repository ships a checked-in AI policy:

- Canonical policy: `AGENTS.md`
- Tool bridges: `.github/copilot-instructions.md`, `CLAUDE.md`, `.cursor/rules/00-core-agent-policy.mdc`

Contributors using AI agents should follow these instructions before generating or editing code.

## Features

- Health dashboard route (default `/healthcheck`) with sectioned checks.
- `/up` dependency probe hook via `DiagnosingHealth`.
- Sensitive message redaction outside local environments.
- Per-check enable/disable controls.
- Extensible check contract for custom probes.
- Unit + feature tests with Testbench.

## Installation

```bash
composer require justgetschwifty/laravel-stack-health
php artisan vendor:publish --tag=stack-health-config
```

Optional publishes:

```bash
php artisan vendor:publish --tag=stack-health-lang
php artisan vendor:publish --tag=stack-health-views
```

## Quick Start

1. Open `/healthcheck` (or custom `STACK_HEALTH_PATH`) in local.
1. Enable dashboard outside local:

```env
STACK_HEALTH_DASHBOARD=true
```

1. Keep safe production defaults:

```env
STACK_HEALTH_REDACT_SENSITIVE=true
STACK_HEALTH_ENABLE_OPTIONAL_CHECKS=false
```

## Built-in Checks

- Runtime: PHP version, SAPI, opcache, timezone data.
- Integrations: database, redis, cache, outbound HTTP, scheduler heartbeat.
- Configuration: app/env/session/cache/queue/log/filesystem checks.
- Extensions: configured PHP extensions list.
- Optional checks: Horizon, GD, Imagick, mail transport, GeoIP.

Enable optional checks:

```env
STACK_HEALTH_ENABLE_OPTIONAL_CHECKS=true
```

## Custom Check

Implement `StackHealthCheckContract`, return `StackHealthItemResult[]`, then add class to `stack-health.sections`.

```php
final class QueueConnectionCheck implements StackHealthCheckContract
{
    public static function id(): string
    {
        return 'queue_connection';
    }

    public function run(): array
    {
        return [new StackHealthItemResult('Queue connection', true, 'Queue is configured')];
    }
}
```

## Testing

```bash
composer test
```

## Security

- Do not commit secrets, keys, `.env`, or internal credentials.
- Use `SECURITY.md` for vulnerability reporting.
- Keep `STACK_HEALTH_REDACT_SENSITIVE=true` in non-local environments.

## Documentation

- [Installation](docs/installation.md)
- [Configuration](docs/configuration.md)
- [Built-in Checks](docs/checks.md)
- [Custom Checks](docs/custom-checks.md)
- [Testing](docs/testing.md)
- [Troubleshooting](docs/troubleshooting.md)
- [Release Process](docs/release.md)
- [Security Guide](docs/security.md)
- [Maintainer Transparency Notes](docs/transparency.md)

## License

MIT. See `LICENSE`.
