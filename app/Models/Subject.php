<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    protected $fillable = [
        'subject_id',
        'name',
        'exam_type',
        'class_name',
        'text_header'
    ];

    protected $casts = [
        'exam_type' => 'string'
    ];

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'subject_id', 'subject_id');
    }

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class, 'subject_id', 'subject_id');
    }

    public function scopeOge($query)
    {
        return $query->where('exam_type', 'oge');
    }

    public function scopeEge($query)
    {
        return $query->where('exam_type', 'ege');
    }

    public function scopeByExamType($query, string $examType)
    {
        return $query->where('exam_type', $examType);
    }

    public function getRouteKeyName(): string
    {
        return 'subject_id';
    }

    /**
     * Accessor для text_header - заменяет %YEAR% на текущий год
     */
    public function getTextHeaderAttribute($value): ?string
    {
        if (!$value) {
            return $value;
        }

        return str_replace('%YEAR%', date('Y'), $value);
    }
}