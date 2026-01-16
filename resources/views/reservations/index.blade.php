<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-display text-2xl font-semibold text-anthracite">
                Reservations
            </h2>
            <a href="{{ route('reservations.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-500 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Nouvelle Reservation
            </a>
        </div>
    </x-slot>

    <!-- Filtres -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('reservations.index') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[150px]">
                <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                <input type="date" name="date" id="date" value="{{ request('date') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div class="flex-1 min-w-[150px]">
                <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select name="statut" id="statut" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Tous les statuts</option>
                    <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                    <option value="confirmee" {{ request('statut') == 'confirmee' ? 'selected' : '' }}>Confirmee</option>
                    <option value="terminee" {{ request('statut') == 'terminee' ? 'selected' : '' }}>Terminee</option>
                    <option value="annulee" {{ request('statut') == 'annulee' ? 'selected' : '' }}>Annulee</option>
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label for="recherche" class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                <input type="text" name="recherche" id="recherche" value="{{ request('recherche') }}"
                       placeholder="Nom ou telephone..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-anthracite text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors">
                    Filtrer
                </button>
                @if (request()->hasAny(['date', 'statut', 'recherche']))
                    <a href="{{ route('reservations.index') }}" class="px-4 py-2 text-gray-600 text-sm font-medium hover:text-gray-800 transition-colors">
                        Reinitialiser
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Stats rapides -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-4">
            <div class="text-2xl font-bold text-anthracite">{{ $stats['total'] }}</div>
            <div class="text-sm text-gray-500">Reservations</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-yellow-500">
            <div class="text-2xl font-bold text-yellow-600">{{ $stats['en_attente'] }}</div>
            <div class="text-sm text-gray-500">En attente</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-statut-disponible">
            <div class="text-2xl font-bold text-statut-disponible">{{ $stats['confirmee'] }}</div>
            <div class="text-sm text-gray-500">Confirmees</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-primary-500">
            <div class="text-2xl font-bold text-primary-500">{{ $stats['personnes'] }}</div>
            <div class="text-sm text-gray-500">Couverts prevus</div>
        </div>
    </div>

    <!-- Liste des reservations -->
    @if ($reservations->isEmpty())
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Aucune reservation</h3>
                <p class="mt-1 text-sm text-gray-500">Aucune reservation pour cette date.</p>
                <div class="mt-6">
                    <a href="{{ route('reservations.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Nouvelle reservation
                    </a>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Heure</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Client</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Table</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pers.</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($reservations as $reservation)
                        @php
                            $statutColors = [
                                'en_attente' => 'bg-yellow-100 text-yellow-800',
                                'confirmee' => 'bg-green-100 text-green-800',
                                'terminee' => 'bg-gray-100 text-gray-800',
                                'annulee' => 'bg-red-100 text-red-800',
                            ];
                            $statutLabels = [
                                'en_attente' => 'En attente',
                                'confirmee' => 'Confirmee',
                                'terminee' => 'Terminee',
                                'annulee' => 'Annulee',
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-lg font-semibold text-anthracite">
                                    {{ \Carbon\Carbon::parse($reservation->heure_reservation)->format('H:i') }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $reservation->client_nom }}</div>
                                <div class="text-sm text-gray-500">{{ $reservation->client_telephone }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">Table {{ $reservation->table->numero }}</div>
                                <div class="text-sm text-gray-500">{{ $reservation->table->zone->nom }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center text-sm text-gray-900">
                                    <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ $reservation->nombre_personnes }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $statutColors[$reservation->statut] }}">
                                    {{ $statutLabels[$reservation->statut] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <!-- Actions rapides selon le statut -->
                                    @if ($reservation->statut === 'en_attente')
                                        <form action="{{ route('reservations.statut', $reservation) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="statut" value="confirmee">
                                            <button type="submit" class="px-2 py-1 text-xs font-medium text-green-600 border border-green-600 rounded hover:bg-green-50 transition-colors" title="Client arrive">
                                                Arrivee
                                            </button>
                                        </form>
                                        <form action="{{ route('reservations.statut', $reservation) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="statut" value="annulee">
                                            <button type="submit" class="px-2 py-1 text-xs font-medium text-red-600 border border-red-600 rounded hover:bg-red-50 transition-colors" title="Annuler">
                                                Annuler
                                            </button>
                                        </form>
                                    @elseif ($reservation->statut === 'confirmee')
                                        <form action="{{ route('reservations.statut', $reservation) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="statut" value="terminee">
                                            <button type="submit" class="px-2 py-1 text-xs font-medium text-gray-600 border border-gray-600 rounded hover:bg-gray-50 transition-colors" title="Terminer">
                                                Terminer
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('reservations.edit', $reservation) }}" class="text-primary-600 hover:text-primary-800 text-sm font-medium">
                                        Modifier
                                    </a>
                                    <form action="{{ route('reservations.destroy', $reservation) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cette reservation ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                            Suppr.
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-app-layout>
