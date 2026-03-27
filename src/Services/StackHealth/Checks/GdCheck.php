<?php

namespace JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks;

use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Contracts\StackHealthCheckContract;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Data\StackHealthItemResult;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\StackHealthMessageRedactor;
use Throwable;

/**
 * Verifies GD is loaded and can emit a minimal PNG because many image pipelines fail at runtime otherwise.
 */
final class GdCheck implements StackHealthCheckContract
{
    public function __construct(
        private StackHealthMessageRedactor $redactor,
    ) {}

    public static function id(): string
    {
        return 'gd';
    }

    /**
     * @return list<StackHealthItemResult>
     */
    public function run(): array
    {
        $name = __('stack-health::stack-health.checks.gd');

        if (! extension_loaded('gd')) {
            return [
                new StackHealthItemResult(
                    $name,
                    false,
                    __('stack-health::stack-health.messages.gd_not_loaded'),
                ),
            ];
        }

        try {
            $im = imagecreatetruecolor(1, 1);
            if ($im === false) {
                return [
                    new StackHealthItemResult(
                        $name,
                        false,
                        __('stack-health::stack-health.messages.gd_create_failed'),
                    ),
                ];
            }
            ob_start();
            $pngOk = imagepng($im);
            $data = ob_get_clean();
            imagedestroy($im);
            if (! $pngOk || $data === false || ! str_starts_with($data, "\x89PNG")) {
                return [
                    new StackHealthItemResult(
                        $name,
                        false,
                        __('stack-health::stack-health.messages.gd_png_failed'),
                    ),
                ];
            }

            return [
                new StackHealthItemResult(
                    $name,
                    true,
                    __('stack-health::stack-health.messages.gd_ok'),
                ),
            ];
        } catch (Throwable $e) {
            return [
                new StackHealthItemResult(
                    $name,
                    false,
                    $this->redactor->exceptionMessageForUi($e, 'stack-health::stack-health.messages.error_gd_runtime'),
                ),
            ];
        }
    }
}
