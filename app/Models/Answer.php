<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $touches = ['question'];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
