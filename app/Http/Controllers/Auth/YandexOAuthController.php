<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ReferralLink;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class YandexOAuthController extends Controller
{
    /**
     * Redirect to Yandex OAuth
     */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('yandex')->redirect();
    }

    /**
     * Handle Yandex OAuth callback
     */
    public function callback(): RedirectResponse
    {
        try {
            $yandexUser = Socialite::driver('yandex')->user();

            $user = User::where('email', $yandexUser->getEmail())->first();

            if (!$user) {
                $referralLinkId = null;
                if (session()->has('referral_code')) {
                    $referralLink = ReferralLink::where('code', session()->get('referral_code'))
                        ->where('is_active', true)
                        ->first();
                    if ($referralLink) {
                        $referralLinkId = $referralLink->id;
                    }
                }

                // Создаём нового пользователя
                $user = User::create([
                    'email' => $yandexUser->getEmail(),
                    'name' => $yandexUser->getName() ?? $yandexUser->getNickname(),
                    'password' => Hash::make(Str::random(24)),
                    'email_verified_at' => now(),
                    'yandex_id' => $yandexUser->getId(),
                    'referral_link_id' => $referralLinkId,
                ]);
            } else {
                // Обновляем Yandex ID, если его нет
                if (!$user->yandex_id) {
                    $user->update([
                        'yandex_id' => $yandexUser->getId(),
                        'email_verified_at' => $user->email_verified_at ?? now(),
                    ]);
                }
            }

            Auth::login($user, true);

            return redirect('/profile')->with('status', 'Вы успешно вошли через Яндекс!');

        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Ошибка при входе через Яндекс. Попробуйте ещё раз.');
        }
    }
}
