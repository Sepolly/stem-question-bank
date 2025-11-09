<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionOption extends Model
{
    protected $touches = ['question'];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
