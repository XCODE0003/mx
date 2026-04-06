<?php

namespace App\Http\Middleware;

use App\Models\ReferralLink;
use App\Models\ReferralVisit;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackReferral
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->has('ref')) {
            $code = $request->get('ref');
            $referralLink = ReferralLink::where('code', $code)
                ->where('is_active', true)
                ->first();

            if ($referralLink) {
                // Сохраняем код в сессии
                session(['referral_code' => $code]);
                
                // Увеличиваем счетчик кликов
                $referralLink->incrementClicks();

                // Записываем визит
                ReferralVisit::create([
                    'referral_link_id' => $referralLink->id,
                    'user_id' => auth()->id(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'visited_at' => now(),
                ]);
            }
        }

        return $next($request);
    }
}
