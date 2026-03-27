<?php

namespace JustGetSchwifty\LaravelStackHealth\Tests\Unit;

use Illuminate\Http\Request;
use JustGetSchwifty\LaravelStackHealth\Http\Middleware\EnsureStackHealthDashboardEnabled;
use JustGetSchwifty\LaravelStackHealth\Tests\TestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EnsureStackHealthDashboardEnabledTest extends TestCase
{
    protected function tearDown(): void
    {
        putenv('APP_ENV=testing');
        $_ENV['APP_ENV'] = 'testing';
        $_SERVER['APP_ENV'] = 'testing';

        parent::tearDown();
    }

    public function test_middleware_aborts_when_disabled_outside_local(): void
    {
        config(['app.env' => 'production', 'stack-health.enabled' => false]);

        $middleware = new EnsureStackHealthDashboardEnabled();

        $this->expectException(NotFoundHttpException::class);

        $middleware->handle(Request::create('/healthcheck', 'GET'), static fn () => response('ok'));
    }

    public function test_middleware_allows_in_local_even_if_disabled(): void
    {
        putenv('APP_ENV=local');
        $_ENV['APP_ENV'] = 'local';
        $_SERVER['APP_ENV'] = 'local';
        config(['app.env' => 'local', 'stack-health.enabled' => false]);

        $middleware = new EnsureStackHealthDashboardEnabled();
        $response = $middleware->handle(Request::create('/healthcheck', 'GET'), static fn () => response('ok'));

        $this->assertSame(200, $response->getStatusCode());
    }
}
