<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('reservations.index') }}" class="mr-4 p-2 rounded-lg hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h2 class="font-display text-2xl font-semibold text-anthracite">
                Modifier la Reservation
            </h2>
        </div>
    </x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <form method="POST" action="{{ route('reservations.update', $reservation) }}" class="p-6">
                @csrf
                @method('PUT')

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
                                   value="{{ old('client_nom', $reservation->client_nom) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                                   placeholder="Nom complet"
                                   required>
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
                                   value="{{ old('client_telephone', $reservation->client_telephone) }}"
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
                                   value="{{ old('date_reservation', $reservation->date_reservation->format('Y-m-d')) }}"
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
                                   value="{{ old('heure_reservation', \Carbon\Carbon::parse($reservation->heure_reservation)->format('H:i')) }}"
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
                                   value="{{ old('nombre_personnes', $reservation->nombre_personnes) }}"
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
                    <select id="table_id"
                            name="table_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                            required>
                        <option value="">Selectionnez une table</option>
                        @foreach ($tables->groupBy('zone.nom') as $zoneName => $zoneTables)
                            <optgroup label="{{ $zoneName }}">
                                @foreach ($zoneTables as $table)
                                    <option value="{{ $table->id }}" {{ old('table_id', $reservation->table_id) == $table->id ? 'selected' : '' }}>
                                        Table {{ $table->numero }} ({{ $table->capacite }} pers.)
                                        @if ($table->statut !== 'disponible' && $table->id !== $reservation->table_id)
                                            - {{ ucfirst($table->statut) }}
                                        @endif
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    @error('table_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Statut -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Statut <span class="text-red-500">*</span>
                    </label>
                    <div class="flex flex-wrap gap-4">
                        @php
                            $statuts = [
                                'en_attente' => ['label' => 'En attente', 'color' => 'yellow'],
                                'confirmee' => ['label' => 'Confirmee', 'color' => 'green'],
                                'terminee' => ['label' => 'Terminee', 'color' => 'gray'],
                                'annulee' => ['label' => 'Annulee', 'color' => 'red'],
                            ];
                        @endphp
                        @foreach ($statuts as $value => $info)
                            <label class="flex items-center">
                                <input type="radio" name="statut" value="{{ $value }}"
                                       {{ old('statut', $reservation->statut) === $value ? 'checked' : '' }}
                                       class="w-4 h-4 text-{{ $info['color'] }}-500 border-gray-300 focus:ring-{{ $info['color'] }}-500">
                                <span class="ml-2 flex items-center">
                                    <span class="w-3 h-3 rounded-full bg-{{ $info['color'] }}-500 mr-2"></span>
                                    {{ $info['label'] }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @error('statut')
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
                              placeholder="Notes particulieres, allergies, occasion speciale...">{{ old('commentaire', $reservation->commentaire) }}</textarea>
                    @error('commentaire')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Info creation -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg text-sm text-gray-500">
                    Reservation creee par <strong>{{ $reservation->user->name }}</strong>
                    le {{ $reservation->created_at->format('d/m/Y à H:i') }}
                </div>

                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('reservations.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors">
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
