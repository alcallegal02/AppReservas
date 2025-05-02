<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Reserva') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('reservations.update', $reservation->id) }}" id="reservationForm">
                        @csrf
                        @method('PUT')

                        @if ($errors->any())
                            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                                <ul class="list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="grid grid-cols-1 gap-6 mt-4 sm:grid-cols-2">
                            <!-- Fecha de Reserva -->
                            <div>
                                <x-label for="reservation_date" :value="__('Fecha de Reserva')" />
                                <x-input id="reservation_date" class="block mt-1 w-full" type="date" name="reservation_date" 
                                    value="{{ old('reservation_date', $reservation->reservation_date->format('Y-m-d')) }}" required />
                            </div>

                            <!-- Franja Horaria -->
                            <div>
                                <x-label for="time_slot_id" :value="__('Franja Horaria')" />
                                <select id="time_slot_id" name="time_slot_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                    <option value="">Seleccione una franja horaria</option>
                                    @foreach($timeSlots as $timeSlot)
                                        <option value="{{ $timeSlot->id }}" 
                                            {{ old('time_slot_id', $reservation->time_slot_id) == $timeSlot->id ? 'selected' : '' }}>
                                            {{ $timeSlot->name }} ({{ $timeSlot->start_time->format('H:i') }} - {{ $timeSlot->end_time->format('H:i') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Zona -->
                            <div>
                                <x-label for="zone_id" :value="__('Zona')" />
                                <select id="zone_id" name="zone_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                    <option value="">Seleccione una zona</option>
                                    @foreach($zones as $zone)
                                        <option value="{{ $zone->id }}" 
                                            {{ old('zone_id', $reservation->table->zone_id) == $zone->id ? 'selected' : '' }}>
                                            {{ $zone->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Número de Invitados -->
                            <div>
                                <x-label for="guest_count" :value="__('Número de Invitados')" />
                                <x-input id="guest_count" class="block mt-1 w-full" type="number" name="guest_count" 
                                    value="{{ old('guest_count', $reservation->guest_count) }}" min="1" required />
                            </div>

                            <!-- Solicitudes Especiales -->
                            <div class="sm:col-span-2">
                                <x-label for="special_requests" :value="__('Solicitudes Especiales')" />
                                <textarea id="special_requests" name="special_requests" rows="3"
                                    class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('special_requests', $reservation->special_requests) }}</textarea>
                            </div>
                        </div>

                        <!-- Días de cierre -->
                        @if($closures->isNotEmpty())
                            <div class="mt-6 p-4 bg-yellow-50 rounded-lg">
                                <h4 class="font-medium text-yellow-800">El restaurante estará cerrado en las siguientes fechas:</h4>
                                <ul class="list-disc pl-5 mt-2 text-sm text-yellow-700">
                                    @foreach($closures as $closure)
                                        <li>{{ $closure->closure_date->format('d/m/Y') }} - {{ $closure->reason }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('reservations.index') }}" class="mr-4 text-gray-600 hover:text-gray-900">
                                {{ __('Cancelar') }}
                            </a>
                            <x-button type="submit" class="ml-4">
                                {{ __('Actualizar Reserva') }}
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
            const form = document.getElementById('reservationForm');
            
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Mostrar spinner de carga
                const button = form.querySelector('button[type="submit"]');
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Actualizando...';
                
                // Enviar el formulario
                form.submit();
            });
        });
    </script>
    @endpush
</x-app-layout>