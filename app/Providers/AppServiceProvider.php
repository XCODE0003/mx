<?php

namespace App\Providers;

use App\Socialite\YandexProvider;
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $socialite = $this->app->make(SocialiteFactory::class);
        
        $socialite->extend('yandex', function ($app) use ($socialite) {
            $config = $app['config']['services.yandex'];
            return $socialite->buildProvider(YandexProvider::class, $config);
        });
    }
}
