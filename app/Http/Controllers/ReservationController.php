<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Table;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ReservationController extends Controller
{
    /**
     * Affiche la liste des reservations avec filtres
     */
    public function index(Request $request): View
    {
        $request->validate([
            'date' => 'nullable|date',
            'statut' => ['nullable', Rule::in(array_keys(Reservation::STATUTS))],
            'recherche' => 'nullable|string|max:100',
        ]);

        $query = Reservation::with(['table.zone', 'user']);

        // Filtre par date (optionnel)
        if ($request->filled('date')) {
            $query->whereDate('date_reservation', $request->date);
        }

        // Filtre par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Recherche par nom ou telephone client (avec echappement des caracteres LIKE)
        if ($request->filled('recherche')) {
            $search = str_replace(['%', '_'], ['\%', '\_'], $request->recherche);
            $query->where(function ($q) use ($search) {
                $q->where('client_nom', 'like', "%{$search}%")
                  ->orWhere('client_telephone', 'like', "%{$search}%");
            });
        }

        $reservations = $query->orderBy('date_reservation')->orderBy('heure_reservation')->get();

        // Stats du jour
        $stats = [
            'total' => $reservations->count(),
            'en_attente' => $reservations->where('statut', 'en_attente')->count(),
            'confirmee' => $reservations->where('statut', 'confirmee')->count(),
            'personnes' => $reservations->whereIn('statut', ['en_attente', 'confirmee'])->sum('nombre_personnes'),
        ];

        return view('reservations.index', compact('reservations', 'stats'));
    }

    /**
     * Affiche le formulaire de creation
     */
    public function create(Request $request): View|RedirectResponse
    {
        $tables = Table::with('zone')
            ->where('statut', 'disponible')
            ->orderBy('numero')
            ->get();

        // Si une date est passee en parametre, pre-remplir
        $selectedDate = $request->get('date', today()->format('Y-m-d'));

        return view('reservations.create', compact('tables', 'selectedDate'));
    }

    /**
     * Enregistre une nouvelle reservation
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_nom' => 'required|string|max:255',
            'client_telephone' => 'required|string|max:20',
            'nombre_personnes' => 'required|integer|min:1|max:20',
            'date_reservation' => 'required|date|after_or_equal:today',
            'heure_reservation' => 'required|date_format:H:i',
            'table_id' => 'required|exists:tables,id',
            'commentaire' => 'nullable|string|max:1000',
        ]);

        // Verifier que la table est disponible pour ce creneau (plage de 2h)
        if ($this->hasConflict($validated['table_id'], $validated['date_reservation'], $validated['heure_reservation'])) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['table_id' => 'Cette table est deja reservee pour ce creneau horaire.']);
        }

        // Verifier la capacite de la table
        $table = Table::find($validated['table_id']);
        if ($validated['nombre_personnes'] > $table->capacite) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['nombre_personnes' => "Cette table a une capacite maximale de {$table->capacite} personnes."]);
        }

        $validated['statut'] = 'en_attente';
        $validated['user_id'] = auth()->id();

        Reservation::create($validated);

        // Mettre la table en statut reservee si la reservation est pour aujourd'hui
        if ($validated['date_reservation'] === today()->format('Y-m-d')) {
            $table->update(['statut' => 'reservee']);
        }

        return redirect()->route('reservations.index')
            ->with('success', 'Reservation creee avec succes.');
    }

    /**
     * Affiche le formulaire d'edition
     */
    public function edit(Reservation $reservation): View
    {
        $tables = Table::with('zone')->orderBy('numero')->get();

        return view('reservations.edit', compact('reservation', 'tables'));
    }

    /**
     * Met a jour une reservation
     */
    public function update(Request $request, Reservation $reservation): RedirectResponse
    {
        $validated = $request->validate([
            'client_nom' => 'required|string|max:255',
            'client_telephone' => 'required|string|max:20',
            'nombre_personnes' => 'required|integer|min:1|max:20',
            'date_reservation' => 'required|date',
            'heure_reservation' => 'required|date_format:H:i',
            'table_id' => 'required|exists:tables,id',
            'statut' => ['required', Rule::in(array_keys(Reservation::STATUTS))],
            'commentaire' => 'nullable|string|max:1000',
        ]);

        // Verifier conflit de table (sauf pour cette reservation)
        if ($validated['table_id'] != $reservation->table_id ||
            $validated['date_reservation'] != $reservation->date_reservation->format('Y-m-d') ||
            $validated['heure_reservation'] != Carbon::parse($reservation->heure_reservation)->format('H:i')) {

            if ($this->hasConflict($validated['table_id'], $validated['date_reservation'], $validated['heure_reservation'], $reservation->id)) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['table_id' => 'Cette table est deja reservee pour ce creneau horaire.']);
            }
        }

        $reservation->update($validated);

        return redirect()->route('reservations.index')
            ->with('success', 'Reservation mise a jour avec succes.');
    }

    /**
     * Supprime une reservation
     */
    public function destroy(Reservation $reservation): RedirectResponse
    {
        // Liberer la table si elle etait reservee pour cette reservation
        if ($reservation->statut !== 'annulee' && $reservation->date_reservation->isToday()) {
            $reservation->table->update(['statut' => 'disponible']);
        }

        $reservation->delete();

        return redirect()->route('reservations.index')
            ->with('success', 'Reservation supprimee avec succes.');
    }

    /**
     * Change le statut d'une reservation (action rapide)
     */
    public function updateStatut(Request $request, Reservation $reservation): RedirectResponse
    {
        $validated = $request->validate([
            'statut' => ['required', Rule::in(array_keys(Reservation::STATUTS))],
        ]);

        $oldStatut = $reservation->statut;
        $newStatut = $validated['statut'];
        $reservation->update(['statut' => $newStatut]);

        // Gerer le statut de la table selon le changement
        if ($reservation->date_reservation->isToday()) {
            if ($newStatut === 'confirmee' && $oldStatut === 'en_attente') {
                // Client arrive : table devient occupee
                $reservation->table->update(['statut' => 'occupee']);
            } elseif ($newStatut === 'terminee') {
                // Reservation terminee : table redevient disponible
                $reservation->table->update(['statut' => 'disponible']);
            } elseif ($newStatut === 'annulee') {
                // Reservation annulee : table redevient disponible
                $reservation->table->update(['statut' => 'disponible']);
            }
        }

        $messages = [
            'en_attente' => 'Reservation mise en attente.',
            'confirmee' => 'Client arrive - table occupee.',
            'terminee' => 'Reservation terminee - table liberee.',
            'annulee' => 'Reservation annulee - table liberee.',
        ];

        return redirect()->back()
            ->with('success', $messages[$newStatut]);
    }

    /**
     * Recherche de reservations (pour autocompletion)
     */
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100',
        ]);

        $search = str_replace(['%', '_'], ['\%', '\_'], $request->q);

        $reservations = Reservation::with(['table.zone'])
            ->where(function ($query) use ($search) {
                $query->where('client_nom', 'like', "%{$search}%")
                      ->orWhere('client_telephone', 'like', "%{$search}%");
            })
            ->futures()
            ->actives()
            ->orderBy('date_reservation')
            ->limit(10)
            ->get();

        return response()->json($reservations);
    }

    /**
     * Verifie s'il y a un conflit de reservation pour une table/date/heure
     * (plage de 2 heures autour de l'heure demandee)
     */
    private function hasConflict(int $tableId, string $date, string $heure, ?int $excludeId = null): bool
    {
        $requestedTime = Carbon::parse($heure);

        $query = Reservation::where('table_id', $tableId)
            ->whereDate('date_reservation', $date)
            ->whereIn('statut', ['en_attente', 'confirmee']);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $reservations = $query->get();

        foreach ($reservations as $reservation) {
            $existingTime = Carbon::parse($reservation->heure_reservation);
            $diffInHours = abs($requestedTime->diffInMinutes($existingTime)) / 60;

            if ($diffInHours < 2) {
                return true;
            }
        }

        return false;
    }
}
