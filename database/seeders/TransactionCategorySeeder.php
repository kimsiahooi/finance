<?php

namespace Database\Seeders;

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
            TransactionCategory::factory(50)->create([
                'user_id' => $user->id,
            ]);
        });
    }
}
