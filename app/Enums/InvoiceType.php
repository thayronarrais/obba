<?php

namespace App\Enums;

enum InvoiceType: int
{
    case NONE = 0;
    case EXPENSE = 1;
    case SALE = 2;

    public function label(): string
    {
        return match($this) {
            self::NONE => 'Sem tipo',
            self::EXPENSE => 'Despesa',
            self::SALE => 'Venda',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($type) => [$type->value => $type->label()])
            ->toArray();
    }
}