# Release Process

## Pre-release

```bash
composer validate --strict
composer test
```

Checklist:

- Update changelog.
- Update `VERSION` (local tooling input).
- Verify docs and config examples.
- Verify no secrets in staged diff.
- Ensure `composer.json` does not contain a fixed `version` field.

## Tag Release

```bash
git checkout main
git pull --ff-only
git tag v1.0.0
git push origin main --tags
```

Notes:

- Use Git tag (`vX.Y.Z`) as the canonical package version for Packagist.
- Do not recreate existing tags; publish a new patch version instead.

## Packagist Publish

1. Submit package URL once on Packagist.
2. Enable GitHub hook/app sync.
3. Verify new version appears after push.
