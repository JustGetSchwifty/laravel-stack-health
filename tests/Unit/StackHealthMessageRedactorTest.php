<?php

namespace JustGetSchwifty\LaravelStackHealth\Tests\Unit;

use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\StackHealthMessageRedactor;
use RuntimeException;
use JustGetSchwifty\LaravelStackHealth\Tests\TestCase;

class StackHealthMessageRedactorTest extends TestCase
{
    public function test_should_redact_outside_local_when_enabled(): void
    {
        config(['stack-health.redact_sensitive_messages' => true]);

        $redactor = app(StackHealthMessageRedactor::class);

        $this->assertTrue($redactor->shouldRedact());
    }

    public function test_should_not_redact_when_disabled_in_config(): void
    {
        config(['stack-health.redact_sensitive_messages' => false]);

        $redactor = app(StackHealthMessageRedactor::class);

        $this->assertFalse($redactor->shouldRedact());
    }

    public function test_should_not_redact_in_local_environment(): void
    {
        $originalEnv = $this->app->environment();
        $this->app['env'] = 'local';
        config(['stack-health.redact_sensitive_messages' => true]);

        $redactor = app(StackHealthMessageRedactor::class);

        $this->assertFalse($redactor->shouldRedact());

        $this->app['env'] = $originalEnv;
    }

    public function test_exception_message_for_ui_is_generic_when_redacted(): void
    {
        config(['stack-health.redact_sensitive_messages' => true]);
        $redactor = app(StackHealthMessageRedactor::class);

        $message = $redactor->exceptionMessageForUi(
            new RuntimeException('sensitive internal detail'),
            'stack-health::stack-health.messages.error_database'
        );

        $this->assertSame(__('stack-health::stack-health.messages.error_database'), $message);
    }
}
