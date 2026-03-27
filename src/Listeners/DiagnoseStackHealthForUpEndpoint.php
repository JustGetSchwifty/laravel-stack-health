<?php

namespace JustGetSchwifty\LaravelStackHealth\Listeners;

use Illuminate\Foundation\Events\DiagnosingHealth;
use Illuminate\Support\Facades\DB;

class DiagnoseStackHealthForUpEndpoint
{
    public function handle(DiagnosingHealth $event): void
    {
        if (! (bool) config('stack-health.up_monitoring_enabled', true)) {
            return;
        }

        DB::connection()->getPdo();
        DB::select('select 1');
    }
}
