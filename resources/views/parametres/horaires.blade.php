<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-2xl font-semibold text-anthracite">
            Parametres
        </h2>
    </x-slot>

    <!-- Navigation des parametres -->
    <div class="mb-6">
        <nav class="flex space-x-4">
            <a href="{{ route('parametres.index') }}" class="px-4 py-2 text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-100">
                General
            </a>
            <a href="{{ route('parametres.horaires') }}" class="px-4 py-2 text-sm font-medium rounded-lg bg-primary-500 text-white">
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

    <!-- Formulaire horaires -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-display text-lg font-semibold text-anthracite mb-6">Horaires d'ouverture</h3>

        <form method="POST" action="{{ route('parametres.update.horaires') }}">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                @foreach($jours as $jour)
                    @php
                        $jourData = $horaires[$jour] ?? ['ouvert' => false, 'midi' => '', 'soir' => ''];
                    @endphp
                    <div class="flex items-center gap-4 p-4 rounded-lg border border-gray-200 hover:border-gray-300 transition-colors">
                        <!-- Checkbox ouvert -->
                        <div class="w-32">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="horaires[{{ $jour }}][ouvert]" value="1"
                                       {{ $jourData['ouvert'] ? 'checked' : '' }}
                                       class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
                                       onchange="toggleJour('{{ $jour }}', this.checked)">
                                <span class="ml-2 text-sm font-medium text-gray-700 capitalize">{{ $jour }}</span>
                            </label>
                        </div>

                        <!-- Horaires midi -->
                        <div class="flex-1">
                            <label class="block text-xs text-gray-500 mb-1">Service midi</label>
                            <input type="text" name="horaires[{{ $jour }}][midi]" id="{{ $jour }}_midi"
                                   value="{{ $jourData['midi'] }}"
                                   placeholder="12:00-14:00"
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 disabled:bg-gray-100"
                                   {{ !$jourData['ouvert'] ? 'disabled' : '' }}>
                        </div>

                        <!-- Horaires soir -->
                        <div class="flex-1">
                            <label class="block text-xs text-gray-500 mb-1">Service soir</label>
                            <input type="text" name="horaires[{{ $jour }}][soir]" id="{{ $jour }}_soir"
                                   value="{{ $jourData['soir'] }}"
                                   placeholder="19:00-22:00"
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 disabled:bg-gray-100"
                                   {{ !$jourData['ouvert'] ? 'disabled' : '' }}>
                        </div>
                    </div>
                @endforeach
            </div>

            <p class="mt-4 text-sm text-gray-500">
                Format des horaires : HH:MM-HH:MM (ex: 12:00-14:00)
            </p>

            <!-- Bouton sauvegarder -->
            <div class="mt-6 flex justify-end">
                <button type="submit" class="px-6 py-2 bg-primary-500 text-white text-sm font-medium rounded-lg hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>

    <script>
        function toggleJour(jour, ouvert) {
            const midiInput = document.getElementById(jour + '_midi');
            const soirInput = document.getElementById(jour + '_soir');
            midiInput.disabled = !ouvert;
            soirInput.disabled = !ouvert;
        }
    </script>
</x-app-layout>
