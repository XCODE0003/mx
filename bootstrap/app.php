<?php

use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\TrackReferral;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/admin.php'));
        }
    )
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('variants:purge-files')->everyFiveMinutes();
        $schedule->command('variants:prune-orders')->dailyAt('03:30');
    })
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);

        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
            TrackReferral::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Throwable $e, Request $request) {
            if (! $e instanceof NotFoundHttpException && ! $e instanceof ModelNotFoundException) {
                return null;
            }
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Страница не найдена.',
                ], 404);
            }

            // Несовпадение маршрута не проходит через группу web → HandleInertiaRequests не вызывается,
            // shared props пустые, в JSON уходит props как [] и фронт Inertia не монтируется страницу.
            Inertia::flushShared();
            $inertia = app(HandleInertiaRequests::class);
            Inertia::version(fn () => $inertia->version($request));
            Inertia::share($inertia->share($request));
            Inertia::setRootView($inertia->rootView($request));

            return Inertia::render('Errors/NotFound')
                ->toResponse($request)
                ->setStatusCode(404);
        });
    })->create();
