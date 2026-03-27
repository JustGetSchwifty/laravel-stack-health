<?php

namespace JustGetSchwifty\LaravelStackHealth\Providers;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Events\DiagnosingHealth;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use JustGetSchwifty\LaravelStackHealth\Http\Controllers\StackHealthController;
use JustGetSchwifty\LaravelStackHealth\Http\Middleware\EnsureStackHealthDashboardEnabled;
use JustGetSchwifty\LaravelStackHealth\Listeners\DiagnoseStackHealthForUpEndpoint;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks\OutboundHttpCheck;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\StackHealthCheckCatalog;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\StackHealthCheckRegistry;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\StackHealthDashboardRouteCollisionDetector;

class StackHealthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/stack-health.php', 'stack-health');

        $this->app->singleton(StackHealthCheckCatalog::class);
        $this->app->singleton(StackHealthCheckRegistry::class);

        $this->app->when(OutboundHttpCheck::class)
            ->needs(ClientInterface::class)
            ->give(static fn (): ClientInterface => new Client);
    }

    public function boot(): void
    {
        $this->registerSchedulerHeartbeatSchedule();

        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'stack-health');
        $this->loadTranslationsFrom(__DIR__.'/../../resources/lang', 'stack-health');

        $this->publishes([
            __DIR__.'/../../config/stack-health.php' => config_path('stack-health.php'),
        ], 'stack-health-config');
        $this->publishes([
            __DIR__.'/../../resources/views' => resource_path('views/vendor/stack-health'),
        ], 'stack-health-views');
        $this->publishes([
            __DIR__.'/../../resources/lang' => $this->app->langPath('vendor/stack-health'),
        ], 'stack-health-lang');

        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('stack.health', EnsureStackHealthDashboardEnabled::class);

        Event::listen(DiagnosingHealth::class, DiagnoseStackHealthForUpEndpoint::class);

        $this->app->booted(function () use ($router): void {
            $dashboardUri = (string) config('stack-health.dashboard_uri', '/healthcheck');
            $routePath = $dashboardUri === '/' ? '/' : ltrim($dashboardUri, '/');
            $routeName = (string) config('stack-health.dashboard_route_name', 'stack-health.dashboard');
            $routeMiddleware = config('stack-health.dashboard_route_middleware', 'stack.health');
            $routeMiddlewareList = is_array($routeMiddleware)
                ? $routeMiddleware
                : explode(',', (string) $routeMiddleware);
            $routeMiddlewareList = array_values(array_filter(array_map(
                static fn (string $entry): string => trim($entry),
                $routeMiddlewareList
            )));
            if ($routeMiddlewareList === []) {
                $routeMiddlewareList = ['stack.health'];
            }

            StackHealthDashboardRouteCollisionDetector::assertGetUriAvailable($router, $routePath);

            $router->middleware($routeMiddlewareList)
                ->get($routePath, StackHealthController::class)
                ->name($routeName);
        });
    }

    /**
     * Register a one-minute heartbeat task so {@see \JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks\SchedulerHeartbeatCheck}
     * passes when `php artisan schedule:work` (or system cron) runs the Laravel scheduler.
     */
    private function registerSchedulerHeartbeatSchedule(): void
    {
        $app = $this->app;

        $register = static function (Schedule $schedule) use ($app): void {
            if (! filter_var($app['config']->get('stack-health.register_scheduler_heartbeat', true), FILTER_VALIDATE_BOOLEAN)) {
                return;
            }

            $schedule->call(static function () use ($app): void {
                $path = $app['config']->get('stack-health.scheduler_heartbeat_path');
                if (! is_string($path) || $path === '') {
                    $path = $app->storagePath('app/.scheduler-heartbeat');
                }

                file_put_contents($path, (string) time(), LOCK_EX);
            })
                ->everyMinute()
                ->name('stack-health-scheduler-heartbeat')
                ->withoutOverlapping();
        };

        $app->afterResolving(Schedule::class, $register);

        if ($app->resolved(Schedule::class)) {
            $register($app->make(Schedule::class));
        }
    }
}
