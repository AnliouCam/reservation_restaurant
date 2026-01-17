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
            <a href="{{ route('parametres.horaires') }}" class="px-4 py-2 text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-100">
                Horaires
            </a>
            <a href="{{ route('users.index') }}" class="px-4 py-2 text-sm font-medium rounded-lg bg-primary-500 text-white">
                Utilisateurs
            </a>
        </nav>
    </div>

    <!-- Messages -->
    @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-sm text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-sm text-red-800">{{ session('error') }}</p>
        </div>
    @endif

    <!-- Liste des utilisateurs -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="font-display text-lg font-semibold text-anthracite">Gestion des utilisateurs</h3>
            <a href="{{ route('users.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-500 text-white text-sm font-medium rounded-lg hover:bg-primary-600 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Nouvel utilisateur
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Nom</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Email</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Role</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Cree le</th>
                        <th class="text-right py-3 px-4 text-sm font-medium text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center mr-3">
                                        <span class="text-sm font-medium text-primary-600">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $user->name }}</span>
                                    @if($user->id === auth()->id())
                                        <span class="ml-2 text-xs text-gray-400">(vous)</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-600">{{ $user->email }}</td>
                            <td class="py-3 px-4">
                                @if($user->role === 'admin')
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">Admin</span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">Reception</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-500">{{ $user->created_at->format('d/m/Y') }}</td>
                            <td class="py-3 px-4">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('users.edit', $user) }}" class="p-2 text-gray-400 hover:text-primary-600 transition-colors" title="Modifier">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline" onsubmit="return confirm('Supprimer cet utilisateur ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition-colors" title="Supprimer">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-500">Aucun utilisateur</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
