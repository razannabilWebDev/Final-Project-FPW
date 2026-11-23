<?php

namespace App;

enum UserRole: string
{
    case Admin = 'admin';
    case Pengelola = 'pengelola';
    case Staf = 'staf';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Pengelola => 'Pengelola',
            self::Staf => 'Staf Operasional',
        };
    }

    public function isAdmin(): bool
    {
        return $this === self::Admin;
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
