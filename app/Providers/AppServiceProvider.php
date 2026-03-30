<?php

namespace App\Providers;

use App\Socialite\YandexProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
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
        // Принудительно используем HTTPS если APP_URL начинается с https://
        if (str_starts_with(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        // Доверяем прокси-серверам для правильного определения HTTPS
        Request::setTrustedProxies(['*'], Request::HEADER_X_FORWARDED_FOR | 
            Request::HEADER_X_FORWARDED_HOST | 
            Request::HEADER_X_FORWARDED_PORT | 
            Request::HEADER_X_FORWARDED_PROTO | 
            Request::HEADER_X_FORWARDED_AWS_ELB);

        $socialite = $this->app->make(SocialiteFactory::class);
        
        $socialite->extend('yandex', function ($app) use ($socialite) {
            $config = $app['config']['services.yandex'];
            return $socialite->buildProvider(YandexProvider::class, $config);
        });
    }
}
