<?php

namespace JustGetSchwifty\LaravelStackHealth\Providers;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
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
}
