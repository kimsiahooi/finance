<?php

namespace App\Http\Controllers;

use App\Enums\Dashboard\Period;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->query('period', Period::MONTH->value);

        $currTime = now('Asia/Kuala_Lumpur');
        $startPeriod = $currTime->copy()->subDays(Period::toSubDay($period))->startOfDay();
        $offsetPeriod = $startPeriod->copy()->subHours($currTime->offsetHours);
        $durations = [$offsetPeriod, $currTime];
        $timezone = $currTime->copy()->format('P');

        $transactions = $request->user()
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

        $periodOptions = collect(Period::cases())
            ->map(
                fn(Period $period) => [
                    'name' => $period->label(),
                    'value' => $period->value,
                    'is_default' => $period->isDefault(),
                ]
            );

        return inertia('Dashboard', [
            'transactions' => $transactions,
            'options' => [
                'select' => [
                    'periods' => $periodOptions,
                ],
            ],
        ]);
    }
}
