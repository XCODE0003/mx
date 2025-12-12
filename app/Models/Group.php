<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = ['title', 'subject_id', 'question', 'image', 'is_forming'];
    protected $appends = ['formatted_title'];
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'subject_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'mark', 'id');
    }

    public function getFormattedTitleAttribute()
    {
        $title = $this->title;
        $title = str_replace('Задание', '', $title);
        $title = str_replace(' ', '', $title);
        $title = str_replace(':', '', $title);
        $title = str_replace(': ', '', $title);
        $title = str_replace('№', '', $title);
        return $title;
    }



}
