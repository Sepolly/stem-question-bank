<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->user_id = request()->user()->id;
        });
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function topics()
    {
        return $this->hasManyThrough(Topic::class, Subject::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'user_events', 'event_id', 'user_id');
    }

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }
}
