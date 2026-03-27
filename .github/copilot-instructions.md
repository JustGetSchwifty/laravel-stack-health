# GitHub Copilot Instructions

Primary policy lives in `AGENTS.md` at repository root. Follow it as the canonical rule set.

## Required behavior

- Read and follow `AGENTS.md` before proposing or generating changes.
- Keep edits small, focused, and aligned with existing architecture.
- Use English only in code artifacts.
- Do not hardcode translatable UI strings.
- Never introduce secrets or private data into code, tests, docs, or logs.

## Required validation

- Run and report relevant checks after changes.
- For PHP/package changes, prefer:
  - `composer validate --strict`
  - `composer pint:test`
  - `composer test`
- For explicit local runs when needed:
  - `vendor/bin/phpunit`
  - `composer audit`
