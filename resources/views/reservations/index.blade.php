<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-display text-2xl font-semibold text-anthracite">
                Reservations
            </h2>
            <button disabled class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-lg font-medium text-sm text-gray-500 cursor-not-allowed">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Nouvelle Reservation
                <span class="ml-2 text-xs bg-gray-400 text-white px-2 py-0.5 rounded">Bientot</span>
            </button>
        </div>
    </x-slot>

    <!-- Message temporaire -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="text-center py-16">
            <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <h3 class="mt-4 text-xl font-display font-semibold text-gray-900">Module Reservations</h3>
            <p class="mt-2 text-gray-500 max-w-md mx-auto">
                Ce module est en cours de developpement. Vous pourrez bientot creer et gerer les reservations de vos clients.
            </p>
            <div class="mt-8 flex items-center justify-center space-x-4">
                <div class="flex items-center text-sm text-gray-500">
                    <svg class="w-5 h-5 mr-2 text-status-disponible" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Zones configurees
                </div>
                <div class="flex items-center text-sm text-gray-500">
                    <svg class="w-5 h-5 mr-2 text-status-reservee" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Tables a venir
                </div>
                <div class="flex items-center text-sm text-gray-500">
                    <svg class="w-5 h-5 mr-2 text-status-reservee" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Reservations a venir
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
