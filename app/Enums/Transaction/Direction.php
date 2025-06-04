<?php

namespace App\Enums\Transaction;

enum Direction: string
{
    case PLUS = 'PLUS';
    case MINUS = 'MINUS';

    public function display(): string
    {
        return match ($this) {
            self::PLUS => 'Plus',
            self::MINUS => 'Minus',
        };
    }
}
