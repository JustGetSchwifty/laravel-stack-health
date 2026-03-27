<?php

namespace JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks;

use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Contracts\StackHealthCheckContract;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Data\StackHealthItemResult;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\StackHealthMessageRedactor;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Opens the default DB connection and runs a trivial query to catch misconfiguration or network issues early.
 */
final class DatabaseCheck implements StackHealthCheckContract
{
    public function __construct(
        private StackHealthMessageRedactor $redactor,
    ) {}

    public static function id(): string
    {
        return 'database';
    }

    /**
     * @return list<StackHealthItemResult>
     */
    public function run(): array
    {
        try {
            DB::connection()->getPdo();
            DB::select('select 1 as ok');

            return [
                new StackHealthItemResult(
                    __('stack-health::stack-health.checks.database'),
                    true,
                    __('stack-health::stack-health.messages.database_ok', ['driver' => config('database.default')]),
                ),
            ];
        } catch (Throwable $e) {
            return [
                new StackHealthItemResult(
                    __('stack-health::stack-health.checks.database'),
                    false,
                    $this->redactor->exceptionMessageForUi($e, 'stack-health::stack-health.messages.error_database'),
                ),
            ];
        }
    }
}
