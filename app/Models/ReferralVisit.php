<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'referral_link_id',
        'user_id',
        'ip_address',
        'user_agent',
        'visited_at',
    ];

    protected $casts = [
        'visited_at' => 'datetime',
    ];

    public $timestamps = false;

    public function referralLink(): BelongsTo
    {
        return $this->belongsTo(ReferralLink::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
