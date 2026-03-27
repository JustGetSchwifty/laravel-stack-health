<?php

namespace JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks;

use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Contracts\StackHealthCheckContract;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Data\StackHealthItemResult;

/**
 * Shows default filesystem disk name.
 */
final class CfgFilesystemCheck implements StackHealthCheckContract
{
    public static function id(): string
    {
        return 'cfg_filesystem';
    }

    /**
     * @return list<StackHealthItemResult>
     */
    public function run(): array
    {
        return [
            new StackHealthItemResult(
                __('stack-health::stack-health.checks.cfg_filesystem'),
                true,
                __('stack-health::stack-health.messages.cfg_filesystem', ['disk' => config('filesystems.default')]),
            ),
        ];
    }
}
