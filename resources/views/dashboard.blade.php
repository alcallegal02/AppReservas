<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard - Mis Reservas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Botón para nueva reserva -->
                    <div class="mb-6">
                        <a href="{{ route('reservations.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Nueva Reserva
                        </a>
                    </div>

                    <!-- Listado de reservas -->
                    @if($reservations->isEmpty())
                        <p class="text-gray-500">No tienes reservas registradas.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hora</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mesa</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Personas</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($reservations as $reservation)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $reservation->reservation_date->format('d/m/Y') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $reservation->start_time->format('H:i') }} - {{ $reservation->end_time->format('H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $reservation->table->name }} ({{ $reservation->table->zone->name }})
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $reservation->guest_count }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
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
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('reservations.show', $reservation) }}" class="text-blue-600 hover:text-blue-900 mr-3">Ver</a>
                                                <a href="{{ route('reservations.edit', $reservation) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</a>
                                                <form action="{{ route('reservations.destroy', $reservation) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('¿Estás seguro de cancelar esta reserva?')">Cancelar</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Paginación -->
                        <div class="mt-4">
                            {{ $reservations->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>