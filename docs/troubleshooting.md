# Troubleshooting

## Dashboard returns 404

- In non-local, set `STACK_HEALTH_DASHBOARD=true`.
- Verify route path and middleware config.

## Packagist not showing latest tag

- Run `composer validate --strict`.
- Check tag format (`vX.Y.Z`).
- Verify Packagist webhook/GitHub app sync.

## Missing translations in UI

- Publish language files:

```bash
php artisan vendor:publish --tag=stack-health-lang
```

## Outbound check fails

- Ensure `STACK_HEALTH_OUTBOUND_HTTP=true`.
- Verify URL and timeout values.
