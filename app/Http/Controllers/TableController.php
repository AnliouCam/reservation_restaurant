<?php

namespace App\Http\Controllers;

use App\Models\Table;
use App\Models\Zone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TableController extends Controller
{
    /**
     * Statuts valides pour une table
     */
    private const STATUTS = ['disponible', 'reservee', 'occupee'];

    /**
     * Affiche la liste des tables avec filtres
     */
    public function index(Request $request): View
    {
        // Valider les filtres
        $request->validate([
            'zone_id' => 'nullable|integer|exists:zones,id',
            'statut' => ['nullable', Rule::in(self::STATUTS)],
        ]);

        $query = Table::with('zone');

        if ($request->filled('zone_id')) {
            $query->where('zone_id', $request->zone_id);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $tables = $query->orderBy('numero')->get();
        $zones = Zone::orderBy('nom')->get();

        // Calculer les stats
        $stats = [
            'disponible' => $tables->where('statut', 'disponible')->count(),
            'reservee' => $tables->where('statut', 'reservee')->count(),
            'occupee' => $tables->where('statut', 'occupee')->count(),
        ];

        return view('tables.index', compact('tables', 'zones', 'stats'));
    }

    /**
     * Affiche le formulaire de création
     */
    public function create(): View|RedirectResponse
    {
        $zones = Zone::orderBy('nom')->get();

        if ($zones->isEmpty()) {
            return redirect()->route('tables.index')
                ->with('error', 'Vous devez d\'abord créer une zone avant d\'ajouter des tables.');
        }

        return view('tables.create', compact('zones'));
    }

    /**
     * Enregistre une nouvelle table
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'numero' => 'required|string|max:50',
            'capacite' => 'required|integer|min:1|max:20',
            'zone_id' => 'required|exists:zones,id',
            'statut' => ['required', Rule::in(self::STATUTS)],
        ]);

        if ($this->numeroExistsInZone($validated['numero'], $validated['zone_id'])) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['numero' => 'Ce numéro de table existe déjà dans cette zone.']);
        }

        Table::create($validated);

        return redirect()->route('tables.index')
            ->with('success', 'Table créée avec succès.');
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit(Table $table): View
    {
        $zones = Zone::orderBy('nom')->get();

        return view('tables.edit', compact('table', 'zones'));
    }

    /**
     * Met à jour une table
     */
    public function update(Request $request, Table $table): RedirectResponse
    {
        $validated = $request->validate([
            'numero' => 'required|string|max:50',
            'capacite' => 'required|integer|min:1|max:20',
            'zone_id' => 'required|exists:zones,id',
            'statut' => ['required', Rule::in(self::STATUTS)],
        ]);

        if ($this->numeroExistsInZone($validated['numero'], $validated['zone_id'], $table->id)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['numero' => 'Ce numéro de table existe déjà dans cette zone.']);
        }

        $table->update($validated);

        return redirect()->route('tables.index')
            ->with('success', 'Table mise à jour avec succès.');
    }

    /**
     * Supprime une table
     */
    public function destroy(Table $table): RedirectResponse
    {
        // Note: Quand le module Reservations sera implementé, ajouter :
        // if ($table->reservations()->where('date_reservation', '>=', now()->toDateString())->exists()) {
        //     return redirect()->route('tables.index')
        //         ->with('error', 'Impossible de supprimer cette table car elle a des réservations futures.');
        // }

        $table->delete();

        return redirect()->route('tables.index')
            ->with('success', 'Table supprimée avec succès.');
    }

    /**
     * Change le statut d'une table (action rapide)
     */
    public function updateStatut(Request $request, Table $table): RedirectResponse
    {
        $validated = $request->validate([
            'statut' => ['required', Rule::in(self::STATUTS)],
        ]);

        $table->update(['statut' => $validated['statut']]);

        $messages = [
            'disponible' => 'Table marquée comme disponible.',
            'reservee' => 'Table marquée comme réservée.',
            'occupee' => 'Table marquée comme occupée.',
        ];

        return redirect()->back()
            ->with('success', $messages[$validated['statut']]);
    }

    /**
     * Vérifie si un numéro de table existe déjà dans une zone
     */
    private function numeroExistsInZone(string $numero, int $zoneId, ?int $excludeId = null): bool
    {
        $query = Table::where('numero', $numero)->where('zone_id', $zoneId);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
