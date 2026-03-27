<?php

namespace JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Contracts;

use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Data\StackHealthItemResult;

/**
 * Pluggable stack-health probe: stable id for config toggles and {@see run()} for one or more UI rows.
 */
interface StackHealthCheckContract
{
    /**
     * Stable key matched against configured checks and {@see STACK_HEALTH_CHECKS_DISABLED}.
     */
    public static function id(): string;

    /**
     * Execute the probe(s); most checks return a single item, composite checks (e.g. PHP extensions) return several.
     *
     * @return list<StackHealthItemResult>
     */
    public function run(): array;
}
