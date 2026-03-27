# Changelog

All notable changes to this project will be documented in this file.

## [1.0.1] - 2026-03-27

### Added

- Automatic Laravel scheduler registration: the package adds an every-minute callback that writes the scheduler heartbeat file used by the Task scheduler health check, so host apps no longer need to wire this in `bootstrap/app.php`.
- Config flag `register_scheduler_heartbeat` / env `STACK_HEALTH_REGISTER_SCHEDULER_HEARTBEAT` (default `true`) to disable registration when you manage the heartbeat file yourself.
- Feature tests covering heartbeat schedule registration.

### Changed

- `SchedulerHeartbeatCheck` documentation and the `scheduler_ok` dashboard string now describe package-owned heartbeat updates.

## [1.0.0] - 2026-03-27

### Added

- Initial standalone package structure.
- Stack health dashboard + checks extracted from host application.
- Testbench-based package tests.
- OSS governance and security baseline files.
