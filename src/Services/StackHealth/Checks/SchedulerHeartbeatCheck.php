<?php

namespace JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks;

use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Contracts\StackHealthCheckContract;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Data\StackHealthItemResult;

/**
 * Reads the scheduler heartbeat file written by {@see bootstrap/app.php} schedule so silent cron failures surface.
 */
final class SchedulerHeartbeatCheck implements StackHealthCheckContract
{
    public static function id(): string
    {
        return 'scheduler_heartbeat';
    }

    /**
     * @return list<StackHealthItemResult>
     */
    public function run(): array
    {
        $name = __('stack-health::stack-health.checks.task_scheduler');
        $path = config('stack-health.scheduler_heartbeat_path');
        $maxAge = (int) config('stack-health.scheduler_heartbeat_max_age_seconds', 180);

        if (! is_readable($path)) {
            return [
                new StackHealthItemResult(
                    $name,
                    false,
                    __('stack-health::stack-health.messages.scheduler_missing'),
                ),
            ];
        }

        $raw = @file_get_contents($path);
        $ts = is_string($raw) ? (int) trim($raw) : 0;
        if ($ts <= 0) {
            return [
                new StackHealthItemResult(
                    $name,
                    false,
                    __('stack-health::stack-health.messages.scheduler_invalid'),
                ),
            ];
        }

        $age = time() - $ts;
        if ($age > $maxAge) {
            return [
                new StackHealthItemResult(
                    $name,
                    false,
                    __('stack-health::stack-health.messages.scheduler_stale', ['seconds' => $age, 'threshold' => $maxAge]),
                ),
            ];
        }

        return [
            new StackHealthItemResult(
                $name,
                true,
                __('stack-health::stack-health.messages.scheduler_ok', ['seconds' => $age]),
            ),
        ];
    }
}
