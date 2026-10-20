<?php

namespace App\Enums;

/**
 * Where an intern currently stands in their internship.
 */
enum InternshipStatus: string
{
    case NotStarted = 'not_started';
    case InPeriod = 'in_period';
    case Finished = 'finished';

    /**
     * Human readable label, used in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::NotStarted => 'Not started',
            self::InPeriod => 'In progress',
            self::Finished => 'Finished',
        };
    }
}
