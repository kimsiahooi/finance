<?php

namespace App\Http\Controllers;

use App\Models\TransactionCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class TransactionCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $entries = $request->query('entries', 10);

        $categories = $request->user()
            ->transactionCategories()
            ->when(
                $request->search,
                fn(Builder $query, string $search) =>
                $query->whereAny(['id', 'name', 'description'], 'like', "%{$search}%")
            )
            ->latest()
            ->paginate($entries)
            ->withQueryString();

        return inertia('TransactionCategories/Index', [
            'categories' => $categories,
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
            'description' => ['sometimes', 'nullable', 'string'],
        ]);

        $request->user()->transactionCategories()->create($validated);

        return back()->with('success', 'Category created successfully.');
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
    public function edit(TransactionCategory $transactionCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TransactionCategory $transactionCategory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransactionCategory $transactionCategory)
    {
        //
    }
}
