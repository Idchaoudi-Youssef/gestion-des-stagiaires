<?php

namespace App\Enums;

/**
 * Review state of a report submitted by an intern.
 */
enum ReportState: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';

    /**
     * Bootstrap badge colour used to display the state.
     */
    public function badge(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Approved => 'success',
            self::Rejected => 'danger',
        };
    }
}
