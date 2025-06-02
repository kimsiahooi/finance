<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::all()->each(function (User $user) {
            TransactionCategory::factory(10)
                ->hasAttached(Transaction::factory(20)
                    ->state(fn() => ['user_id' => $user->id]))->create([
                    'user_id' => $user->id,
                ]);
        });
    }
}
