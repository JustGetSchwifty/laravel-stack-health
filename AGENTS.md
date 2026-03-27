# AI Agent Operating Instructions

This file is the canonical instruction source for AI coding agents in this repository.
If another tool-specific instruction file exists, treat this file as the source of truth.

## Project Scope

- Package: `laravel-stack-health`
- Stack: PHP 8.3+, Laravel package, Orchestral Testbench, PHPUnit, Pint
- Main goals: safe health diagnostics, extensibility, predictable behavior, no secret leaks

## Non-Negotiable Rules

- Use English only in code artifacts: identifiers, class names, comments, commit text, docs updates.
- Never hardcode user-facing UI copy where translations should be used.
- Keep translation-backed text in language files and use translation keys in views/messages.
- Never commit secrets, credentials, private keys, tokens, or `.env` contents.
- Keep sensitive output redacted in non-local environments.
- Preserve package boundaries; avoid coupling package code to host-app internals.
- Prefer backward-compatible changes unless a breaking change is explicitly requested.

## Required Workflow For Every Code Change

1. Understand scope, affected paths, and existing conventions before editing.
2. Make minimal, targeted edits that solve the root cause.
3. Update tests/docs together with behavior changes.
4. Run validation and tests after changes.
5. Re-check for security/privacy regressions before finishing.

## Validation And Test Gates

Run from the repository root.

- Fast quality checks:
  - `composer validate --strict`
  - `composer pint:test`
  - `composer test`
- Optional explicit commands:
  - `vendor/bin/phpunit`
  - `composer audit`

Policy:

- Testing is mandatory after code changes.
- At minimum, run impacted tests; for cross-cutting changes, run the full suite.
- Do not claim success without reporting what was actually executed.

## Code Quality Expectations

- Follow existing architecture and naming patterns in the package.
- Keep classes focused; avoid mixing HTTP, domain, and infra concerns unnecessarily.
- Prefer explicit types and clear method contracts.
- Add concise comments or PHPDoc where intent is not obvious.
- Avoid noisy comments that restate trivial code.
- Do not introduce dead code, speculative abstractions, or hidden side effects.

## i18n And Configuration Discipline

- Add new user-visible strings to translation files.
- Use config keys / env-backed config, not scattered magic values.
- Keep defaults safe and production-friendly.

## Security And Privacy Guardrails

- Treat all logs, exception messages, and diagnostics as potentially sensitive.
- Keep redaction behavior intact outside local environments.
- Never print or persist secrets in tests, fixtures, docs, or sample outputs.
- If a change could impact security posture, document it in PR/release notes.

## Dependency And Release Hygiene

- Do not add dependencies unless clearly justified by value.
- Keep Composer metadata valid and consistent.
- Package version source of truth is Git tag (`vX.Y.Z`), not `composer.json` `version`.
- Local tooling version input is `VERSION` file plus `COMPOSER_ROOT_VERSION`.

## Documentation Requirements

When behavior, config, commands, or contributor workflow changes:

- Update `README.md` and relevant docs under `docs/`.
- Update `CONTRIBUTING.md` if contributor workflow changes.
- Keep examples runnable and consistent with real commands in this repository.

## Agent Collaboration Behavior

- State assumptions when uncertain.
- Ask for clarification before irreversible or risky operations.
- Prefer small diffs and transparent rationale over large rewrites.
- Do not invent test results, command outputs, or external references.
