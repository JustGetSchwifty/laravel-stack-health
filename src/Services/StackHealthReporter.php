<?php

namespace JustGetSchwifty\LaravelStackHealth\Services;

use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\StackHealthCheckRegistry;

/**
 * Builds the stack-health dashboard dataset and derives aggregate counts for the Blade view.
 *
 * Individual probes live in {@see \App\Services\StackHealth\Checks} and are wired via {@see config('stack-health.sections')}.
 */
class StackHealthReporter
{
    public function __construct(
        private StackHealthCheckRegistry $checkRegistry,
    ) {}

    /**
     * @return array<int, array{title: string, items: list<array{name: string, ok: bool|null, message: string}>}>
     */
    public function report(): array
    {
        return $this->checkRegistry->buildSections();
    }

    /**
     * @param  array<int, array{title: string, items: list<array{name: string, ok: bool|null, message: string}>}>  $sections
     * @return array{total: int, pass: int, fail: int, warn: int, state: 'ok'|'warn'|'fail'}
     */
    public function summarize(array $sections): array
    {
        $pass = 0;
        $fail = 0;
        $warn = 0;

        foreach ($sections as $section) {
            foreach ($section['items'] as $item) {
                if ($item['ok'] === true) {
                    $pass++;
                } elseif ($item['ok'] === false) {
                    $fail++;
                } else {
                    $warn++;
                }
            }
        }

        $total = $pass + $fail + $warn;
        $state = $fail > 0 ? 'fail' : ($warn > 0 ? 'warn' : 'ok');

        return [
            'total' => $total,
            'pass' => $pass,
            'fail' => $fail,
            'warn' => $warn,
            'state' => $state,
        ];
    }

    /**
     * Summary plus precomputed strings and percentages for the dashboard view.
     *
     * @param  array<int, array{title: string, items: list<array{name: string, ok: bool|null, message: string}>}>  $sections
     * @return array{
     *     total: int, pass: int, fail: int, warn: int, state: 'ok'|'warn'|'fail',
     *     headline: string, mark: string, pct_pass: float, pct_warn: float, pct_fail: float
     * }
     */
    public function summarizeForView(array $sections): array
    {
        $summary = $this->summarize($sections);
        $sTotal = $summary['total'];
        $sPass = $summary['pass'];
        $sFail = $summary['fail'];
        $sWarn = $summary['warn'];
        $sState = $summary['state'];
        $den = max(1, $sTotal);
        $pctPass = $sTotal > 0 ? round(100 * $sPass / $den, 2) : 0.0;
        $pctWarn = $sTotal > 0 ? round(100 * $sWarn / $den, 2) : 0.0;
        $pctFail = $sTotal > 0 ? round(100 * $sFail / $den, 2) : 0.0;
        $mark = $sState === 'ok' ? '✓' : ($sState === 'warn' ? '!' : '✕');
        $headline = match (true) {
            $sTotal === 0 => __('stack-health::stack-health.summary.headline_empty'),
            $sState === 'ok' => __('stack-health::stack-health.summary.headline_ok'),
            $sState === 'warn' => __('stack-health::stack-health.summary.headline_warn'),
            default => __('stack-health::stack-health.summary.headline_fail'),
        };

        return [
            ...$summary,
            'headline' => $headline,
            'mark' => $mark,
            'pct_pass' => $pctPass,
            'pct_warn' => $pctWarn,
            'pct_fail' => $pctFail,
        ];
    }
}
