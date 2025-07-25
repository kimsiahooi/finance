<?php

namespace App\Http\Controllers;

use App\Enums\Transaction\Type;
use App\Models\Transaction;
use App\Services\ErrorLoggerService;
use Illuminate\Database\QueryException;
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
        $transactions = Transaction::where('user_id', $request->user()->id)
            ->when($request->search, fn($query) => $query->where('name', 'LIKE', "%$request->search%"))
            ->with(['categories', 'transactionType'])
            ->latest()
            ->paginate($request->query('entries', 10))
            ->withQueryString();

        return inertia('Transactions/Index', [
            'transactions' => $transactions,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {

        $categories = $request->user()->transactionCategories()->get();
        $types = $request->user()->transactionTypes()->get();

        return inertia('Transactions/Create', [
            'types' => $types,
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $transaction = $request->user()->transactions()
                ->create($request->validate([
                    'name' => ['required', 'string', 'max:255'],
                    'remark' => ['nullable', 'string'],
                    'categories' => ['required', 'array'],
                    'categories.*' => ['required', 'integer', Rule::exists('transaction_categories', 'id')
                        ->where(
                            fn($query) => $query->where('user_id', $request->user()->id)
                                ->whereNull('deleted_at')
                        )],
                    'transaction_type_id' => ['required', 'integer', Rule::exists('transaction_types', 'id')
                        ->where(
                            fn($query) => $query->where('user_id', $request->user()->id)
                                ->whereNull('deleted_at')
                        )],
                    'amount' => ['required', 'numeric', 'decimal:0,2', 'min:0'],
                    'transaction_at' => ['required', 'date', 'before:tomorrow'],
                ]));

            $transaction->categories()->sync($request->categories);

            return back()->with('success', 'Transaction created successfully.');
        } catch (QueryException $e) {
            ErrorLoggerService::log($e);

            return back()->with('error', 'Something went wrong. Please try again.');
        }
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
    public function edit(Transaction $transaction, Request $request)
    {
        Gate::authorize('update', $transaction);

        $categories = $request->user()->transactionCategories()->get();
        $types = $request->user()->transactionTypes()->get();

        return inertia('Transactions/Edit', [
            'transaction' => $transaction->load(['transactionType', 'categories']),
            'types' => $types,
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        Gate::authorize('update', $transaction);

        try {
            $transaction->update($request->validate([
                'name' => ['required', 'string', 'max:255'],
                'remark' => ['nullable', 'string'],
                'categories' => ['required', 'array'],
                'categories.*' => ['required', 'integer', Rule::exists('transaction_categories', 'id')
                    ->where(
                        fn($query) => $query->where('user_id', $request->user()->id)
                            ->whereNull('deleted_at')
                    )],
                'transaction_type_id' => ['required', 'integer', Rule::exists('transaction_types', 'id')
                    ->where(
                        fn($query) => $query->where('user_id', $request->user()->id)
                            ->whereNull('deleted_at')
                    )],
                'amount' => ['required', 'numeric', 'decimal:0,2', 'min:0'],
                'transaction_at' => ['required', 'date', 'before:tomorrow'],
            ]));

            $transaction->categories()->sync($request->categories);

            return back()->with('success', 'Transaction updated successfully.');
        } catch (QueryException $e) {
            ErrorLoggerService::log($e);

            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        Gate::authorize('delete', $transaction);

        try {
            $transaction->delete();

            return back()->with('success', 'Transaction deleted successfully.');
        } catch (QueryException $e) {
            ErrorLoggerService::log($e);

            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }
}
