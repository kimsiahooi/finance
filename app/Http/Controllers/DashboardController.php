<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $currTime = now('Asia/Kuala_Lumpur');
        $startPeriod = $currTime->copy()->subDays(30)->startOfDay();
        $offsetPeriod = $startPeriod->copy()->subHours($currTime->offsetHours);
        $durations = [$offsetPeriod, $currTime];
        $timezone = $currTime->copy()->format('P');

        $transactions = $request->user()
            ->transactions()
            ->selectRaw("DATE(CONVERT_TZ(transactioned_at, '+00:00', ?)) as transactioned_date", [$timezone])
            ->selectRaw("SUM(amount) as total_amount")
            ->selectRaw("SUM(CASE WHEN amount < 0 THEN amount ELSE 0 END) * -1 as total_expenses")
            ->selectRaw("SUM(CASE WHEN amount > 0 THEN amount ELSE 0 END) as total_incomes")
            ->whereBetween('transactioned_at', $durations)
            ->groupBy('transactioned_date')
            ->orderBy('transactioned_date')
            ->get();

        return inertia('Dashboard', [
            'transactions' => $transactions,
        ]);
    }
}
