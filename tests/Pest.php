<?php

use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
*/

uses(TestCase::class, RefreshDatabase::class)->in('Feature');

// Configuración global para tests
beforeEach(function() {
    // Crear roles necesarios si no existen
    if (!Role::where('slug', 'admin')->exists()) {
        Role::factory()->create([
            'id' => 1,
            'name' => 'Admin',
            'slug' => 'admin',
            'is_active' => true
        ]);
    }

    if (!Role::where('slug', 'user')->exists()) {
        Role::factory()->create([
            'id' => 2,
            'name' => 'User',
            'slug' => 'user',
            'is_active' => true
        ]);
    }
});

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
*/

function createAdminUser()
{
    return \App\Models\User::factory()->create([
        'role_id' => 1 // admin
    ]);
}

function createRegularUser()
{
    return \App\Models\User::factory()->create([
        'role_id' => 2 // user
    ]);
}