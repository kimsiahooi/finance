<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $currTime = now();
        $durations = [$currTime->copy()->subDays(30), $currTime];

        $categories = $request->user()
            ->transactionCategories()
            ->withWhereHas(
                'transactions',
                fn($query) =>
                $query->whereBetween('transactioned_at', $durations)
                    ->orderBy('transactioned_at')
            )
            ->get();

        return inertia('Dashboard', [
            'categories' => $categories,
        ]);
    }
}
