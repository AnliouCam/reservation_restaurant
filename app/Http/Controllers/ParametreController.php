<?php

namespace App\Http\Controllers;

use App\Models\Parametre;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParametreController extends Controller
{
    /**
     * Affiche la page des parametres generaux
     */
    public function index(): View
    {
        // Initialise les parametres par defaut si necessaire
        Parametre::initDefaults();

        $parametres = [
            'nom_restaurant' => Parametre::get('nom_restaurant', 'Mon Restaurant'),
            'telephone' => Parametre::get('telephone', ''),
            'adresse' => Parametre::get('adresse', ''),
            'email' => Parametre::get('email', ''),
            'duree_reservation' => Parametre::get('duree_reservation', 120),
        ];

        return view('parametres.index', compact('parametres'));
    }

    /**
     * Sauvegarde les parametres generaux
     */
    public function updateGeneral(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nom_restaurant' => 'required|string|max:100',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:100',
            'duree_reservation' => 'required|integer|min:30|max:300',
        ]);

        Parametre::set('nom_restaurant', $validated['nom_restaurant'], 'string', 'Nom du restaurant');
        Parametre::set('telephone', $validated['telephone'] ?? '', 'string', 'Telephone du restaurant');
        Parametre::set('adresse', $validated['adresse'] ?? '', 'string', 'Adresse du restaurant');
        Parametre::set('email', $validated['email'] ?? '', 'string', 'Email de contact');
        Parametre::set('duree_reservation', $validated['duree_reservation'], 'integer', 'Duree moyenne d\'une reservation en minutes');

        return redirect()->route('parametres.index')
            ->with('success', 'Parametres enregistres avec succes.');
    }

    /**
     * Affiche la page des horaires
     */
    public function horaires(): View
    {
        Parametre::initDefaults();

        $horaires = Parametre::get('horaires', []);

        $jours = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'];

        return view('parametres.horaires', compact('horaires', 'jours'));
    }

    /**
     * Sauvegarde les horaires
     */
    public function updateHoraires(Request $request): RedirectResponse
    {
        $jours = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'];

        // Validation du format des horaires (ex: 12:00-14:00)
        $rules = [];
        foreach ($jours as $jour) {
            $rules["horaires.{$jour}.midi"] = 'nullable|regex:/^(\d{2}:\d{2}-\d{2}:\d{2})?$/';
            $rules["horaires.{$jour}.soir"] = 'nullable|regex:/^(\d{2}:\d{2}-\d{2}:\d{2})?$/';
        }
        $request->validate($rules, [
            'regex' => 'Le format doit etre HH:MM-HH:MM (ex: 12:00-14:00)',
        ]);

        $horaires = [];
        foreach ($jours as $jour) {
            $horaires[$jour] = [
                'ouvert' => $request->boolean("horaires.{$jour}.ouvert"),
                'midi' => $request->input("horaires.{$jour}.midi", ''),
                'soir' => $request->input("horaires.{$jour}.soir", ''),
            ];
        }

        Parametre::set('horaires', $horaires, 'json', 'Horaires d\'ouverture');

        return redirect()->route('parametres.horaires')
            ->with('success', 'Horaires enregistres avec succes.');
    }
}
