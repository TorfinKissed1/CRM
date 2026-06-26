<?php

namespace App\Enums;

enum TransactionType: string
{
    case Income = 'income';
    case Expense = 'expense';

    public function label(): string
    {
        return match ($this) {
            self::Income => 'Доход',
            self::Expense => 'Расход',
        };
    }

    public function sign(): int
    {
        return $this === self::Income ? 1 : -1;
    }
}
