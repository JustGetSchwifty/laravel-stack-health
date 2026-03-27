# Security

## Safe Defaults

- Keep `STACK_HEALTH_REDACT_SENSITIVE=true` in non-local environments.
- Keep optional checks disabled until dependencies are ready.
- Protect dashboard route with middleware.

## Secret Hygiene

Never commit:

- `.env`
- credentials, tokens, private keys
- production URLs with credentials

## Vulnerability Reporting

Use private disclosure flow from `SECURITY.md`.
