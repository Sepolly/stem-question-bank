<?php

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;

class QuestionBuilder extends Builder
{
    public function mcq()
    {
        return $this ? $this->options()->exists() : null;
    }

    public function filterBySearch(?string $search)
    {

        if ($search) {
            $search = strtolower($search);
            $this->whereRaw('LOWER(question_text) LIKE ?', ["%{$search}%"]);
        }

        return $this;
    }

    public function filterBySubject(?string $subject)
    {
        if ($subject) {
            $subject = strtolower($subject);
            $this->whereHas('subject', function ($query) use ($subject) {
                $query->whereRaw('LOWER(name) = ?', ["{$subject}"]);
            });
        }

        return $this;
    }

    public function filterByStatus(?string $status)
    {
        if ($status) {
            $status = strtolower($status);
            $this->where('status', $status);
        }

        return $this;
    }

    public function filterByDifficulty(?string $difficulty)
    {
        if ($difficulty) {
            $difficulty = strtolower($difficulty);
            $this->where('difficulty', $difficulty);
        }

        return $this;
    }

    public function filterByType(?string $type)
    {
        if ($type) {
            $this->where('type', $type);
        }

        return $this;
    }

    // public function sort(?string $sort)
    // {

    //     $column = match ($sort) {
    //         'price_asc' => 'price',
    //         'price_desc' => 'price',
    //         'newest' => 'created_at',
    //         'relevance' => 'relevance',
    //         default => 'relevance'
    //     };

    //     $direction = Str::contains($sort, 'asc') ? 'asc' : 'desc';

    //     $this->orderBy($column, $direction);

    //     return $this;
    // }
}
