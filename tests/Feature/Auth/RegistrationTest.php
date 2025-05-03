<?php

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');
    $response->assertStatus(200);
});

test('new users can register', function () {
    Event::fake();

    $userData = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ];

    $response = $this->post('/register', $userData);

    // Verifica que el usuario fue creado
    $user = User::where('email', $userData['email'])->first();
    $this->assertNotNull($user, 'El usuario no fue creado en la base de datos.');

    // Verifica que tiene el rol 'user' (id=2 según tu seeder)
    $this->assertEquals(2, $user->role_id, 'El usuario no tiene asignado el rol user.');

    $this->assertAuthenticatedAs($user);
    $response->assertRedirect(route('dashboard', absolute: false));
    Event::assertDispatched(Registered::class);
});