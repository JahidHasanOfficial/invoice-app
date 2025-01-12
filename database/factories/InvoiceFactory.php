<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'number' => $this->faker->unique()->numberBetween(100, 1000),
            'customer_id' => $this->faker->unique()->numberBetween(1, 10),
            'date' => $this->faker->dateTimeThisYear(),
            'due_date' => $this->faker->dateTimeBetween('now', '+30 days'),
            'reference' => $this->faker->unique()->numerify('REF###'),
            'terms_and_conditions' => $this->faker->text,
            'discount' => $this->faker->randomFloat(2, 0, 50),
            'sub_total' => $this->faker->randomFloat(2, 100, 1000),
            'total' => $this->faker->randomFloat(2, 100, 1000),
        ];
    }
}
