<?php

namespace Database\Factories;

use App\Models\TimeSlot;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TimeSlot>
 */
class TimeSlotFactory extends Factory
{
    protected $model = TimeSlot::class;

    public function definition(): array
    {
        // Definimos un rango de horas aleatorias válidas
        $start = $this->faker->unique()->time('H:i', '20:00'); // hora de inicio aleatoria antes de las 20:00
        $startCarbon = Carbon::createFromFormat('H:i', $start);
        $endCarbon = (clone $startCarbon)->addHours(rand(1, 3)); // sumamos entre 1 y 3 horas

        return [
            'name' => 'Turno ' . $start,
            'start_time' => $startCarbon->format('H:i'),
            'end_time' => $endCarbon->format('H:i'),
            'is_active' => $this->faker->boolean(90),
        ];
    }
}
