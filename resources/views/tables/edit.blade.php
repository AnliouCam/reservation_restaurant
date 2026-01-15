<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('tables.index') }}" class="mr-4 p-2 rounded-lg hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h2 class="font-display text-2xl font-semibold text-anthracite">
                Modifier la Table {{ $table->numero }}
            </h2>
        </div>
    </x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <form method="POST" action="{{ route('tables.update', $table) }}" class="p-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Numero -->
                    <div>
                        <label for="numero" class="block text-sm font-medium text-gray-700 mb-2">
                            Numero de table <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="numero"
                               name="numero"
                               value="{{ old('numero', $table->numero) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                               placeholder="Ex: T1, 12, A3..."
                               required
                               autofocus>
                        @error('numero')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Capacite -->
                    <div>
                        <label for="capacite" class="block text-sm font-medium text-gray-700 mb-2">
                            Capacite (personnes) <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               id="capacite"
                               name="capacite"
                               value="{{ old('capacite', $table->capacite) }}"
                               min="1"
                               max="20"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                               required>
                        @error('capacite')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Zone -->
                <div class="mt-6">
                    <label for="zone_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Zone <span class="text-red-500">*</span>
                    </label>
                    <select id="zone_id"
                            name="zone_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                            required>
                        <option value="">Selectionnez une zone</option>
                        @foreach ($zones as $zone)
                            <option value="{{ $zone->id }}" {{ old('zone_id', $table->zone_id) == $zone->id ? 'selected' : '' }}>
                                {{ $zone->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('zone_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Statut -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Statut <span class="text-red-500">*</span>
                    </label>
                    <div class="flex flex-wrap gap-4">
                        <label class="flex items-center">
                            <input type="radio" name="statut" value="disponible" {{ old('statut', $table->statut) === 'disponible' ? 'checked' : '' }}
                                   class="w-4 h-4 text-statut-disponible border-gray-300 focus:ring-statut-disponible">
                            <span class="ml-2 flex items-center">
                                <span class="w-3 h-3 rounded-full bg-statut-disponible mr-2"></span>
                                Disponible
                            </span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="statut" value="reservee" {{ old('statut', $table->statut) === 'reservee' ? 'checked' : '' }}
                                   class="w-4 h-4 text-statut-reservee border-gray-300 focus:ring-statut-reservee">
                            <span class="ml-2 flex items-center">
                                <span class="w-3 h-3 rounded-full bg-statut-reservee mr-2"></span>
                                Reservee
                            </span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="statut" value="occupee" {{ old('statut', $table->statut) === 'occupee' ? 'checked' : '' }}
                                   class="w-4 h-4 text-statut-occupee border-gray-300 focus:ring-statut-occupee">
                            <span class="ml-2 flex items-center">
                                <span class="w-3 h-3 rounded-full bg-statut-occupee mr-2"></span>
                                Occupee
                            </span>
                        </label>
                    </div>
                    @error('statut')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end space-x-4 pt-6 mt-6 border-t border-gray-200">
                    <a href="{{ route('tables.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors">
                        Annuler
                    </a>
                    <button type="submit" class="px-6 py-2 bg-primary-500 text-white text-sm font-medium rounded-lg hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors">
                        Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
