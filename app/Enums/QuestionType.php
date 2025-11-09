<?php

namespace App\Enums;

enum QuestionType: string
{
    case MCQ = 'mcq';
    case TRUE_FALSE = 'true_false';
    case SHORT_ANSWER = 'short_answer';
    case LONG_ANSWER = 'long_answer';

    public static function values(): array
    {
        return array_column(self::cases(), 'values');
    }
}
