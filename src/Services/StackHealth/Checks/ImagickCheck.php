<?php

namespace JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks;

use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Contracts\StackHealthCheckContract;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Data\StackHealthItemResult;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\StackHealthMessageRedactor;
use Throwable;

/**
 * Confirms Imagick is present and reports its extension version when available.
 */
final class ImagickCheck implements StackHealthCheckContract
{
    public function __construct(
        private StackHealthMessageRedactor $redactor,
    ) {}

    public static function id(): string
    {
        return 'imagick';
    }

    /**
     * @return list<StackHealthItemResult>
     */
    public function run(): array
    {
        $name = __('stack-health::stack-health.checks.imagick');

        if (! extension_loaded('imagick')) {
            return [
                new StackHealthItemResult(
                    $name,
                    false,
                    __('stack-health::stack-health.messages.imagick_not_loaded'),
                ),
            ];
        }

        try {
            $version = phpversion('imagick');
            $msg = $version
                ? __('stack-health::stack-health.messages.imagick_ok_version', ['version' => $version])
                : __('stack-health::stack-health.messages.imagick_ok_unknown');

            return [
                new StackHealthItemResult(
                    $name,
                    true,
                    $msg,
                ),
            ];
        } catch (Throwable $e) {
            return [
                new StackHealthItemResult(
                    $name,
                    false,
                    $this->redactor->exceptionMessageForUi($e, 'stack-health::stack-health.messages.error_imagick_runtime'),
                ),
            ];
        }
    }
}
