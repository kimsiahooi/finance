<?php

namespace App\Http\Controllers;

use App\Models\TransactionCategory;
use App\Services\ErrorLoggerService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redis;
use Illuminate\Validation\Rule;

class TransactionCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categories = TransactionCategory::where('user_id', $request->user()->id)
            ->when($request->search, fn($query) => $query->where('name', 'LIKE', "%$request->search%"))
            ->latest()
            ->paginate($request->query('entries', 10))
            ->withQueryString();

        return inertia('Transactions/Categories/Index', [
            'categories' => $categories,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('Transactions/Categories/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255', Rule::unique('transaction_categories')
                    ->where(
                        fn($query) => $query->where('user_id', $request->user()->id)
                            ->whereNull('deleted_at')
                    )],
                'description' => ['nullable', 'string'],
            ]);

            $trashed = $request->user()
                ->transactionCategories()
                ->onlyTrashed()
                ->where('name', $request->name)
                ->first();

            if ($trashed) {
                $trashed->restore();
            } else {
                $request->user()->transactionCategories()
                    ->create($validated);
            }

            return back()->with('success', 'Category created successfully.');
        } catch (QueryException $e) {
            ErrorLoggerService::log($e);

            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(TransactionCategory $transactionCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TransactionCategory $category)
    {
        Gate::authorize('update', $category);

        return inertia('Transactions/Categories/Edit', [
            'category' => $category,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TransactionCategory $category)
    {
        Gate::authorize('update', $category);

        try {
            $category->update($request->validate([
                'name' => ['required', 'string', 'max:255', Rule::unique('transaction_categories')
                    ->ignore($category->id)
                    ->where(
                        fn($query) => $query->where('user_id', $request->user()->id)
                    )],
                'description' => ['nullable', 'string'],
            ]));

            return back()->with('success', 'Category updated successfully.');
        } catch (QueryException $e) {
            ErrorLoggerService::log($e);

            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransactionCategory $category)
    {
        Gate::authorize('delete', $category);

        try {
            $category->delete();

            return back()->with('success', 'Category deleted successfully.');
        } catch (QueryException $e) {
            ErrorLoggerService::log($e);

            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }
}
