<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mis Reservas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">Listado de Reservas</h3>
                        <a href="{{ route('reservations.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Nueva Reserva
                        </a>
                    </div>

                    @if ($reservations->isEmpty())
                        <p class="text-gray-500">No tienes reservas registradas.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hora</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mesa</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invitados</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($reservations as $reservation)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $reservation->reservation_date->format('d/m/Y') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $reservation->start_time->format('H:i') }} - {{ $reservation->end_time->format('H:i') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $reservation->table->name }} ({{ $reservation->table->zone->name }})</td>
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
                                                    @case('completed')
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Completada</span>
                                                        @break
                                                    @case('no_show')
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">No asistió</span>
                                                        @break
                                                @endswitch
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('reservations.show', $reservation) }}" class="text-blue-600 hover:text-blue-900 mr-3">Ver</a>
                                                @if($reservation->status === 'pending' || $reservation->status === 'confirmed')
                                                    <a href="{{ route('reservations.edit', $reservation) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</a>
                                                    <form action="{{ route('reservations.destroy', $reservation) }}" method="POST" class="inline">
    @csrf
    @method('DELETE')
    <button type="submit" 
            class="text-red-600 hover:text-red-900"
            onclick="return confirm('¿Estás seguro de que deseas cancelar esta reserva? Esta acción no se puede deshacer.')">
        Cancelar
    </button>
</form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>