<?php

namespace JustGetSchwifty\LaravelStackHealth\Services\StackHealth;

use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Contracts\StackHealthCheckContract;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Data\StackHealthItemResult;
use Throwable;

/**
 * Executes checks behind a safety boundary so one failing check does not break the full dashboard response.
 */
final class StackHealthCheckRunner
{
    /**
     * @return list<StackHealthItemResult>
     */
    public function run(StackHealthCheckContract $check): array
    {
        try {
            return $check->run();
        } catch (Throwable $e) {
            report($e);

            $id = $check::id();
            $name = __('stack-health::stack-health.checks.'.$id);
            if ($name === 'stack-health::stack-health.checks.'.$id) {
                $name = __('stack-health::stack-health.messages.unknown_check_label', ['id' => $id]);
            }

            return [
                new StackHealthItemResult(
                    $name,
                    false,
                    __('stack-health::stack-health.messages.error_check_runtime', ['check' => $name]),
                ),
            ];
        }
    }
}
