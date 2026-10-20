<?php

namespace App\Enums;

/**
 * The two kinds of report an intern can submit.
 */
enum ReportType: string
{
    case Weekly = 'weekly';
    case Final = 'final';

    /**
     * Human readable label, used in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Weekly => 'Weekly',
            self::Final => 'Final',
        };
    }
}
