<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-display text-2xl font-semibold text-anthracite">
                Gestion des Tables
            </h2>
            <a href="{{ route('tables.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-500 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Nouvelle Table
            </a>
        </div>
    </x-slot>

    <!-- Filtres -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('tables.index') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label for="zone_id" class="block text-sm font-medium text-gray-700 mb-1">Zone</label>
                <select name="zone_id" id="zone_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Toutes les zones</option>
                    @foreach ($zones as $zone)
                        <option value="{{ $zone->id }}" {{ request('zone_id') == $zone->id ? 'selected' : '' }}>
                            {{ $zone->nom }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select name="statut" id="statut" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Tous les statuts</option>
                    <option value="disponible" {{ request('statut') == 'disponible' ? 'selected' : '' }}>Disponible</option>
                    <option value="reservee" {{ request('statut') == 'reservee' ? 'selected' : '' }}>Reservee</option>
                    <option value="occupee" {{ request('statut') == 'occupee' ? 'selected' : '' }}>Occupee</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-anthracite text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors">
                    Filtrer
                </button>
                @if (request()->hasAny(['zone_id', 'statut']))
                    <a href="{{ route('tables.index') }}" class="px-4 py-2 text-gray-600 text-sm font-medium hover:text-gray-800 transition-colors">
                        Reinitialiser
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Stats rapides -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-statut-disponible">
            <div class="text-2xl font-bold text-statut-disponible">{{ $stats['disponible'] }}</div>
            <div class="text-sm text-gray-500">Disponibles</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-statut-reservee">
            <div class="text-2xl font-bold text-statut-reservee">{{ $stats['reservee'] }}</div>
            <div class="text-sm text-gray-500">Reservees</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-statut-occupee">
            <div class="text-2xl font-bold text-statut-occupee">{{ $stats['occupee'] }}</div>
            <div class="text-sm text-gray-500">Occupees</div>
        </div>
    </div>

    <!-- Liste des tables en cartes -->
    @if ($tables->isEmpty())
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Aucune table</h3>
                <p class="mt-1 text-sm text-gray-500">
                    @if ($zones->isEmpty())
                        Commencez par creer une zone avant d'ajouter des tables.
                    @else
                        Commencez par creer votre premiere table.
                    @endif
                </p>
                <div class="mt-6">
                    @if ($zones->isEmpty())
                        <a href="{{ route('zones.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Creer une zone
                        </a>
                    @else
                        <a href="{{ route('tables.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Creer une table
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach ($tables as $table)
                @php
                    $statutColors = [
                        'disponible' => 'border-statut-disponible bg-green-50',
                        'reservee' => 'border-statut-reservee bg-orange-50',
                        'occupee' => 'border-statut-occupee bg-red-50',
                    ];
                    $statutBadge = [
                        'disponible' => 'bg-statut-disponible',
                        'reservee' => 'bg-statut-reservee',
                        'occupee' => 'bg-statut-occupee',
                    ];
                    $statutLabel = [
                        'disponible' => 'Disponible',
                        'reservee' => 'Reservee',
                        'occupee' => 'Occupee',
                    ];
                @endphp
                <div class="bg-white rounded-xl shadow-sm border-l-4 {{ $statutColors[$table->statut] }} overflow-hidden">
                    <div class="p-4">
                        <!-- Header carte -->
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <div class="w-12 h-12 rounded-lg bg-anthracite flex items-center justify-center text-white font-bold text-lg">
                                    {{ $table->numero }}
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm text-gray-500">{{ $table->zone->nom }}</div>
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        {{ $table->capacite }} pers.
                                    </div>
                                </div>
                            </div>
                            <span class="px-2 py-1 text-xs font-medium text-white rounded {{ $statutBadge[$table->statut] }}">
                                {{ $statutLabel[$table->statut] }}
                            </span>
                        </div>

                        <!-- Actions rapides statut -->
                        <div class="flex gap-1 mb-3">
                            @if ($table->statut !== 'disponible')
                                <form action="{{ route('tables.statut', $table) }}" method="POST" class="flex-1">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="statut" value="disponible">
                                    <button type="submit" class="w-full px-2 py-1 text-xs font-medium text-statut-disponible border border-statut-disponible rounded hover:bg-green-50 transition-colors">
                                        Liberer
                                    </button>
                                </form>
                            @endif
                            @if ($table->statut !== 'reservee')
                                <form action="{{ route('tables.statut', $table) }}" method="POST" class="flex-1">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="statut" value="reservee">
                                    <button type="submit" class="w-full px-2 py-1 text-xs font-medium text-statut-reservee border border-statut-reservee rounded hover:bg-orange-50 transition-colors">
                                        Reserver
                                    </button>
                                </form>
                            @endif
                            @if ($table->statut !== 'occupee')
                                <form action="{{ route('tables.statut', $table) }}" method="POST" class="flex-1">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="statut" value="occupee">
                                    <button type="submit" class="w-full px-2 py-1 text-xs font-medium text-statut-occupee border border-statut-occupee rounded hover:bg-red-50 transition-colors">
                                        Occuper
                                    </button>
                                </form>
                            @endif
                        </div>

                        <!-- Actions CRUD -->
                        <div class="flex items-center justify-between pt-3 border-t border-gray-200">
                            <a href="{{ route('tables.edit', $table) }}" class="text-sm text-primary-600 hover:text-primary-800 font-medium">
                                Modifier
                            </a>
                            <form action="{{ route('tables.destroy', $table) }}" method="POST" onsubmit="return confirm('Etes-vous sur de vouloir supprimer cette table ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm text-red-600 hover:text-red-800 font-medium">
                                    Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-app-layout>
