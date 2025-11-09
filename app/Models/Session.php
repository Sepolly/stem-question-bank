<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    protected $guarded = [];

    protected $table = 'question_sessions';

    public function questions()
    {
        return $this->hasMany(Question::class)->where('has_been_asked', false);
    }
}
