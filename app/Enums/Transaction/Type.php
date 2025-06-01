<?php

namespace App\Enums\Transaction;

enum Type: string
{
    case Expense = 'EXPENSE';
    case Income = 'INCOME';

    public function display(): string
    {
        return match ($this) {
            self::Expense => 'Expense',
            self::Income => 'Income',
        };
    }
}
