<?php

namespace Database\Factories;

use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;
use Money\Money;

/**
 * @extends Factory<Account>
 */
class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'number' => $this->faker->unique()->numerify('######-#'),
            'balance' => Money::BRL($this->faker->numberBetween(0, 100000)),
            // 'currency' => $this->faker->randomElement(['USD', 'BRL', 'EUR']),
        ];
    }
}
