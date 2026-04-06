<?php

use App\Http\Controllers\Admin\ReferralController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'admin']], function () {
    // Реферальные ссылки
    Route::get('/referrals', [ReferralController::class, 'index'])->name('admin.referrals.index');
    Route::post('/referrals', [ReferralController::class, 'store'])->name('admin.referrals.store');
    Route::get('/referrals/{link}', [ReferralController::class, 'show'])->name('admin.referrals.show');
    Route::put('/referrals/{link}', [ReferralController::class, 'update'])->name('admin.referrals.update');
    Route::delete('/referrals/{link}', [ReferralController::class, 'destroy'])->name('admin.referrals.destroy');
    
    // Пользователи
    Route::get('/users', [ReferralController::class, 'users'])->name('admin.users.index');
});
