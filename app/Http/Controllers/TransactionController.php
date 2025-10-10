<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Models\TransactionTransactionCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

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
            'categories' => ['nullable', 'array'],
            'categories.*' => [
                'required',
                'distinct',
                Rule::exists('transaction_categories', 'id')
                    ->withoutTrashed()
                    ->where(
                        fn(QueryBuilder $query) =>
                        $query->where('user_id', $request->user()->id)
                    )
            ],
            'amount' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'expense' => ['required', 'boolean'],
            'remark' => ['nullable', 'string'],
        ]);

        if ($validated['expense']) {
            $validated['amount'] = $validated['amount'] * -1;
        }

        $validated['transactioned_at'] = now();

        $transaction = $request->user()
            ->transactions()
            ->create($validated);

        if ($validated['categories']) {
            $categories = collect($validated['categories'])
                ->mapWithKeys(fn(int $category) => [
                    $category => [
                        'user_id' => $request->user()->id,
                    ]
                ]);

            $transaction->categories()
                ->sync($categories);
        }

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
    public function edit(Request $request, Transaction $transaction)
    {
        Gate::authorize('update', $transaction);

        $categories = $request->user()
            ->transactionCategories()
            ->select('id', 'name')
            ->get()
            ->map(fn(TransactionCategory $category) =>
            [
                'name' => $category->name,
                'value' => $category->id,
            ]);

        return inertia('Transactions/Edit', [
            'transaction' => $transaction->load(['categories']),
            'options' => [
                'select' => [
                    'categories' => $categories,
                ],
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        Gate::authorize('update', $transaction);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'categories' => ['nullable', 'array'],
            'categories.*' => [
                'required',
                'distinct',
                Rule::exists('transaction_categories', 'id')
                    ->withoutTrashed()
                    ->where(
                        fn(QueryBuilder $query) =>
                        $query->where('user_id', $request->user()->id)
                    )
            ],
            'amount' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'expense' => ['required', 'boolean'],
            'remark' => ['nullable', 'string'],
            'transactioned_at' => ['required', 'date'],
        ]);

        if ($validated['expense']) {
            $validated['amount'] = $validated['amount'] * -1;
        }

        $transaction->update($validated);

        $categories = collect($validated['categories'])
            ->mapWithKeys(fn(int $category) => [
                $category => [
                    'user_id' => $request->user()->id,
                ]
            ])->toArray();

        TransactionTransactionCategory::onlyTrashed()
            ->whereBelongsTo($request->user())
            ->whereIn('category_id', array_keys($categories))
            ->restore();

        $transaction->categories()->sync($categories);


        return to_route('transactions.index')
            ->with('success', 'Transaction updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        Gate::authorize('delete', $transaction);

        $transaction->delete($transaction);

        return back()->with('success', 'Transaction deleted successfully.');
    }
}
