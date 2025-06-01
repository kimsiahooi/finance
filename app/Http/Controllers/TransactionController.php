<?php

namespace App\Http\Controllers;

use App\Enums\Transaction\Type;
use App\Models\Transaction;
use App\Services\ErrorLoggerService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
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
    public function create()
    {
        $types = collect(Type::cases())
            ->map(function (Type $type) {
                return [
                    'name' => $type->label(),
                    'value' => $type->value,
                ];
            });

        return inertia('Transactions/Create', [
            'types' => $types,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->user()->transactions()
                ->create($request->validate([
                    'name' => ['required', 'string', 'max:255'],
                    'description' => ['nullable', 'string'],
                    'type' => ['required', Rule::enum(Type::class)],
                    'amount' => ['required', 'numeric', 'decimal:0,2', 'min:0'],
                    'transaction_at' => ['required', 'date', 'before:tomorrow'],
                ]));

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
