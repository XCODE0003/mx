<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReferralLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'clicks',
        'is_active',
    ];

    protected $casts = [
        'clicks' => 'integer',
        'is_active' => 'boolean',
    ];

    public function visits(): HasMany
    {
        return $this->hasMany(ReferralVisit::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function getUrlAttribute(): string
    {
        return url("/?ref={$this->code}");
    }

    public function incrementClicks(): void
    {
        $this->increment('clicks');
    }

    public function getRegistrationsCountAttribute(): int
    {
        return $this->users()->count();
    }

    public function getPurchasesCountAttribute(): int
    {
        return $this->orders()->count();
    }
}
