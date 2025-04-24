<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tables')->insert([
            [
                'name' => 'Mesa 1', 
                'capacity' => 4, 
                'zone_id' => 1, 
                'is_active' => true
            ],
            [
                'name' => 'Mesa 3', 
                'capacity' => 2, 
                'zone_id' => 1, 
                'is_active' => true
            ],
            [
                'name' => 'Mesa 4', 
                'capacity' => 4, 
                'zone_id' => 1, 
                'is_active' => true
            ],
            [
                'name' => 'Mesa 5', 
                'capacity' => 4, 
                'zone_id' => 1, 
                'is_active' => true
            ],
            [
                'name' => 'Mesa 6', 
                'capacity' => 4, 
                'zone_id' => 1, 
                'is_active' => true
            ],
            [
                'name' => 'Mesa 7', 
                'capacity' => 4, 
                'zone_id' => 1, 
                'is_active' => true
            ],
            [
                'name' => 'Mesa 8', 
                'capacity' => 4, 
                'zone_id' => 1, 
                'is_active' => true
            ],
            [
                'name' => 'Mesa 9', 
                'capacity' => 4, 
                'zone_id' => 1, 
                'is_active' => true
            ],
            [
                'name' => 'Mesa 10', 
                'capacity' => 4, 
                'zone_id' => 1, 
                'is_active' => true
            ],
            [
                'name' => 'Mesa 11', 
                'capacity' => 4, 
                'zone_id' => 1, 
                'is_active' => true
            ],
            [
                'name' => 'Mesa 12', 
                'capacity' => 4, 
                'zone_id' => 1, 
                'is_active' => true
            ],
            [
                'name' => 'Mesa 13', 
                'capacity' => 4, 
                'zone_id' => 1, 
                'is_active' => true
            ],
            [
                'name' => 'Mesa 14', 
                'capacity' => 4, 
                'zone_id' => 1, 
                'is_active' => true
            ],
            [
                'name' => 'Mesa 15', 
                'capacity' => 4, 
                'zone_id' => 1, 
                'is_active' => true
            ],
            [
                'name' => 'Mesa 2', 
                'capacity' => 6, 
                'zone_id' => 1, 
                'is_active' => true
            ],
            [
                'name' => 'Mesa VIP', 
                'capacity' => 8, 
                'zone_id' => 2, 
                'is_active' => true
            ],
        ]);
    }
}
