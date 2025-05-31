<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->count(10)->sequence(fn(Sequence $sequence) => [
            'name' => 'admin' . ($sequence->index ? $sequence->index : ''),
            'email' => 'admin' . ($sequence->index ? $sequence->index : '') . '@gmail.com',
        ])->create();
    }
}
