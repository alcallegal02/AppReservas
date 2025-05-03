<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        // Obtener el rol 'user' o crearlo si no existe
        $role = Role::where('slug', 'user')->first();
        
        if (!$role) {
            $role = Role::create([
                'name' => 'User',
                'slug' => 'user',
                'description' => 'Regular user role',
                'is_active' => true
            ]);
        }

        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role_id' => $role->id, // Asignar el role_id correctamente
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    // Método adicional para crear usuarios admin
    public function admin(): static
    {
        $role = Role::where('slug', 'admin')->first();
        
        if (!$role) {
            $role = Role::create([
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Administrator role',
                'is_active' => true
            ]);
        }

        return $this->state(fn (array $attributes) => [
            'role_id' => $role->id,
        ]);
    }
}