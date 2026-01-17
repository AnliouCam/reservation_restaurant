<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Affiche la liste des utilisateurs
     */
    public function index(): View
    {
        $users = User::orderBy('name')->get();

        return view('users.index', compact('users'));
    }

    /**
     * Affiche le formulaire de creation
     */
    public function create(): View
    {
        return view('users.create');
    }

    /**
     * Enregistre un nouvel utilisateur
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'role' => ['required', Rule::in(['admin', 'reception'])],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        $user->password = Hash::make($validated['password']);
        $user->save();

        return redirect()->route('users.index')
            ->with('success', 'Utilisateur cree avec succes.');
    }

    /**
     * Affiche le formulaire d'edition
     */
    public function edit(User $user): View
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Met a jour un utilisateur
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['admin', 'reception'])],
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ]);

        // Empecher de modifier son propre role
        if ($user->id === auth()->id() && $validated['role'] !== $user->role) {
            return redirect()->route('users.edit', $user)
                ->with('error', 'Vous ne pouvez pas modifier votre propre role.');
        }

        // Empecher de retirer le role admin au dernier admin
        if ($user->role === 'admin' && $validated['role'] !== 'admin' && User::where('role', 'admin')->count() <= 1) {
            return redirect()->route('users.edit', $user)
                ->with('error', 'Impossible de retirer le role admin au dernier administrateur.');
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('users.index')
            ->with('success', 'Utilisateur modifie avec succes.');
    }

    /**
     * Supprime un utilisateur
     */
    public function destroy(User $user): RedirectResponse
    {
        // Empecher la suppression de son propre compte
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        // Empecher la suppression du dernier admin
        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            return redirect()->route('users.index')
                ->with('error', 'Impossible de supprimer le dernier administrateur.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Utilisateur supprime avec succes.');
    }
}
