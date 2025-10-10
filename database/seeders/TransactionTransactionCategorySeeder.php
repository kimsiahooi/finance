<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionTransactionCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            $transactions = Transaction::whereBelongsTo($user)->get();

            foreach ($transactions as $transaction) {
                $categories = TransactionCategory::whereBelongsTo($user)
                    ->inRandomOrder()
                    ->limit(rand(1, 2))
                    ->get()
                    ->mapWithKeys(fn(TransactionCategory $category) => [
                        $category['id'] => [
                            'user_id' => $user->id,
                        ]
                    ]);

                $transaction->categories()->sync($categories);
            }
        }
    }
}
