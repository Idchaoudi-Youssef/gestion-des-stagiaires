<?php

namespace App\Enums;

/**
 * The role a user plays in the platform.
 *
 * `pending` is used for accounts that self-registered but have not been
 * validated by an administrator yet. Those accounts can sign in but they are
 * not allowed to reach any of the three role dashboards.
 */
enum UserType: string
{
    case Admin = 'admin';
    case Supervisor = 'supervisor';
    case Intern = 'intern';
    case Pending = 'pending';

    /**
     * Human readable label, used in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::Supervisor => 'Supervisor',
            self::Intern => 'Intern',
            self::Pending => 'Pending validation',
        };
    }

    /**
     * Roles that an administrator is allowed to assign from the interface.
     *
     * `pending` is intentionally excluded: a pending account only exists
     * because the person registered on their own.
     *
     * @return list<string>
     */
    public static function assignable(): array
    {
        return [self::Admin->value, self::Supervisor->value, self::Intern->value];
    }
}
