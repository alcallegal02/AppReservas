<?php

use App\Models\Reservation;
use App\Models\Table;
use App\Models\TimeSlot;
use App\Models\Zone;
use App\Models\RestaurantClosure;
use App\Models\User;
use Carbon\Carbon;
use function Pest\Laravel\{get, post, put, delete, actingAs};

// Configuración inicial para pruebas
beforeEach(function () {
    $this->user = User::factory()->create();
    actingAs($this->user);

    $this->zone = Zone::factory()->create(['is_active' => true]);
    $this->table = Table::factory()->create([
        'zone_id' => $this->zone->id,
        'capacity' => 4,
        'is_active' => true
    ]);
    $this->timeSlot = TimeSlot::factory()->create(['is_active' => true]);
    
    // Fecha base para pruebas (mañana a las 12:00)
    $this->tomorrow = Carbon::tomorrow()->setTime(12, 0);
});

// 1. Pruebas de acceso a vistas
describe('Vistas del CRUD', function () {
    it('puede acceder al listado de reservas', function () {
        // Creamos algunas reservas para probar que se listan
        Reservation::factory()->count(3)->create(['user_id' => $this->user->id]);

        get(route('reservations.index'))
            ->assertOk()
            ->assertViewHas('reservations')
            ->assertSeeText('Mis Reservas');
    });

    it('puede acceder al formulario de creación de reservas', function () {
        get(route('reservations.create'))
            ->assertOk()
            ->assertViewHasAll(['zones', 'timeSlots', 'closures'])
            ->assertSeeText('Nueva Reserva');
    });

    it('puede acceder al formulario de edición de una reserva', function () {
        $reservation = Reservation::factory()->create([
            'user_id' => $this->user->id,
            'reservation_date' => $this->tomorrow->format('Y-m-d')
        ]);
        
        get(route('reservations.edit', $reservation))
            ->assertOk()
            ->assertViewHas('reservation')
            ->assertSee($reservation->guest_count);
    });

    it('no permite editar una reserva pasada', function () {
        $pastReservation = Reservation::factory()->create([
            'user_id' => $this->user->id,
            'reservation_date' => now()->subDay()->format('Y-m-d')
        ]);
        
        get(route('reservations.edit', $pastReservation))
            ->assertRedirect()
            ->assertSessionHas('error');
    });
});

// 2. Pruebas de creación de reservas
describe('Creación de reservas', function () {
    it('puede crear una reserva válida y se guarda en la base de datos', function () {
        $reservationData = [
            'reservation_date' => $this->tomorrow->format('Y-m-d'),
            'time_slot_id' => $this->timeSlot->id,
            'zone_id' => $this->zone->id,
            'guest_count' => 2,
            'special_requests' => 'Mesa cerca de la ventana'
        ];

        post(route('reservations.store'), $reservationData)
            ->assertRedirect(route('reservations.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('reservations', [
            'user_id' => $this->user->id,
            'guest_count' => 2,
            'status' => 'pending' // Cambiado a pending si es el estado inicial
        ]);
    });

    it('no permite reservas en el pasado', function () {
        $reservationData = [
            'reservation_date' => now()->subDay()->format('Y-m-d'),
            'time_slot_id' => $this->timeSlot->id,
            'zone_id' => $this->zone->id,
            'guest_count' => 2
        ];

        post(route('reservations.store'), $reservationData)
            ->assertSessionHasErrors('reservation_date');
    });

    it('no permite más comensales que la capacidad de la mesa', function () {
        $reservationData = [
            'reservation_date' => $this->tomorrow->format('Y-m-d'),
            'time_slot_id' => $this->timeSlot->id,
            'zone_id' => $this->zone->id,
            'guest_count' => 10 // Mesa tiene capacidad 4
        ];

        post(route('reservations.store'), $reservationData)
            ->assertSessionHasErrors('guest_count');
    });

    it('no permite crear reserva cuando no hay mesas disponibles', function () {
        // Ocupamos todas las mesas
        Reservation::factory()->create([
            'table_id' => $this->table->id,
            'reservation_date' => $this->tomorrow->format('Y-m-d'),
            'time_slot_id' => $this->timeSlot->id,
            'status' => 'confirmed'
        ]);

        $reservationData = [
            'reservation_date' => $this->tomorrow->format('Y-m-d'),
            'time_slot_id' => $this->timeSlot->id,
            'zone_id' => $this->zone->id,
            'guest_count' => 2
        ];

        post(route('reservations.store'), $reservationData)
            ->assertRedirect()
            ->assertSessionHas('error');
    });

    it('no permite crear reserva en días de cierre del restaurante', function () {
        $closure = RestaurantClosure::factory()->create([
            'closure_date' => $this->tomorrow->format('Y-m-d')
        ]);

        $reservationData = [
            'reservation_date' => $closure->closure_date,
            'time_slot_id' => $this->timeSlot->id,
            'zone_id' => $this->zone->id,
            'guest_count' => 2
        ];

        post(route('reservations.store'), $reservationData)
            ->assertRedirect()
            ->assertSessionHas('error');
    });
});

