<?php

namespace App\Enums;

enum QuestionSessionStatus: string
{
    case PENDING = 'pending';
    case ONGOING = 'ongoing';
    case ENDED = 'ended';

    public static function values(): array
    {
        return array_column(self::cases(), 'values');
    }
}
