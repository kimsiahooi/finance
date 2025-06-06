<?php

use App\Http\Controllers\TransactionCategoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransactionTypeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');



Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::resource('transactions', TransactionController::class)->except(['show']);
    Route::resource('transactions/categories', TransactionCategoryController::class)
        ->names('transactions.categories')
        ->except(['show']);
    Route::resource('transactions/types', TransactionTypeController::class)
        ->names('transactions.types')
        ->except(['show']);
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
