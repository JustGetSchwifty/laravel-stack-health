<?php

namespace JustGetSchwifty\LaravelStackHealth\Tests\Unit;

use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Contracts\StackHealthCheckContract;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Data\StackHealthItemResult;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\StackHealthCheckRegistry;
use RuntimeException;
use JustGetSchwifty\LaravelStackHealth\Tests\TestCase;

class StackHealthCheckRegistryTest extends TestCase
{
    public function test_disabled_check_id_omits_rows_from_report(): void
    {
        config(['stack-health.checks_disabled' => ['php_version']]);

        $sections = app(StackHealthCheckRegistry::class)->buildSections();
        $runtime = $sections[0];
        $this->assertSame(__('stack-health::stack-health.sections.runtime'), $runtime['title']);

        $names = array_map(static fn (array $row): string => $row['name'], $runtime['items']);
        $this->assertNotContains(__('stack-health::stack-health.checks.php_version'), $names);
    }

    public function test_registry_returns_runtime_fallback_row_when_check_throws(): void
    {
        config([
            'stack-health.sections' => [
                [
                    'title_key' => 'stack-health::stack-health.sections.runtime',
                    'checks' => [ExplodingRegistryCheck::class],
                ],
            ],
            'stack-health.checks_disabled' => [],
        ]);

        $sections = app(StackHealthCheckRegistry::class)->buildSections();
        $this->assertCount(1, $sections);
        $this->assertCount(1, $sections[0]['items']);
        $this->assertFalse($sections[0]['items'][0]['ok']);
        $this->assertSame(
            __('stack-health::stack-health.messages.error_check_runtime', [
                'check' => __('stack-health::stack-health.messages.unknown_check_label', ['id' => 'exploding_registry_check']),
            ]),
            $sections[0]['items'][0]['message']
        );
    }
}

final class ExplodingRegistryCheck implements StackHealthCheckContract
{
    public static function id(): string
    {
        return 'exploding_registry_check';
    }

    public function run(): array
    {
        throw new RuntimeException('kaboom');
    }
}
