<?php

namespace App\Enums;

enum UserRole: int
{
    case ADMIN = 1;
    case ACCOUNTANT = 2;
    case USER = 3;

    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'Administrador',
            self::ACCOUNTANT => 'Contabilista',
            self::USER => 'Utilizador',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($role) => [$role->value => $role->label()])
            ->toArray();
    }
}