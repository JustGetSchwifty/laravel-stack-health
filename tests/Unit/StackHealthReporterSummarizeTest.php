<?php

namespace JustGetSchwifty\LaravelStackHealth\Tests\Unit;

use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\StackHealthCheckRegistry;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealthReporter;
use JustGetSchwifty\LaravelStackHealth\Tests\TestCase;

class StackHealthReporterSummarizeTest extends TestCase
{
    private function reporter(): StackHealthReporter
    {
        return new StackHealthReporter($this->createStub(StackHealthCheckRegistry::class));
    }

    public function test_summarize_counts_and_state(): void
    {
        $reporter = $this->reporter();
        $sections = [
            [
                'title' => 'A',
                'items' => [
                    ['name' => 'x', 'ok' => true, 'message' => ''],
                    ['name' => 'y', 'ok' => false, 'message' => ''],
                    ['name' => 'z', 'ok' => null, 'message' => ''],
                ],
            ],
        ];

        $s = $reporter->summarize($sections);

        $this->assertSame(3, $s['total']);
        $this->assertSame(1, $s['pass']);
        $this->assertSame(1, $s['fail']);
        $this->assertSame(1, $s['warn']);
        $this->assertSame('fail', $s['state']);
    }

    public function test_summarize_warn_state_when_no_failures(): void
    {
        $reporter = $this->reporter();
        $sections = [
            [
                'title' => 'A',
                'items' => [
                    ['name' => 'x', 'ok' => true, 'message' => ''],
                    ['name' => 'y', 'ok' => null, 'message' => ''],
                ],
            ],
        ];

        $s = $reporter->summarize($sections);

        $this->assertSame('warn', $s['state']);
    }

    public function test_summarize_ok_when_all_pass(): void
    {
        $reporter = $this->reporter();
        $sections = [
            [
                'title' => 'A',
                'items' => [
                    ['name' => 'x', 'ok' => true, 'message' => ''],
                ],
            ],
        ];

        $this->assertSame('ok', $reporter->summarize($sections)['state']);
    }

    public function test_summarize_for_view_includes_headline_and_percentages(): void
    {
        $reporter = $this->reporter();
        $sections = [
            [
                'title' => 'A',
                'items' => [
                    ['name' => 'x', 'ok' => true, 'message' => ''],
                    ['name' => 'y', 'ok' => true, 'message' => ''],
                ],
            ],
        ];

        $v = $reporter->summarizeForView($sections);

        $this->assertSame(2, $v['total']);
        $this->assertSame('ok', $v['state']);
        $this->assertSame(100.0, $v['pct_pass']);
        $this->assertSame(0.0, $v['pct_warn']);
        $this->assertSame(0.0, $v['pct_fail']);
        $this->assertArrayHasKey('headline', $v);
        $this->assertArrayHasKey('mark', $v);
    }
}
