<?php

namespace JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Data;

/**
 * One dashboard row: human-readable name, tri-state outcome, and detail text for operators.
 *
 * @see \App\Services\StackHealth\Contracts\StackHealthCheckContract Check implementations produce these.
 */
readonly class StackHealthItemResult
{
    /**
     * @param  string  $name  Localized label shown in the first column.
     * @param  bool|null  $ok  {@see true} pass, {@see false} fail, {@see null} warning / skipped / informational ambiguity.
     * @param  string  $message  Explanation or evidence (version string, error summary, redacted hint).
     */
    public function __construct(
        public string $name,
        public ?bool $ok,
        public string $message,
    ) {}

    /**
     * @return array{name: string, ok: bool|null, message: string} Shape expected by the Blade dashboard.
     */
    public function toViewArray(): array
    {
        return [
            'name' => $this->name,
            'ok' => $this->ok,
            'message' => $this->message,
        ];
    }
}
