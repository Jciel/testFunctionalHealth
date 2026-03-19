<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Operation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Operation>
 */
class OperationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => $this->faker->randomElement(['deposit', 'withdraw', 'balance']),
            'account_id' => Account::inRandomOrder()->value('id') ?? Account::factory(),
        ];
    }
}
