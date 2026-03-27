<?php

namespace JustGetSchwifty\LaravelStackHealth\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use JustGetSchwifty\LaravelStackHealth\Providers\StackHealthServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [StackHealthServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.key', 'base64:'.base64_encode(str_repeat('a', 32)));
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    /**
     * Path or URI prefix for the stack health dashboard (matches {@see config('stack-health.dashboard_uri')}).
     */
    protected function stackHealthDashboardUri(): string
    {
        $uri = (string) config('stack-health.dashboard_uri', '/healthcheck');

        return $uri === '/' ? '/' : $uri;
    }
}
