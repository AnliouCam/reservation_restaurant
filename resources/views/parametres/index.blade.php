<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-2xl font-semibold text-anthracite">
            Parametres
        </h2>
    </x-slot>

    <!-- Navigation des parametres -->
    <div class="mb-6">
        <nav class="flex space-x-4">
            <a href="{{ route('parametres.index') }}" class="px-4 py-2 text-sm font-medium rounded-lg bg-primary-500 text-white">
                General
            </a>
            <a href="{{ route('parametres.horaires') }}" class="px-4 py-2 text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-100">
                Horaires
            </a>
            <a href="{{ route('users.index') }}" class="px-4 py-2 text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-100">
                Utilisateurs
            </a>
        </nav>
    </div>

    <!-- Message de succes -->
    @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-sm text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Formulaire parametres generaux -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-display text-lg font-semibold text-anthracite mb-6">Informations du restaurant</h3>

        <form method="POST" action="{{ route('parametres.update.general') }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nom du restaurant -->
                <div>
                    <label for="nom_restaurant" class="block text-sm font-medium text-gray-700 mb-1">
                        Nom du restaurant <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nom_restaurant" id="nom_restaurant"
                           value="{{ old('nom_restaurant', $parametres['nom_restaurant']) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('nom_restaurant') border-red-500 @enderror"
                           required>
                    @error('nom_restaurant')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Telephone -->
                <div>
                    <label for="telephone" class="block text-sm font-medium text-gray-700 mb-1">
                        Telephone
                    </label>
                    <input type="text" name="telephone" id="telephone"
                           value="{{ old('telephone', $parametres['telephone']) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('telephone') border-red-500 @enderror">
                    @error('telephone')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email de contact
                    </label>
                    <input type="email" name="email" id="email"
                           value="{{ old('email', $parametres['email']) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Duree reservation -->
                <div>
                    <label for="duree_reservation" class="block text-sm font-medium text-gray-700 mb-1">
                        Duree moyenne d'une reservation (minutes) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="duree_reservation" id="duree_reservation"
                           value="{{ old('duree_reservation', $parametres['duree_reservation']) }}"
                           min="30" max="300" step="15"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('duree_reservation') border-red-500 @enderror"
                           required>
                    @error('duree_reservation')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Adresse (pleine largeur) -->
                <div class="md:col-span-2">
                    <label for="adresse" class="block text-sm font-medium text-gray-700 mb-1">
                        Adresse
                    </label>
                    <textarea name="adresse" id="adresse" rows="2"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('adresse') border-red-500 @enderror">{{ old('adresse', $parametres['adresse']) }}</textarea>
                    @error('adresse')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Bouton sauvegarder -->
            <div class="mt-6 flex justify-end">
                <button type="submit" class="px-6 py-2 bg-primary-500 text-white text-sm font-medium rounded-lg hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
