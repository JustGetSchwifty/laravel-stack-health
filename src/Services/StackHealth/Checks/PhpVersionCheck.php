<?php

namespace JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks;

use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Contracts\StackHealthCheckContract;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Data\StackHealthItemResult;

/**
 * Surfaces the runtime PHP version string so mismatches with CLI/FPM or deploy images are obvious.
 */
final class PhpVersionCheck implements StackHealthCheckContract
{
    public static function id(): string
    {
        return 'php_version';
    }

    /**
     * @return list<StackHealthItemResult>
     */
    public function run(): array
    {
        return [
            new StackHealthItemResult(
                __('stack-health::stack-health.checks.php_version'),
                true,
                PHP_VERSION,
            ),
        ];
    }
}
