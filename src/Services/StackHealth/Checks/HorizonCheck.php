<?php

namespace JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks;

use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Contracts\StackHealthCheckContract;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Data\StackHealthItemResult;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\HorizonRedisProbe;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\StackHealthMessageRedactor;

/**
 * Probes Redis for Horizon index keys so “Horizon installed but not running” is distinguishable from a dead Redis probe.
 */
final class HorizonCheck implements StackHealthCheckContract
{
    public function __construct(
        private StackHealthMessageRedactor $redactor,
        private HorizonRedisProbe $horizonProbe,
    ) {}

    public static function id(): string
    {
        return 'horizon';
    }

    /**
     * @return list<StackHealthItemResult>
     */
    public function run(): array
    {
        $name = __('stack-health::stack-health.checks.laravel_horizon');
        $probe = $this->horizonProbe->hasHorizonIndices();

        if ($probe['error'] !== null) {
            report(new \RuntimeException(__('stack-health::stack-health.messages.horizon_probe_error').': '.$probe['error']));

            return [
                new StackHealthItemResult(
                    $name,
                    false,
                    $this->redactor->shouldRedact()
                        ? __('stack-health::stack-health.messages.horizon_probe_error')
                        : $probe['error'],
                ),
            ];
        }

        if (! $probe['ok']) {
            return [
                new StackHealthItemResult(
                    $name,
                    false,
                    __('stack-health::stack-health.messages.horizon_missing'),
                ),
            ];
        }

        return [
            new StackHealthItemResult(
                $name,
                true,
                __('stack-health::stack-health.messages.horizon_ok', [
                    'masters' => $probe['masters'],
                    'supervisors' => $probe['supervisors'],
                ]),
            ),
        ];
    }
}
