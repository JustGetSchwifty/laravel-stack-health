# Maintainer Transparency Notes

## Project Origin

This package started as a personal operational tool.  
It was built first for real-world private use, then extracted and generalized for public Laravel projects.

## AI-Assisted Development Policy

The maintainer is transparent about AI-assisted development. AI may be used for drafting, speed, and repetitive tasks, but it is not treated as an autonomous authority.

Current maintainer rules:

- architectural and product decisions are human-made
- implementation is reviewed manually, line by line when needed
- tests are executed and checked manually
- development is incremental (small scoped changes), not large uncontrolled dumps
- security and secret hygiene are validated manually before release

## AI Instruction Source Of Truth

To reduce inconsistency across different AI tools, this repository centralizes AI guidance in:

- `AGENTS.md` (canonical policy)

Compatibility bridge files are also included for common assistants:

- `.github/copilot-instructions.md`
- `CLAUDE.md`
- `.cursor/rules/00-core-agent-policy.mdc`

## Quality and Responsibility

Human review is the final gate for correctness, security, and release readiness.  
If you find a bug, edge case, or regression, please report it. Constructive feedback is welcome and appreciated.
