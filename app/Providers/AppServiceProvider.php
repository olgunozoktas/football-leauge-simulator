<?php

namespace App\Providers;

use App\Services\FixtureService;
use App\Services\MatchService;
use App\Services\PredictionService;
use App\Services\SimulationService;
use App\Services\SimulationStateService;
use App\Services\TeamService;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(TeamService::class);
        $this->app->singleton(FixtureService::class);

        $this->app->singleton(MatchService::class, function ($app) {
            return new MatchService($app->make(TeamService::class));
        });

        $this->app->singleton(PredictionService::class, function ($app) {
            return new PredictionService($app->make(MatchService::class));
        });

        $this->app->singleton(SimulationStateService::class);

        $this->app->singleton(SimulationService::class, function ($app) {
            return new SimulationService(
                $app->make(TeamService::class),
                $app->make(FixtureService::class),
                $app->make(MatchService::class),
                $app->make(PredictionService::class),
                $app->make(SimulationStateService::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
