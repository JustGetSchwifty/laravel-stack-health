<?php

namespace JustGetSchwifty\LaravelStackHealth\Services\StackHealth;

use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Contracts\StackHealthCheckContract;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Data\StackHealthItemResult;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Resolves enabled {@see StackHealthCheckContract} classes from config and flattens their results for the view.
 *
 * Section order and check order follow {@see config('stack-health.sections')} so the dashboard stays predictable.
 */
class StackHealthCheckRegistry
{
    public function __construct(
        private Application $app,
        private StackHealthCheckRunner $checkRunner,
        private StackHealthCheckCatalog $checkCatalog,
    ) {}

    /**
     * @return array<int, array{title: string, items: list<array{name: string, ok: bool|null, message: string}>}>
     */
    public function buildSections(): array
    {
        /** @var list<array{title_key: string, checks: list<class-string<StackHealthCheckContract>>}> $sections */
        $sections = config('stack-health.sections', []);
        $catalog = $this->checkCatalog->describe();
        /** @var array<string, bool> $enabled */
        $enabled = $catalog['enabled_map'];
        /** @var list<string> $unknownDisabledIds */
        $unknownDisabledIds = $catalog['disabled_unknown_ids'];
        /** @var list<array{id: string, kept: class-string<StackHealthCheckContract>, ignored: class-string<StackHealthCheckContract>}> $duplicateIdConflicts */
        $duplicateIdConflicts = $catalog['duplicate_id_conflicts'];
        $out = [];

        if ($unknownDisabledIds !== []) {
            Log::warning('Stack health received unknown disabled check IDs.', [
                'unknown_ids' => $unknownDisabledIds,
            ]);
        }
        if ($duplicateIdConflicts !== []) {
            Log::warning('Stack health detected duplicate check IDs and ignored later classes.', [
                'conflicts' => $duplicateIdConflicts,
            ]);
        }

        foreach ($sections as $section) {
            $titleKey = $section['title_key'] ?? '';
            $classes = $section['checks'] ?? [];
            $items = [];

            foreach ($classes as $class) {
                if (! is_string($class) || ! class_exists($class)) {
                    Log::warning('Stack health skipped invalid check class entry.', [
                        'class' => $class,
                    ]);
                    continue;
                }
                if (! is_subclass_of($class, StackHealthCheckContract::class)) {
                    Log::warning('Stack health skipped class that does not implement contract.', [
                        'class' => $class,
                    ]);
                    continue;
                }

                $id = $class::id();
                if (($enabled[$id] ?? true) !== true) {
                    continue;
                }

                try {
                    $check = $this->app->make($class);
                    if (! $check instanceof StackHealthCheckContract) {
                        Log::warning('Stack health container resolved invalid check instance.', [
                            'class' => $class,
                            'resolved_type' => get_debug_type($check),
                        ]);
                        continue;
                    }
                } catch (Throwable $e) {
                    report($e);
                    $items[] = $this->runtimeErrorItem($id)->toViewArray();

                    continue;
                }

                foreach ($this->checkRunner->run($check) as $result) {
                    $items[] = $result->toViewArray();
                }
            }

            $out[] = [
                'title' => $titleKey !== '' ? __($titleKey) : '',
                'items' => $items,
            ];
        }

        return $out;
    }

    private function runtimeErrorItem(string $id): StackHealthItemResult
    {
        $label = __('stack-health::stack-health.checks.'.$id);
        if ($label === 'stack-health::stack-health.checks.'.$id) {
            $label = __('stack-health::stack-health.messages.unknown_check_label', ['id' => $id]);
        }

        return new StackHealthItemResult(
            $label,
            false,
            __('stack-health::stack-health.messages.error_check_runtime', ['check' => $label]),
        );
    }
}
