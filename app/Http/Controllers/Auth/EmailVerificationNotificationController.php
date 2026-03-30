<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect('/profile')->with('status', 'Email уже подтверждён');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'Письмо для подтверждения отправлено повторно!');
    }
}
