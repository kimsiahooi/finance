<?php

use App\Models\Transaction;
use App\Models\TransactionCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaction_transaction_category', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Transaction::class)->constrained();
            $table->foreignIdFor(TransactionCategory::class)->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_category_transactions');
    }
};
