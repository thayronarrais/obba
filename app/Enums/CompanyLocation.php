<?php

namespace App\Enums;

enum CompanyLocation: int
{
    case MAINLAND = 0;
    case AZORES = 1;
    case MADEIRA = 2;

    public function label(): string
    {
        return match($this) {
            self::MAINLAND => 'Portugal Continental',
            self::AZORES => 'Açores',
            self::MADEIRA => 'Madeira',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($location) => [$location->value => $location->label()])
            ->toArray();
    }
}