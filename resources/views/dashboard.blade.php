<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-2xl font-semibold text-anthracite">
            Dashboard
        </h2>
    </x-slot>

    <!-- Stats Cards - Ligne 1 -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Reservations du jour -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 rounded-lg bg-primary-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Reservations du jour</p>
                    <p class="text-2xl font-semibold text-anthracite">{{ $reservationsJour }}</p>
                </div>
            </div>
        </div>

        <!-- Couverts prevus -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Couverts prevus</p>
                    <p class="text-2xl font-semibold text-anthracite">{{ $couvertsPrevus }}</p>
                </div>
            </div>
        </div>

        <!-- Tables disponibles -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 rounded-lg bg-green-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-statut-disponible" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Tables disponibles</p>
                    <p class="text-2xl font-semibold text-statut-disponible">{{ $tablesDisponibles }}<span class="text-sm text-gray-400">/{{ $totalTables }}</span></p>
                </div>
            </div>
        </div>

        <!-- Taux d'occupation -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 rounded-lg bg-purple-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Taux d'occupation</p>
                    <p class="text-2xl font-semibold text-anthracite">{{ $tauxOccupation }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards - Ligne 2 -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Reservations semaine -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Cette semaine</p>
                    <p class="text-3xl font-semibold text-anthracite">{{ $reservationsSemaine }}</p>
                    <p class="text-xs text-gray-400">reservations</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-indigo-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Reservations mois -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Ce mois</p>
                    <p class="text-3xl font-semibold text-anthracite">{{ $reservationsMois }}</p>
                    <p class="text-xs text-gray-400">reservations</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-pink-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Statut des tables -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <p class="text-sm font-medium text-gray-500 mb-3">Statut des tables</p>
            <div class="flex items-center space-x-4">
                <div class="flex items-center">
                    <span class="w-3 h-3 rounded-full bg-statut-disponible mr-2"></span>
                    <span class="text-sm text-gray-600">{{ $tablesDisponibles }} dispo</span>
                </div>
                <div class="flex items-center">
                    <span class="w-3 h-3 rounded-full bg-statut-reservee mr-2"></span>
                    <span class="text-sm text-gray-600">{{ $tablesReservees }} reserv.</span>
                </div>
                <div class="flex items-center">
                    <span class="w-3 h-3 rounded-full bg-statut-occupee mr-2"></span>
                    <span class="text-sm text-gray-600">{{ $tablesOccupees }} occup.</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Prochaines reservations -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-display text-lg font-semibold text-anthracite">Prochaines reservations</h3>
                <a href="{{ route('reservations.index') }}" class="text-sm text-primary-600 hover:text-primary-800">Voir tout</a>
            </div>
            @if($prochainesReservations->isEmpty())
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">Aucune reservation pour aujourd'hui</p>
                    <a href="{{ route('reservations.create') }}" class="mt-3 inline-flex items-center text-sm text-primary-600 hover:text-primary-800">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Nouvelle reservation
                    </a>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($prochainesReservations as $reservation)
                        @php
                            $statutColors = [
                                'en_attente' => 'bg-yellow-100 text-yellow-800',
                                'confirmee' => 'bg-green-100 text-green-800',
                            ];
                        @endphp
                        <div class="flex items-center justify-between p-3 rounded-lg border border-gray-100 hover:border-gray-200 transition-colors">
                            <div class="flex items-center">
                                <div class="w-12 h-12 rounded-lg bg-primary-50 flex items-center justify-center">
                                    <span class="text-lg font-semibold text-primary-600">{{ \Carbon\Carbon::parse($reservation->heure_reservation)->format('H:i') }}</span>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900">{{ $reservation->client_nom }}</p>
                                    <p class="text-xs text-gray-500">Table {{ $reservation->table->numero }} - {{ $reservation->table->zone->nom }} - {{ $reservation->nombre_personnes }} pers.</p>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $statutColors[$reservation->statut] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $reservation->statut === 'en_attente' ? 'En attente' : 'Confirmee' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Colonne droite -->
        <div class="space-y-6">
            <!-- Stats par zone -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-display text-lg font-semibold text-anthracite mb-4">Tables par zone</h3>
                <div class="space-y-3">
                    @forelse($statsParZone as $zone)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">{{ $zone->nom }}</span>
                            <div class="flex items-center">
                                <span class="text-sm font-medium text-statut-disponible">{{ $zone->tables_disponibles_count }}</span>
                                <span class="text-sm text-gray-400 mx-1">/</span>
                                <span class="text-sm text-gray-600">{{ $zone->tables_count }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Aucune zone configuree</p>
                    @endforelse
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-display text-lg font-semibold text-anthracite mb-4">Actions rapides</h3>
                <div class="space-y-2">
                    <a href="{{ route('reservations.create') }}" class="flex items-center p-3 rounded-lg border border-gray-200 hover:border-primary-300 hover:bg-primary-50 transition-colors">
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span class="ml-3 text-sm font-medium text-gray-900">Nouvelle reservation</span>
                    </a>
                    <a href="{{ route('tables.index') }}" class="flex items-center p-3 rounded-lg border border-gray-200 hover:border-primary-300 hover:bg-primary-50 transition-colors">
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                        </svg>
                        <span class="ml-3 text-sm font-medium text-gray-900">Gerer les tables</span>
                    </a>
                    <a href="{{ route('zones.index') }}" class="flex items-center p-3 rounded-lg border border-gray-200 hover:border-primary-300 hover:bg-primary-50 transition-colors">
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <span class="ml-3 text-sm font-medium text-gray-900">Gerer les zones</span>
                    </a>
                </div>
            </div>

            <!-- Informations -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-display text-lg font-semibold text-anthracite mb-4">Informations</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-500">Utilisateur</span>
                        <span class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-500">Role</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800 capitalize">
                            {{ auth()->user()->role }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-gray-500">Date</span>
                        <span class="text-sm font-medium text-gray-900">{{ now()->translatedFormat('l j F Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
