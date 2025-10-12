<?php

namespace App\Enums\Dashboard;

enum Period: string
{
    case TODAY = '0';
    case YESTERDAY = '1';
    case WEEK = '7';
    case MONTH = '30';
    case THEREE_MONTHS = '90';
    case SIX_MONTHS = '180';
    case YEAR = '365';

    public function label(): string
    {
        return match ($this) {
            self::TODAY => 'Today',
            self::YESTERDAY => 'Yesterday',
            self::WEEK => '1 Week Ago',
            self::MONTH => '1 Month Ago',
            self::THEREE_MONTHS => '3 Months Ago',
            self::SIX_MONTHS => '6 Months Ago',
            self::YEAR => '1 Year Ago',
        };
    }

    public function isDefault(): bool
    {
        return match ($this) {
            self::WEEK => true,
            default => false,
        };
    }

    public static function toSubDay(string $value): int
    {
        return match ($value) {
            Period::TODAY->value => 0,
            Period::YESTERDAY->value => 1,
            Period::WEEK->value => 7,
            Period::MONTH->value => 30,
            Period::THEREE_MONTHS->value => 90,
            Period::SIX_MONTHS->value => 180,
            Period::YEAR->value => 365,
            default => 7,
        };
    }

    public static function getDateFormat(string $value): string
    {
        return match ($value) {
            Period::TODAY->value, Period::YESTERDAY->value => '%Y-%m-%d %H:00%p',
            Period::WEEK->value, Period::MONTH->value => '%Y-%m-%d',
            Period::THEREE_MONTHS->value, Period::SIX_MONTHS->value, Period::YEAR->value => '%Y-%m',
            default => '%Y-%m-%d',
        };
    }
}
