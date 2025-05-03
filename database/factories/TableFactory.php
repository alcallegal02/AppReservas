<?php

namespace Database\Factories;

use App\Models\Table;
use App\Models\Zone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Table>
 */
class TableFactory extends Factory
{
    protected $model = Table::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word() . ' Table',
            'capacity' => $this->faker->numberBetween(2, 10),
            'zone_id' => Zone::factory(), // Crea automáticamente una zona relacionada
            'is_active' => $this->faker->boolean(90), // 90% de probabilidad de estar activa
        ];
    }
}
