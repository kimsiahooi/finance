<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Ooi Kim Siah',
            'email' => 'admin@gmail.com',
        ]);

        $this->call([
            TransactionCategorySeeder::class,
            TransactionSeeder::class,
            TransactionTransactionCategorySeeder::class,
        ]);
    }
}
