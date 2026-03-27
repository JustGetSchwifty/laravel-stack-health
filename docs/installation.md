# Installation

```bash
composer require justgetschwifty/laravel-stack-health
php artisan vendor:publish --tag=stack-health-config
```

Package discovery registers the service provider automatically.

## Optional Publishes

```bash
php artisan vendor:publish --tag=stack-health-lang
php artisan vendor:publish --tag=stack-health-views
```

## Minimal Environment

```env
STACK_HEALTH_DASHBOARD=false
STACK_HEALTH_PATH=healthcheck
STACK_HEALTH_REDACT_SENSITIVE=true
STACK_HEALTH_ENABLE_OPTIONAL_CHECKS=false
```
