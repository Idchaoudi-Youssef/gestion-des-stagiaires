<?php

namespace App\Enums;

/**
 * Lifecycle of a task given by a supervisor to an intern.
 */
enum TaskState: string
{
    case Pending = 'pending';
    case Completed = 'completed';
    case Unfinished = 'unfinished';

    /**
     * Bootstrap badge colour used to display the state.
     */
    public function badge(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Completed => 'success',
            self::Unfinished => 'danger',
        };
    }

    /**
     * Human readable label, used in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Completed => 'Completed',
            self::Unfinished => 'Unfinished',
        };
    }
}
