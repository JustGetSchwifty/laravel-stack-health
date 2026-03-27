<?php

namespace JustGetSchwifty\LaravelStackHealth\Services\StackHealth;

use Throwable;

/**
 * Decides when stack-health UI should hide raw errors/URLs so production pages stay safe while {@see report()} still logs detail.
 */
class StackHealthMessageRedactor
{
    /**
     * @return bool True outside local when {@see config('stack-health.redact_sensitive_messages')} is on.
     */
    public function shouldRedact(): bool
    {
        $isLocal = app()->environment('local')
            || (string) config('app.env') === 'local';

        if ($isLocal) {
            return false;
        }

        return filter_var(config('stack-health.redact_sensitive_messages', true), FILTER_VALIDATE_BOOL);
    }

    /**
     * @param  string  $genericTranslationKey  Translation key for the operator-safe message when redacting.
     * @return string Either {@see Throwable::getMessage()} or a localized generic string after {@see report()}.
     */
    public function exceptionMessageForUi(Throwable $e, string $genericTranslationKey): string
    {
        if (! $this->shouldRedact()) {
            return $e->getMessage();
        }

        report($e);

        return __($genericTranslationKey);
    }
}
