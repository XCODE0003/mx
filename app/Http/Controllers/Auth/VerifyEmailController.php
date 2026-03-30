<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect('/profile')->with('status', 'Email уже подтверждён');
        }

        if ($request->fulfill()) {
            return redirect('/profile')->with('status', 'Email успешно подтверждён!');
        }

        return redirect('/profile')->with('error', 'Не удалось подтвердить email');
    }
}
