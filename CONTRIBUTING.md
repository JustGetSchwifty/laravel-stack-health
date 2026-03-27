# Contributing

## Development

```bash
composer install
composer test
```

## Pull Requests

- Keep PRs focused on one concern.
- Add tests for behavior changes.
- Update docs for config or API changes.
- Keep backward compatibility unless explicitly marked as breaking.
- If AI tools were used, disclose that in the PR summary and confirm manual review/testing.
- If AI tools are used, follow repository policy in `AGENTS.md` (and tool bridge files).

## Code Style

Use Laravel Pint:

```bash
composer pint
composer pint:test
```

## Commit and Release Notes

Use clear, intent-driven commit messages and update changelog for user-facing changes.

## AI Usage Transparency

AI assistance is allowed for contributor productivity, but maintainers review all code manually before merge.

## AI Policy Files

- Canonical instructions: `AGENTS.md`
- Tool-specific bridge files:
  - `.github/copilot-instructions.md`
  - `CLAUDE.md`
  - `.cursor/rules/00-core-agent-policy.mdc`
