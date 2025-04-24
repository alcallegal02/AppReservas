<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Nueva Reserva') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('reservations.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 gap-6 mt-4 sm:grid-cols-2">
                            <!-- Fecha de Reserva -->
                            <div>
                                <x-label for="reservation_date" :value="__('Fecha de Reserva')" />
                                <x-input id="reservation_date" class="block mt-1 w-full" type="date" name="reservation_date" :value="old('reservation_date')" required />
                                @error('reservation_date')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Franja Horaria -->
                            <div>
                                <x-label for="time_slot_id" :value="__('Franja Horaria')" />
                                <select id="time_slot_id" name="time_slot_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                    <option value="">Seleccione una franja horaria</option>
                                    @foreach($timeSlots as $timeSlot)
                                        <option value="{{ $timeSlot->id }}" {{ old('time_slot_id') == $timeSlot->id ? 'selected' : '' }}>
                                            {{ $timeSlot->name }} ({{ $timeSlot->start_time->format('H:i') }} - {{ $timeSlot->end_time->format('H:i') }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('time_slot_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Zona y Mesa -->
                            <div>
                                <x-label for="zone_id" :value="__('Zona')" />
                                <select id="zone_id" name="zone_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                    <option value="">Seleccione una zona</option>
                                    @foreach($zones as $zone)
                                        <option value="{{ $zone->id }}" {{ old('zone_id') == $zone->id ? 'selected' : '' }}>
                                            {{ $zone->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('zone_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Número de Invitados -->
                            <div>
                                <x-label for="guest_count" :value="__('Número de Invitados')" />
                                <x-input id="guest_count" class="block mt-1 w-full" type="number" name="guest_count" :value="old('guest_count')" min="1" required />
                                @error('guest_count')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Solicitudes Especiales -->
                            <div>
                                <x-label for="special_requests" :value="__('Solicitudes Especiales')" />
                                <textarea id="special_requests" name="special_requests" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('special_requests') }}</textarea>
                                @error('special_requests')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Días de cierre -->
                        @if($closures->isNotEmpty())
                            <div class="mt-6">
                                <h4 class="font-medium text-gray-700">El restaurante estará cerrado en las siguientes fechas:</h4>
                                <ul class="list-disc pl-5 mt-2 text-sm text-gray-600">
                                    @foreach($closures as $closure)
                                        <li>{{ $closure->closure_date->format('d/m/Y') }} - {{ $closure->reason }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="flex justify-end mt-6">
                            <x-button>
                                {{ __('Reservar') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const zoneSelect = document.getElementById('zone_id');
    const tableSelect = document.getElementById('table_id');
    
    zoneSelect.addEventListener('change', async function() {
        const zoneId = this.value;
        tableSelect.innerHTML = '<option value="">Cargando mesas...</option>';
        
        if (!zoneId) {
            tableSelect.innerHTML = '<option value="">Primero seleccione una zona</option>';
            return;
        }

        try {
            const response = await fetch(`/api/zones/${zoneId}/tables`);
            
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }
            
            const result = await response.json();
            
            if (!result.success) {
                throw new Error(result.message || 'Error al obtener mesas');
            }
            
            console.log('Mesas recibidas:', result.data); // Para depuración
            
            tableSelect.innerHTML = '<option value="">Seleccione una mesa</option>';
            
            if (result.data.length === 0) {
                tableSelect.innerHTML = '<option value="">No hay mesas disponibles en esta zona</option>';
                return;
            }
            
            result.data.forEach(table => {
                const option = new Option(
                    `${table.name} (Capacidad: ${table.capacity})`,
                    table.id
                );
                tableSelect.add(option);
            });
            
            // Seleccionar valor anterior si existe
            const oldTableId = '{{ old("table_id") }}';
            if (oldTableId) {
                tableSelect.value = oldTableId;
            }
        } catch (error) {
            console.error('Error al cargar mesas:', error);
            tableSelect.innerHTML = `<option value="">Error: ${error.message}</option>`;
        }
    });
    
    // Disparar cambio si hay zona seleccionada
    const oldZoneId = '{{ old("zone_id") }}';
    if (oldZoneId) {
        zoneSelect.value = oldZoneId;
        zoneSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush
</x-app-layout>