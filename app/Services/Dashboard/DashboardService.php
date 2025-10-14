<?php

namespace App\Services\Dashboard;

use App\Enums\Dashboard\Period;
use Illuminate\Support\Facades\Auth;

class DashboardService
{
    public function getTransactions(string $period, string $timezone, array $durations)
    {
        return Auth::user()
            ->transactions()
            ->selectRaw(
                "DATE_FORMAT(CONVERT_TZ(transactioned_at, '+00:00', ?), ?) as transactioned_date",
                [$timezone, Period::getDateFormat($period)]
            )
            ->selectRaw("SUM(amount) as total_amount")
            ->selectRaw("SUM(CASE WHEN amount < 0 THEN amount ELSE 0 END) * -1 as total_expenses")
            ->selectRaw("SUM(CASE WHEN amount > 0 THEN amount ELSE 0 END) as total_incomes")
            ->whereBetween('transactioned_at', $durations)
            ->groupBy('transactioned_date')
            ->orderBy('transactioned_date')
            ->get();
    }
}
