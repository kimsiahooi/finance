<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $entries = $request->query('entries', 10);

        $user = $request->user();

        $categories = $user->transactionCategories()
            ->select('id', 'name')
            ->get()
            ->map(fn(TransactionCategory $category) =>
            [
                'name' => $category->name,
                'value' => $category->id,
            ]);

        $transactions = $user->transactions()
            ->with(['categories'])
            ->when(
                $request->search,
                fn(Builder $query, string $search) =>
                $query->whereAny(['id', 'name', 'remark'], 'like', "%{$search}%")
            )
            ->when(
                $request->start_date,
                fn(Builder $query, string $startDate) =>
                $query->where('transactioned_at', '>=', $startDate)
            )
            ->when(
                $request->end_date,
                fn(Builder $query, string $endDate) =>
                $query->where('transactioned_at', '<=', $endDate)
            )
            ->latest()
            ->paginate($entries)
            ->withQueryString();

        return inertia('Transactions/Index', [
            'transactions' => $transactions,
            'report' => [
                'total_amount' => $transactions->sum('amount'),
            ],
            'options' => [
                'select' => [
                    'categories' => $categories,
                ],
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'expense' => ['sometimes', 'in:on'],
            'remark' => ['nullable', 'string'],
        ]);

        if (isset($validated['expense'])) {
            $validated['amount'] = $validated['amount'] * -1;
        }

        $validated['transactioned_at'] = now();

        $request->user()->transactions()->create($validated);

        return back()->with('success', 'Transaction created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        //
    }
}
