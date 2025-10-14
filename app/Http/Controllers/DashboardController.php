<?php

namespace App\Http\Controllers;

use App\Enums\Dashboard\Period;
use App\Services\Dashboard\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(protected DashboardService $dashboardService) {}

    public function index(Request $request)
    {
        $period = $request->query('period', Period::MONTH->value);
        $timezone = $request->user()->timezone;
        $currTime = now($timezone?->code);

        $startPeriod = $currTime->copy()->subDays(Period::toSubDay($period))->startOfDay();
        $offsetPeriod = $startPeriod->copy()->subHours($currTime->offsetHours);
        $durations = [$offsetPeriod, $currTime];
        $formattedTimezone = $currTime->copy()->format('P');

        $transactions = $this->dashboardService->getTransactions($period, $formattedTimezone, $durations);

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
