<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            
            // Relaciones
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('table_id')->constrained('tables')->cascadeOnDelete();
            $table->foreignId('time_slot_id')->constrained('time_slots')->cascadeOnDelete();
            
            // Datos de la reserva
            $table->date('reservation_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('guest_count');
            
            // Estado y control
            $table->enum('status', [
                'pending', 
                'confirmed', 
                'cancelled', 
                'completed',
                'no_show'
            ])->default('pending');
            
            $table->text('special_requests')->nullable();
            
            // Auditoría
            $table->timestamps();
            $table->softDeletes();
        
            // Índices
            $table->index(['reservation_date', 'time_slot_id']);
            $table->index(['table_id', 'reservation_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
