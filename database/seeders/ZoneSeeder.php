<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('zones')->insert([
            [
                'name' => 'Terraza', 
                'description' => 'Zona exterior', 
                'is_active' => true
            ],
            [
                'name' => 'Sala principal', 
                'description' => 'Zona interior', 
                'is_active' => true
            ],
        ]);
    }
}
