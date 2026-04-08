<?php

use App\Http\Controllers\BanksApiController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\TaskExportController;
use App\Http\Controllers\TaskDownloadController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('/test', function () {
    return Inertia::render('test');
})->name('test');
Route::get('/login', function () {
    return Inertia::render('Auth/Login');
})->name('login');

Route::get('/signup', function () {
    return Inertia::render('Auth/Signup');
})->name('signup');

Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews');
Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');

Route::get('/news', [NewsController::class, 'index'])->name('news');
Route::get('/news/{slug}', [NewsController::class, 'show'])->name('news.show');

Route::get('/faq', function () {
    return Inertia::render('Faq');
})->name('faq');

Route::get('/banks', function () {
    return Inertia::render('Banks');
})->name('banks');

Route::get('/policy', function () {
    return Inertia::render('Policy');
})->name('policy');

Route::get('/contacts', function () {
    return Inertia::render('Contacts');
})->name('contacts');

Route::get('/about', function () {
    return Inertia::render('About');
})->name('about');

Route::prefix('banks-api')->middleware('throttle:180,1')->group(function () {
    Route::get('/subjects/{grade}', [BanksApiController::class, 'subjects'])->name('banks.api.subjects');
    Route::get('/groups/{grade}/{subject}', [BanksApiController::class, 'groups'])->name('banks.api.groups');
    Route::get('/tasks/{group_id}', [BanksApiController::class, 'tasks'])->name('banks.api.tasks');
});

Route::get('/admin/tasks/{task}/prepare-download', [TaskDownloadController::class, 'prepare'])->name('admin.tasks.prepare-download');
Route::get('/admin/tasks/{task}/download-status', [TaskDownloadController::class, 'checkStatus'])->name('admin.tasks.download-status');

Route::get('/tasks/{task}/view', [TaskExportController::class, 'view'])->name('tasks.view');
Route::get('/tasks/{task}/view_tasks', [TaskExportController::class, 'viewTasks'])->name('tasks.view_tasks');
Route::get('/tasks/{task}/view_tasks_word', [TaskExportController::class, 'viewTasksWord'])->name('tasks.view_tasks_word');
Route::get('/tasks/{task}/download', [TaskExportController::class, 'exportPdf'])->name('tasks.download');
Route::post('/manual/tasks/download', [TaskExportController::class, 'exportPdfManual'])->name('tasks.download.manual');
Route::get('/manual/tasks/status/{taskId}/{file}', [TaskExportController::class, 'checkReady'])->name('tasks.download.manual.status');

Route::post('/auto/tasks/download', [TaskExportController::class, 'exportPdfAuto'])->name('tasks.download.auto');
Route::get('/auto/tasks/status/{taskId}/{file}', [TaskExportController::class, 'checkReady'])->name('tasks.download.auto.status');

Route::get('/variants/{uuid}/download', [TaskExportController::class, 'downloadVariant'])
    ->name('variants.download')
    ->whereUuid('uuid');
Route::get('/variants/{uuid}/status', [TaskExportController::class, 'variantStatus'])
    ->name('variants.status')
    ->whereUuid('uuid');
Route::post('/variants/{uuid}/queue', [TaskExportController::class, 'queueVariantGeneration'])
    ->middleware('throttle:30,1')
    ->name('variants.queue')
    ->whereUuid('uuid');
Route::post('/variants/{uuid}/regenerate', [TaskExportController::class, 'regenerateVariant'])
    ->middleware('auth')
    ->name('variants.regenerate')
    ->whereUuid('uuid');



require __DIR__.'/auth.php';
require __DIR__.'/profile.php';
require __DIR__.'/settings.php';
