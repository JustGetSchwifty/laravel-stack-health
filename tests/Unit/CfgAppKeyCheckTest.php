<?php

namespace JustGetSchwifty\LaravelStackHealth\Tests\Unit;

use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks\CfgAppKeyCheck;
use JustGetSchwifty\LaravelStackHealth\Tests\TestCase;

class CfgAppKeyCheckTest extends TestCase
{
    public function test_fails_when_key_missing(): void
    {
        config(['app.key' => '']);

        $items = (new CfgAppKeyCheck)->run();

        $this->assertFalse($items[0]->ok);
    }

    public function test_passes_for_valid_base64_key(): void
    {
        $raw = str_repeat('a', 32);
        config(['app.key' => 'base64:'.base64_encode($raw)]);

        $items = (new CfgAppKeyCheck)->run();

        $this->assertTrue($items[0]->ok);
    }
}
