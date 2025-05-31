<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(1),
            'description' => fake()->sentence(12),
            'amount' => fake()->numberBetween(-99, 99),
            'transaction_at' => fake()->dateTimeBetween('-1 year'),
            'user_id' => User::all()->random(),
        ];
    }
}