// 3. Pruebas de actualización de reservas
describe('Actualización de reservas', function () {
    it('puede actualizar una reserva existente', function () {
        $reservation = Reservation::factory()->create([
            'user_id' => $this->user->id,
            'table_id' => $this->table->id,
            'time_slot_id' => $this->timeSlot->id,
            'reservation_date' => $this->tomorrow->format('Y-m-d')
        ]);

        $newData = [
            'reservation_date' => $this->tomorrow->addDay()->format('Y-m-d'),
            'time_slot_id' => $this->timeSlot->id,
            'zone_id' => $this->zone->id,
            'guest_count' => 3,
            'special_requests' => 'Nuevo requerimiento'
        ];

        put(route('reservations.update', $reservation), $newData)
            ->assertRedirect(route('reservations.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'guest_count' => 3,
            'special_requests' => 'Nuevo requerimiento'
        ]);
    });

    it('no permite actualizar una reserva pasada', function () {
        $pastReservation = Reservation::factory()->create([
            'user_id' => $this->user->id,
            'reservation_date' => now()->subDay()->format('Y-m-d')
        ]);

        put(route('reservations.update', $pastReservation), [
            'guest_count' => 3
        ])->assertRedirect()
          ->assertSessionHas('error');
    });
});

// 4. Pruebas de eliminación de reservas
describe('Eliminación de reservas', function () {
    it('puede cancelar una reserva (soft delete)', function () {
        $reservation = Reservation::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'confirmed',
            'reservation_date' => $this->tomorrow->format('Y-m-d')
        ]);

        delete(route('reservations.destroy', $reservation))
            ->assertRedirect(route('reservations.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'cancelled'
        ]);
    });

    it('no permite cancelar una reserva pasada', function () {
        $pastReservation = Reservation::factory()->create([
            'user_id' => $this->user->id,
            'reservation_date' => now()->subDay()->format('Y-m-d')
        ]);

        delete(route('reservations.destroy', $pastReservation))
            ->assertRedirect()
            ->assertSessionHas('error');
    });
});

// 5. Pruebas de autorización
describe('Autorización', function () {
    it('no permite acceder a reservas de otros usuarios', function () {
        $otherUser = User::factory()->create();
        $reservation = Reservation::factory()->create([
            'user_id' => $otherUser->id,
            'reservation_date' => $this->tomorrow->format('Y-m-d')
        ]);

        // Verificar todas las rutas protegidas
        get(route('reservations.show', $reservation))->assertForbidden();
        get(route('reservations.edit', $reservation))->assertForbidden();
        put(route('reservations.update', $reservation), [])->assertForbidden();
        delete(route('reservations.destroy', $reservation))->assertForbidden();
    });

    it('permite al administrador acceder a todas las reservas', function () {
        $admin = User::factory()->create(['role_id' => 1]); // Asume que role_id 1 es admin
        actingAs($admin);

        $otherUserReservation = Reservation::factory()->create([
            'reservation_date' => $this->tomorrow->format('Y-m-d')
        ]);

        get(route('reservations.edit', $otherUserReservation))->assertOk();
    });
});