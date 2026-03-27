# Testing

This package uses Orchestral Testbench.

## Run Tests

```bash
composer test
```

## Recommended Maintainer Matrix

- PHP 8.3 and 8.4
- Laravel/Testbench compatible versions
- Add one `prefer-lowest` job

## Minimum Coverage Expectations

- Unit tests for each check critical branch.
- Registry/catalog/runner behavior.
- Feature tests for route/access/render behavior.
