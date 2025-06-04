<?php

use App\Enums\Transaction\Direction;
use App\Enums\Transaction\Type;
use App\Models\Transaction;
use App\Models\TransactionType;
use App\Models\User;
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
        Schema::create('transaction_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('direction', array_column(Direction::cases(), 'value'))->default(Direction::MINUS->value);
            $table->foreignIdFor(User::class)->constrained();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['name', 'user_id']);
        });

        Transaction::all()->each(function (Transaction $transaction) {
            TransactionType::firstOrCreate([
                'name' => Type::tryFrom($transaction->type)?->name,
                'user_id' => $transaction->user_id,
            ], [
                'description' => null,
                'direction' => $transaction->type === Type::Expense->value ? Direction::MINUS->value : Direction::PLUS->value,
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_types');
    }
};
