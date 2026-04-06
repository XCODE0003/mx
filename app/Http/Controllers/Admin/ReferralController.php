<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReferralLink;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ReferralController extends Controller
{
    public function index()
    {
        $links = ReferralLink::withCount(['visits', 'users', 'orders'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($link) {
                return [
                    'id' => $link->id,
                    'code' => $link->code,
                    'name' => $link->name,
                    'description' => $link->description,
                    'url' => $link->url,
                    'clicks' => $link->clicks,
                    'visits_count' => $link->visits_count,
                    'registrations' => $link->users_count,
                    'purchases' => $link->orders_count,
                    'revenue' => $link->total_revenue,
                    'is_active' => $link->is_active,
                    'created_at' => $link->created_at->format('d.m.Y H:i'),
                ];
            });

        return Inertia::render('Admin/Referrals/Index', [
            'links' => $links,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'code' => 'nullable|string|max:50|unique:referral_links,code',
        ]);

        if (empty($validated['code'])) {
            $validated['code'] = Str::random(10);
        }

        $link = ReferralLink::create($validated);

        return redirect()->back()->with('success', 'Реферальная ссылка создана');
    }

    public function update(Request $request, ReferralLink $link)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $link->update($validated);

        return redirect()->back()->with('success', 'Реферальная ссылка обновлена');
    }

    public function destroy(ReferralLink $link)
    {
        $link->delete();

        return redirect()->back()->with('success', 'Реферальная ссылка удалена');
    }

    public function show(ReferralLink $link)
    {
        $link->load(['visits.user', 'users', 'orders.user']);

        $stats = [
            'link' => [
                'id' => $link->id,
                'code' => $link->code,
                'name' => $link->name,
                'description' => $link->description,
                'url' => $link->url,
                'clicks' => $link->clicks,
                'is_active' => $link->is_active,
                'created_at' => $link->created_at->format('d.m.Y H:i'),
            ],
            'visits' => $link->visits->map(function ($visit) {
                return [
                    'id' => $visit->id,
                    'user' => $visit->user ? [
                        'id' => $visit->user->id,
                        'name' => $visit->user->name,
                        'email' => $visit->user->email,
                    ] : null,
                    'ip_address' => $visit->ip_address,
                    'user_agent' => $visit->user_agent,
                    'visited_at' => $visit->visited_at->format('d.m.Y H:i:s'),
                ];
            }),
            'registrations' => $link->users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'created_at' => $user->created_at->format('d.m.Y H:i'),
                ];
            }),
            'purchases' => $link->orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'user' => $order->user ? [
                        'id' => $order->user->id,
                        'name' => $order->user->name,
                        'email' => $order->user->email,
                    ] : null,
                    'price' => $order->price,
                    'created_at' => $order->created_at->format('d.m.Y H:i'),
                ];
            }),
            'total_revenue' => $link->total_revenue,
        ];

        return Inertia::render('Admin/Referrals/Show', $stats);
    }

    public function users()
    {
        $users = User::with('referralLink')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'referral' => $user->referralLink ? [
                        'code' => $user->referralLink->code,
                        'name' => $user->referralLink->name,
                    ] : null,
                    'created_at' => $user->created_at->format('d.m.Y H:i'),
                ];
            });

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
        ]);
    }
}
