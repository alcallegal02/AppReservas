<?php

namespace Database\Factories;

use App\Models\RestaurantClosure;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RestaurantClosure>
 */
class RestaurantClosureFactory extends Factory
{
    protected $model = RestaurantClosure::class;

    public function definition(): array
    {
        return [
            'closure_date' => $this->faker->dateTimeBetween('now', '+6 months')->format('Y-m-d'),
            'reason' => $this->faker->optional()->sentence(),
            'is_recurring' => $this->faker->boolean(20), // 20% de probabilidad de que se repita
        ];
    }
}
