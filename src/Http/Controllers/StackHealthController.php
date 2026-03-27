<?php

namespace JustGetSchwifty\LaravelStackHealth\Http\Controllers;

use JustGetSchwifty\LaravelStackHealth\Services\StackHealthReporter;
use Illuminate\View\View;

/**
 * Renders the internal stack-health HTML report built by {@see StackHealthReporter}.
 */
class StackHealthController
{
    /**
     * @return View Blade template stack-health; expects variables sections and summary for the dashboard.
     */
    public function __invoke(StackHealthReporter $reporter): View
    {
        $sections = $reporter->report();

        return view('stack-health::stack-health', [
            'sections' => $sections,
            'summary' => $reporter->summarizeForView($sections),
        ]);
    }
}
