<?php

namespace JustGetSchwifty\LaravelStackHealth\Tests\Unit;

use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Contracts\StackHealthCheckContract;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\StackHealthCheckCatalog;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Data\StackHealthItemResult;
use JustGetSchwifty\LaravelStackHealth\Tests\TestCase;

class StackHealthCheckCatalogTest extends TestCase
{
    public function test_catalog_builds_enabled_map_and_unknown_disabled_ids(): void
    {
        config([
            'stack-health.sections' => [
                [
                    'title_key' => 'stack-health.sections.runtime',
                    'checks' => [CatalogCheckOne::class, CatalogCheckTwo::class],
                ],
            ],
            'stack-health.checks_disabled' => ['catalog_check_two', 'not_registered'],
        ]);

        $description = app(StackHealthCheckCatalog::class)->describe();

        $this->assertSame(
            [
                'catalog_check_one' => CatalogCheckOne::class,
                'catalog_check_two' => CatalogCheckTwo::class,
            ],
            $description['class_map']
        );
        $this->assertSame(
            [
                'catalog_check_one' => true,
                'catalog_check_two' => false,
            ],
            $description['enabled_map']
        );
        $this->assertSame(['not_registered'], $description['disabled_unknown_ids']);
    }

    public function test_catalog_detects_duplicate_ids_and_keeps_first_class(): void
    {
        config([
            'stack-health.sections' => [
                [
                    'title_key' => 'stack-health.sections.runtime',
                    'checks' => [CatalogDuplicateFirst::class, CatalogDuplicateSecond::class],
                ],
            ],
            'stack-health.checks_disabled' => [],
        ]);

        $description = app(StackHealthCheckCatalog::class)->describe();

        $this->assertSame(
            ['duplicate_catalog_id' => CatalogDuplicateFirst::class],
            $description['class_map']
        );
        $this->assertCount(1, $description['duplicate_id_conflicts']);
        $this->assertSame('duplicate_catalog_id', $description['duplicate_id_conflicts'][0]['id']);
        $this->assertSame(CatalogDuplicateFirst::class, $description['duplicate_id_conflicts'][0]['kept']);
        $this->assertSame(CatalogDuplicateSecond::class, $description['duplicate_id_conflicts'][0]['ignored']);
    }
}

final class CatalogCheckOne implements StackHealthCheckContract
{
    public static function id(): string
    {
        return 'catalog_check_one';
    }

    public function run(): array
    {
        return [new StackHealthItemResult('One', true, 'ok')];
    }
}

final class CatalogCheckTwo implements StackHealthCheckContract
{
    public static function id(): string
    {
        return 'catalog_check_two';
    }

    public function run(): array
    {
        return [new StackHealthItemResult('Two', true, 'ok')];
    }
}

final class CatalogDuplicateFirst implements StackHealthCheckContract
{
    public static function id(): string
    {
        return 'duplicate_catalog_id';
    }

    public function run(): array
    {
        return [new StackHealthItemResult('First', true, 'ok')];
    }
}

final class CatalogDuplicateSecond implements StackHealthCheckContract
{
    public static function id(): string
    {
        return 'duplicate_catalog_id';
    }

    public function run(): array
    {
        return [new StackHealthItemResult('Second', true, 'ok')];
    }
}
