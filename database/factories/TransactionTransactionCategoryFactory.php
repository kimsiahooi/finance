<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TransactionTransactionCategory>
 */
class TransactionTransactionCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::inRandomOrder()->first();
        $transaction = Transaction::whereBelongsTo($user)->inRandomOrder()->first();
        $category = TransactionCategory::whereBelongsTo($user)->inRandomOrder()->first();

        return [
            'user_id' => $user->id,
            'transaction_id' => $transaction->id,
            'category_id' => $category->id,
        ];
    }
}
