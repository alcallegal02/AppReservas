<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use App\Models\Role;

abstract class TestCase extends BaseTestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Ejecutar los seeders necesarios
        $this->seedRoles();
    }

    protected function seedRoles()
    {
        if (Role::count() === 0) {
            Artisan::call('db:seed', ['--class' => 'RoleSeeder']);
        }
    }
}