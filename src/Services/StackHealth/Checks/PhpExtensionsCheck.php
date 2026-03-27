<?php

namespace JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks;

use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Contracts\StackHealthCheckContract;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Data\StackHealthItemResult;

/**
 * Matrix of common PHP extensions expected by this stack; one row per extension so gaps are visible individually.
 */
final class PhpExtensionsCheck implements StackHealthCheckContract
{
    public static function id(): string
    {
        return 'php_extensions';
    }

    /**
     * @return list<StackHealthItemResult>
     */
    public function run(): array
    {
        $items = [];
        /** @var list<string> $extensions */
        $extensions = config('stack-health.php_extensions', []);

        foreach ($extensions as $ext) {
            $loaded = extension_loaded($ext);
            $items[] = new StackHealthItemResult(
                __('stack-health::stack-health.checks.ext_named', ['ext' => $ext]),
                $loaded,
                $loaded
                    ? __('stack-health::stack-health.messages.ext_loaded')
                    : __('stack-health::stack-health.messages.ext_not_loaded'),
            );
        }

        return $items;
    }
}
