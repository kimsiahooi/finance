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
        $user = User::inRandomOrder()->first();

        return [
            'user_id' => $user->id,
            'name' => fake()->sentence(2),
            'amount' => fake()->numberBetween(-99, 99),
            'remark' => fake()->optional()->sentence(),
            'transactioned_at' => fake()->dateTimeBetween('-2 year'),
        ];
    }
}
