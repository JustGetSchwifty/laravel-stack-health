<?php

namespace JustGetSchwifty\LaravelStackHealth\Tests\Unit;

use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks\OutboundHttpCheck;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\StackHealthMessageRedactor;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use Mockery;
use JustGetSchwifty\LaravelStackHealth\Tests\TestCase;

class OutboundHttpCheckTest extends TestCase
{
    public function test_skipped_when_outbound_disabled_in_config(): void
    {
        config([
            'stack-health.outbound_http' => false,
        ]);

        $client = Mockery::mock(ClientInterface::class);
        $client->shouldNotReceive('request');

        $check = new OutboundHttpCheck(app(StackHealthMessageRedactor::class), $client);
        $items = $check->run();

        $this->assertCount(1, $items);
        $this->assertNull($items[0]->ok);
        $this->assertSame(__('stack-health::stack-health.messages.outbound_disabled'), $items[0]->message);
    }

    public function test_passes_when_head_returns_success_code(): void
    {
        config([
            'stack-health.outbound_http' => true,
            'stack-health.outbound_http_url' => 'https://probe.example.test/path',
            'stack-health.outbound_http_timeout' => 5.0,
            'stack-health.redact_sensitive_messages' => false,
        ]);

        $client = Mockery::mock(ClientInterface::class);
        $client->shouldReceive('request')
            ->once()
            ->with(
                'HEAD',
                'https://probe.example.test/path',
                Mockery::type('array')
            )
            ->andReturn(new Response(204));

        $check = new OutboundHttpCheck(app(StackHealthMessageRedactor::class), $client);
        $items = $check->run();

        $this->assertCount(1, $items);
        $this->assertTrue($items[0]->ok);
        $this->assertStringContainsString('204', $items[0]->message);
    }

    public function test_fails_when_head_returns_error_code(): void
    {
        config([
            'stack-health.outbound_http' => true,
            'stack-health.outbound_http_url' => 'https://probe.example.test/',
            'stack-health.outbound_http_timeout' => 5.0,
            'stack-health.redact_sensitive_messages' => false,
        ]);

        $client = Mockery::mock(ClientInterface::class);
        $client->shouldReceive('request')
            ->once()
            ->andReturn(new Response(503));

        $check = new OutboundHttpCheck(app(StackHealthMessageRedactor::class), $client);
        $items = $check->run();

        $this->assertCount(1, $items);
        $this->assertFalse($items[0]->ok);
    }
}
