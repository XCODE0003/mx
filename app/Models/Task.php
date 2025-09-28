<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $table = 'tasks'; // Явно указываем таблицу

    protected $fillable = [
        'article_id',
        'question',
        'image',
        'response',
        'subject_id',
        'blank_id',
        'mark',
        'table_answer',
        'count_columns',
        'question_id'
    ];

    protected $casts = [
        'image' => 'array'
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'mark', 'id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'subject_id');
    }


}