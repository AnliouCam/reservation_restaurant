<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('reservations.index') }}" class="mr-4 p-2 rounded-lg hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h2 class="font-display text-2xl font-semibold text-anthracite">
                Nouvelle Reservation
            </h2>
        </div>
    </x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <form method="POST" action="{{ route('reservations.store') }}" class="p-6">
                @csrf

                <!-- Informations client -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informations client</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="client_nom" class="block text-sm font-medium text-gray-700 mb-2">
                                Nom du client <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   id="client_nom"
                                   name="client_nom"
                                   value="{{ old('client_nom') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                                   placeholder="Nom complet"
                                   required
                                   autofocus>
                            @error('client_nom')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="client_telephone" class="block text-sm font-medium text-gray-700 mb-2">
                                Telephone <span class="text-red-500">*</span>
                            </label>
                            <input type="tel"
                                   id="client_telephone"
                                   name="client_telephone"
                                   value="{{ old('client_telephone') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                                   placeholder="Ex: 0787606430"
                                   required>
                            @error('client_telephone')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Details reservation -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Details de la reservation</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="date_reservation" class="block text-sm font-medium text-gray-700 mb-2">
                                Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date"
                                   id="date_reservation"
                                   name="date_reservation"
                                   value="{{ old('date_reservation', $selectedDate) }}"
                                   min="{{ today()->format('Y-m-d') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                                   required>
                            @error('date_reservation')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="heure_reservation" class="block text-sm font-medium text-gray-700 mb-2">
                                Heure <span class="text-red-500">*</span>
                            </label>
                            <input type="time"
                                   id="heure_reservation"
                                   name="heure_reservation"
                                   value="{{ old('heure_reservation', '19:00') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                                   required>
                            @error('heure_reservation')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="nombre_personnes" class="block text-sm font-medium text-gray-700 mb-2">
                                Personnes <span class="text-red-500">*</span>
                            </label>
                            <input type="number"
                                   id="nombre_personnes"
                                   name="nombre_personnes"
                                   value="{{ old('nombre_personnes', 2) }}"
                                   min="1"
                                   max="20"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                                   required>
                            @error('nombre_personnes')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Selection de la table -->
                <div class="mb-6">
                    <label for="table_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Table <span class="text-red-500">*</span>
                    </label>
                    @if ($tables->isEmpty())
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-yellow-800 text-sm">
                            Aucune table disponible. Veuillez d'abord creer des tables ou liberer des tables occupees.
                        </div>
                    @else
                        <select id="table_id"
                                name="table_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                                required>
                            <option value="">Selectionnez une table</option>
                            @foreach ($tables->groupBy('zone.nom') as $zoneName => $zoneTables)
                                <optgroup label="{{ $zoneName }}">
                                    @foreach ($zoneTables as $table)
                                        <option value="{{ $table->id }}" {{ old('table_id') == $table->id ? 'selected' : '' }}>
                                            Table {{ $table->numero }} ({{ $table->capacite }} pers.)
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    @endif
                    @error('table_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Commentaire -->
                <div class="mb-6">
                    <label for="commentaire" class="block text-sm font-medium text-gray-700 mb-2">
                        Commentaire <span class="text-gray-400">(optionnel)</span>
                    </label>
                    <textarea id="commentaire"
                              name="commentaire"
                              rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                              placeholder="Notes particulieres, allergies, occasion speciale...">{{ old('commentaire') }}</textarea>
                    @error('commentaire')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('reservations.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors">
                        Annuler
                    </a>
                    <button type="submit" class="px-6 py-2 bg-primary-500 text-white text-sm font-medium rounded-lg hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors" {{ $tables->isEmpty() ? 'disabled' : '' }}>
                        Creer la reservation
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
