<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="font-display text-2xl font-semibold text-anthracite">Connexion</h2>
        <p class="text-sm text-gray-500 mt-1">Accedez a votre espace de gestion</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input id="email"
                   type="email"
                   name="email"
                   value="{{ old('email') }}"
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                   placeholder="votre@email.com"
                   required
                   autofocus
                   autocomplete="username">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
            <input id="password"
                   type="password"
                   name="password"
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                   placeholder="••••••••"
                   required
                   autocomplete="current-password">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me"
                       type="checkbox"
                       class="w-4 h-4 rounded border-gray-300 text-primary-500 focus:ring-primary-500"
                       name="remember">
                <span class="ml-2 text-sm text-gray-600">Se souvenir de moi</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-primary-600 hover:text-primary-800 transition-colors" href="{{ route('password.request') }}">
                    Mot de passe oublie ?
                </a>
            @endif
        </div>

        <div class="mt-6">
            <button type="submit" class="w-full px-4 py-2.5 bg-primary-500 text-white font-medium rounded-lg hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors">
                Se connecter
            </button>
        </div>
    </form>
</x-guest-layout>
