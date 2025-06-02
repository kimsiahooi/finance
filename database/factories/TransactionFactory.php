<?php

namespace Database\Factories;

use App\Enums\Transaction\Type;
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
            'name' => fake()->sentence(3),
            'remark' => fake()->sentence(12),
            'type' => fake()->randomElement(Type::cases()),
            'amount' => fake()->numberBetween(0, 99),
            'transaction_at' => fake()->dateTimeBetween('-1 year'),
            'user_id' => User::all()->random(),
        ];
    }
}
