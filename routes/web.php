<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\ReviewController;
use App\Http\Controllers\TaskExportController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('/test', function () {
    return Inertia::render('test');
})->name('home');
Route::get('/login', function () {
    return Inertia::render('Auth/Login');
})->name('login');

Route::get('/signup', function () {
    return Inertia::render('Auth/Signup');
})->name('signup');


Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews');
Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');

Route::get('/news', function () {
    return Inertia::render('News');
})->name('news');


Route::get('/faq', function () {
    return Inertia::render('Faq');
})->name('faq');

Route::get('/banks', function () {
    return Inertia::render('Banks');
})->name('banks');

// Экспорт одного задания в PDF
Route::get('/tasks/{task}/view', [TaskExportController::class, 'view'])->name('tasks.view');
Route::get('/tasks/{task}/view_tasks', [TaskExportController::class, 'viewTasks'])->name('tasks.view_tasks');
Route::get('/tasks/{task}/download', [TaskExportController::class, 'exportPdf'])->name('tasks.download');
Route::post('/manual/tasks/download', [TaskExportController::class, 'exportPdfManual'])->name('tasks.download.manual');
Route::get('/manual/tasks/status/{taskId}/{file}', [TaskExportController::class, 'checkReady'])->name('tasks.download.manual.status');

require __DIR__.'/auth.php';
require __DIR__.'/profile.php';
require __DIR__.'/settings.php';