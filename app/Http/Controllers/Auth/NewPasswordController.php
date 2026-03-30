<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class NewPasswordController extends Controller
{

    public function create(Request $request): Response
    {
        return Inertia::render('Auth/ResetPassword', [
            'email' => $request->email,
            'token' => $request->route('token'),
        ]);
    }


    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', 'min:8'],
        ], [
            'password.required' => 'Укажите новый пароль',
            'password.confirmed' => 'Пароли не совпадают',
            'password.min' => 'Пароль должен содержать минимум 8 символов',
            'email.required' => 'Укажите email',
            'email.email' => 'Некорректный email',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', 'Пароль успешно изменён! Войдите с новым паролем.');
        }

        return back()->withInput($request->only('email'))
            ->withErrors(['email' => $this->getResetErrorMessage($status)]);
    }

    /**
     * Получить понятное сообщение об ошибке
     */
    private function getResetErrorMessage(string $status): string
    {
        return match($status) {
            Password::INVALID_TOKEN => 'Ссылка недействительна или устарела. Запросите новую ссылку для сброса пароля.',
            Password::INVALID_USER => 'Пользователь с таким email не найден.',
            default => 'Не удалось сбросить пароль. Попробуйте ещё раз.',
        };
    }
}
