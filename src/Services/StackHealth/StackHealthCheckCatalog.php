<?php

namespace JustGetSchwifty\LaravelStackHealth\Services\StackHealth;

use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Contracts\StackHealthCheckContract;

/**
 * Builds normalized metadata for configured check classes and per-check enabled state.
 */
final class StackHealthCheckCatalog
{
    /**
     * @var array{
     *   class_map: array<string, class-string<StackHealthCheckContract>>,
     *   enabled_map: array<string, bool>,
     *   disabled_unknown_ids: list<string>,
     *   duplicate_id_conflicts: list<array{id: string, kept: class-string<StackHealthCheckContract>, ignored: class-string<StackHealthCheckContract>}>
     * }|null
     */
    private ?array $cachedDescription = null;

    /**
     * @return array{
     *   class_map: array<string, class-string<StackHealthCheckContract>>,
     *   enabled_map: array<string, bool>,
     *   disabled_unknown_ids: list<string>,
     *   duplicate_id_conflicts: list<array{id: string, kept: class-string<StackHealthCheckContract>, ignored: class-string<StackHealthCheckContract>}>
     * }
     */
    public function describe(): array
    {
        if ($this->cachedDescription !== null) {
            return $this->cachedDescription;
        }

        /** @var list<array{title_key?: string, checks?: list<mixed>}> $sections */
        $sections = config('stack-health.sections', []);
        /** @var list<string> $disabled */
        $disabled = config('stack-health.checks_disabled', []);

        $classMap = [];
        $duplicateIdConflicts = [];
        foreach ($sections as $section) {
            foreach (($section['checks'] ?? []) as $class) {
                if (! is_string($class) || ! class_exists($class)) {
                    continue;
                }
                if (! is_subclass_of($class, StackHealthCheckContract::class)) {
                    continue;
                }
                $id = $class::id();
                if (! is_string($id) || $id === '') {
                    continue;
                }
                if (isset($classMap[$id])) {
                    $duplicateIdConflicts[] = [
                        'id' => $id,
                        'kept' => $classMap[$id],
                        'ignored' => $class,
                    ];

                    continue;
                }
                $classMap[$id] = $class;
            }
        }

        $enabledMap = array_fill_keys(array_keys($classMap), true);
        foreach ($disabled as $id) {
            if (array_key_exists($id, $enabledMap)) {
                $enabledMap[$id] = false;
            }
        }

        $this->cachedDescription = [
            'class_map' => $classMap,
            'enabled_map' => $enabledMap,
            'disabled_unknown_ids' => array_values(array_diff($disabled, array_keys($classMap))),
            'duplicate_id_conflicts' => $duplicateIdConflicts,
        ];

        return $this->cachedDescription;
    }
}
