<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionCategoryController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('transactions', TransactionController::class)->except(['create', 'show']);
    Route::resource('transaction-categories', TransactionCategoryController::class)
        ->parameters(['transaction-categories' => 'category'])
        ->except(['create', 'show']);
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
