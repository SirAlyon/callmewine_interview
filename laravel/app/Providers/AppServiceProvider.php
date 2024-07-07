<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Predis\Client as PredisClient;
use GuzzleHttp\Client as GuzzleClient;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PredisClient::class, function ($app) {
            return new PredisClient([
                'scheme' => 'tcp',
                'host'   => env('REDIS_HOST'),
                'port'   => env('REDIS_PORT'),
            ]);
        });

        $this->app->singleton(GuzzleClient::class, function ($app) {
            return new GuzzleClient([
                'base_uri' => 'https://pro-api.coinmarketcap.com/',
                'timeout'  => 2.0,
            ]);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
