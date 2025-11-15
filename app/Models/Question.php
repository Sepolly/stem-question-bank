<?php

namespace App\Models;

use App\Builders\QuestionBuilder;
use App\Enums\QuestionStatus;
use App\Enums\QuestionType;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class Question extends Model
{
    protected function casts()
    {
        return [
            'type' => QuestionType::class,
        ];
    }

    public static function boot()
    {
        parent::boot();
        static::creating(function ($question) {
            $question->user_id = auth()->id();
            $question->event_id = cache()->get('current_event_id');
            $question->status = request()->user()?->isSuperAdmin() ? QuestionStatus::APPROVED->value : QuestionStatus::PENDING->value;
        });
    }

    public static function getType(string $type)
    {
        return match(strtolower($type)){
            'multiple choice' => 'mcq',
            'short answer' => 'short_answer',
            'long answer' => 'long_answer',
            'true/false' => 'true_false',
            default => throw new InvalidArgumentException("Unknown question type: $type")
        };
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function answer()
    {
        return $this->hasOne(Answer::class);
    }

    public function options()
    {
        return $this->hasMany(QuestionOption::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault(function (User $user) {
            $user->name = 'Unknown User';
        });
    }

    public function lastModifier()
    {
        return $this->belongsTo(User::class, 'last_modified_by');
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    /**
     * Summary of newEloquentBuilder
     *
     * @method static QuestionBuilder query()
     *
     * @param  mixed  $query
     * @return QuestionBuilder
     */
    public function newEloquentBuilder($query)
    {
        return new QuestionBuilder($query);
    }
}
