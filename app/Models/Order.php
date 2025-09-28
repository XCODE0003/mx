<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Order extends Model
{
    protected $fillable = [
        'subject_id',
        'task_ids',
        'user_id',
        'file_url',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'id');
    }
    protected $casts = [
        'task_ids' => 'array',
        'created_at' => 'datetime:d.m.Y H:i',
    ];
}
