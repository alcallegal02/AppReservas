<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detalles de la Reserva') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if (session('error'))
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="grid grid-cols-1 gap-6 mt-4 sm:grid-cols-2">
                        <!-- Fecha de Reserva -->
                        <div>
                            <x-label :value="__('Fecha de Reserva')" />
                            <div class="block mt-1 w-full p-2 border border-gray-300 rounded-md bg-gray-50">
                                {{ $reservation->reservation_date->format('d/m/Y') }}
                            </div>
                        </div>

                        <!-- Franja Horaria -->
                        <div>
                            <x-label :value="__('Franja Horaria')" />
                            <div class="block mt-1 w-full p-2 border border-gray-300 rounded-md bg-gray-50">
                                {{ $reservation->timeSlot->name }} ({{ $reservation->start_time->format('H:i') }} - {{ $reservation->end_time->format('H:i') }})
                            </div>
                        </div>

                        <!-- Zona -->
                        <div>
                            <x-label :value="__('Zona')" />
                            <div class="block mt-1 w-full p-2 border border-gray-300 rounded-md bg-gray-50">
                                {{ $reservation->table->zone->name }}
                            </div>
                        </div>

                        <!-- Mesa -->
                        <div>
                            <x-label :value="__('Mesa')" />
                            <div class="block mt-1 w-full p-2 border border-gray-300 rounded-md bg-gray-50">
                                {{ $reservation->table->name }} (Capacidad: {{ $reservation->table->capacity }} personas)
                            </div>
                        </div>

                        <!-- Número de Invitados -->
                        <div>
                            <x-label :value="__('Número de Invitados')" />
                            <div class="block mt-1 w-full p-2 border border-gray-300 rounded-md bg-gray-50">
                                {{ $reservation->guest_count }}
                            </div>
                        </div>

                        <!-- Estado -->
                        <div>
                            <x-label :value="__('Estado')" />
                            <div class="block mt-1 w-full p-2 rounded-md">
                                @switch($reservation->status)
                                    @case('pending')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pendiente</span>
                                        @break
                                    @case('confirmed')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Confirmada</span>
                                        @break
                                    @case('cancelled')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Cancelada</span>
                                        @break
                                    @default
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">{{ ucfirst($reservation->status) }}</span>
                                @endswitch
                            </div>
                        </div>

                        <!-- Solicitudes Especiales -->
                        @if($reservation->special_requests)
                        <div class="sm:col-span-2">
                            <x-label :value="__('Solicitudes Especiales')" />
                            <div class="block mt-1 w-full p-2 border border-gray-300 rounded-md bg-gray-50 whitespace-pre-line">
                                {{ $reservation->special_requests }}
                            </div>
                        </div>
                        @endif
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
                            {{ __('Volver') }}
                        </a>
                        @if($reservation->status === 'pending' || $reservation->status === 'confirmed')
        <!-- Botón Editar -->
        <a href="{{ route('reservations.edit', $reservation) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:border-indigo-800 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
            {{ __('Editar') }}
        </a>
        
        <!-- Botón Cancelar (cambia estado) -->
        <form action="{{ route('reservations.destroy', $reservation) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas cancelar esta reserva?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 active:bg-yellow-800 focus:outline-none focus:border-yellow-800 focus:ring ring-yellow-300 disabled:opacity-25 transition ease-in-out duration-150">
                {{ __('Cancelar Reserva') }}
            </button>
        </form>
    @elseif($reservation->status === 'cancelled')
        <!-- Botón Eliminar Permanentemente -->
        <form action="{{ route('reservations.destroy', $reservation) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas ELIMINAR PERMANENTEMENTE esta reserva? Esta acción no se puede deshacer.');">
            @csrf
            @method('DELETE')
            <input type="hidden" name="complete_delete" value="1">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-800 focus:outline-none focus:border-red-800 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                {{ __('Eliminar') }}
            </button>
        </form>
    @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>