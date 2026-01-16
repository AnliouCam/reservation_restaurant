<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Table;
use App\Models\Zone;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Stats des reservations
        $reservationsJour = Reservation::whereDate('date_reservation', today())
            ->whereIn('statut', ['en_attente', 'confirmee'])
            ->count();

        $reservationsSemaine = Reservation::whereBetween('date_reservation', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])
            ->whereIn('statut', ['en_attente', 'confirmee'])
            ->count();

        $reservationsMois = Reservation::whereMonth('date_reservation', now()->month)
            ->whereYear('date_reservation', now()->year)
            ->whereIn('statut', ['en_attente', 'confirmee'])
            ->count();

        // Stats des tables
        $tablesDisponibles = Table::where('statut', 'disponible')->count();
        $tablesOccupees = Table::where('statut', 'occupee')->count();
        $tablesReservees = Table::where('statut', 'reservee')->count();
        $totalTables = Table::count();

        // Taux d'occupation
        $tauxOccupation = $totalTables > 0
            ? round((($tablesOccupees + $tablesReservees) / $totalTables) * 100)
            : 0;

        // Prochaines reservations du jour
        $prochainesReservations = Reservation::with(['table.zone'])
            ->whereDate('date_reservation', today())
            ->whereIn('statut', ['en_attente', 'confirmee'])
            ->orderBy('heure_reservation')
            ->limit(5)
            ->get();

        // Couverts prevus aujourd'hui
        $couvertsPrevus = Reservation::whereDate('date_reservation', today())
            ->whereIn('statut', ['en_attente', 'confirmee'])
            ->sum('nombre_personnes');

        // Stats par zone
        $statsParZone = Zone::withCount(['tables', 'tables as tables_disponibles_count' => function ($query) {
            $query->where('statut', 'disponible');
        }])->get();

        return view('dashboard', compact(
            'reservationsJour',
            'reservationsSemaine',
            'reservationsMois',
            'tablesDisponibles',
            'tablesOccupees',
            'tablesReservees',
            'totalTables',
            'tauxOccupation',
            'prochainesReservations',
            'couvertsPrevus',
            'statsParZone'
        ));
    }
}
