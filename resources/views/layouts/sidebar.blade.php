<!-- Sidebar -->
<aside x-data="{ open: false }"
       :class="open ? 'translate-x-0' : '-translate-x-full'"
       class="fixed inset-y-0 left-0 z-50 w-64 bg-anthracite text-white transform transition-transform duration-300 ease-in-out lg:translate-x-0">

    <!-- Logo / Brand -->
    <div class="h-16 flex items-center justify-center border-b border-gray-700">
        <a href="{{ route('reservations.index') }}" class="flex items-center space-x-2">
            <svg class="w-8 h-8 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            <span class="font-display text-xl font-semibold text-primary-500">Restaurant</span>
        </a>
    </div>

    <!-- Navigation -->
    <nav class="mt-6 px-3">
        <!-- Section Admin -->
        @if(auth()->user()->isAdmin())
            <div class="mb-6">
                <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Administration</p>
                <div class="mt-3 space-y-1">
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-primary-500 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Dashboard
                    </a>

                    <!-- Zones -->
                    <a href="{{ route('zones.index') }}"
                       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('zones.*') ? 'bg-primary-500 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Zones
                    </a>

                    <!-- Tables -->
                    <a href="{{ route('tables.index') }}"
                       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('tables.*') ? 'bg-primary-500 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                        </svg>
                        Tables
                    </a>
                </div>
            </div>
        @endif

        <!-- Section Reservations -->
        <div class="mb-6">
            <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Reservations</p>
            <div class="mt-3 space-y-1">
                <!-- Liste Reservations -->
                <a href="{{ route('reservations.index') }}"
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('reservations.*') ? 'bg-primary-500 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Reservations
                </a>

                <!-- Nouvelle Reservation -->
                <a href="{{ route('reservations.create') }}"
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('reservations.create') ? 'bg-primary-500 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Nouvelle
                </a>
            </div>
        </div>
    </nav>

    <!-- User Info (bottom) -->
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-700">
        <div class="flex items-center">
            <div class="w-10 h-10 rounded-full bg-primary-500 flex items-center justify-center text-white font-semibold">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
            <div class="ml-3 flex-1 min-w-0">
                <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-400 capitalize">{{ auth()->user()->role }}</p>
            </div>
        </div>
    </div>
</aside>

<!-- Overlay for mobile -->
<div x-show="open"
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click="open = false"
     class="fixed inset-0 z-40 bg-black bg-opacity-50 lg:hidden">
</div>
