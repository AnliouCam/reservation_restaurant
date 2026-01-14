<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ZoneController extends Controller
{
    /**
     * Affiche la liste des zones
     */
    public function index(): View
    {
        $zones = Zone::withCount('tables')->orderBy('nom')->get();

        return view('zones.index', compact('zones'));
    }

    /**
     * Affiche le formulaire de création
     */
    public function create(): View
    {
        return view('zones.create');
    }

    /**
     * Enregistre une nouvelle zone
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:zones,nom',
            'description' => 'nullable|string|max:1000',
        ]);

        Zone::create($validated);

        return redirect()->route('zones.index')
            ->with('success', 'Zone créée avec succès.');
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit(Zone $zone): View
    {
        return view('zones.edit', compact('zone'));
    }

    /**
     * Met à jour une zone
     */
    public function update(Request $request, Zone $zone): RedirectResponse
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255', Rule::unique('zones', 'nom')->ignore($zone->id)],
            'description' => 'nullable|string|max:1000',
        ]);

        $zone->update($validated);

        return redirect()->route('zones.index')
            ->with('success', 'Zone mise à jour avec succès.');
    }

    /**
     * Supprime une zone
     */
    public function destroy(Zone $zone): RedirectResponse
    {
        if ($zone->tables()->count() > 0) {
            return redirect()->route('zones.index')
                ->with('error', 'Impossible de supprimer cette zone car elle contient des tables.');
        }

        $zone->delete();

        return redirect()->route('zones.index')
            ->with('success', 'Zone supprimée avec succès.');
    }
}
