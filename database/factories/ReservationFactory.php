<?php

namespace Database\Factories;

use App\Models\Reservation;
use App\Models\Table;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition(): array
    {
        $reservationDate = $this->faker->dateTimeBetween('now', '+2 months');
        $startTime = Carbon::createFromTime(rand(11, 20), [0, 30][rand(0, 1)]); // entre 11:00 y 20:30
        $endTime = (clone $startTime)->addMinutes(90); // duración estándar

        return [
            'user_id' => User::factory(),
            'table_id' => Table::factory(),
            'time_slot_id' => TimeSlot::factory(),
            'reservation_date' => $reservationDate->format('Y-m-d'),
            'start_time' => $startTime->format('H:i'),
            'end_time' => $endTime->format('H:i'),
            'guest_count' => $this->faker->numberBetween(1, 10),
            'status' => $this->faker->randomElement([
                Reservation::STATUS_PENDING,
                Reservation::STATUS_CONFIRMED,
                Reservation::STATUS_CANCELLED,
                Reservation::STATUS_COMPLETED,
                Reservation::STATUS_NO_SHOW,
            ]),
            'special_requests' => $this->faker->optional()->sentence(),
        ];
    }
}
