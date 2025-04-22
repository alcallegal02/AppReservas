<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Zone;

// Ruta pública para obtener mesas por zona
Route::get('/zones/{zone}/tables', function (Zone $zone) {
    try {
        $tables = $zone->tables()
            ->where('is_active', true)
            ->select('id', 'name', 'capacity')
            ->get();
            
        return response()->json([
            'success' => true,
            'data' => $tables
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al cargar mesas'
        ], 500);
    }
});

// Ruta protegida (opcional para otros endpoints)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});