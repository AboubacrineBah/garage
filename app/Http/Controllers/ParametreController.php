<?php

namespace App\Http\Controllers;

use App\Models\Parametre;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParametreController extends Controller
{
    /**
     * Affiche les paramètres par groupe.
     * Réservé au gérant (et éventuellement comptable pour facturation).
     */
    public function index(): View
    {
        $this->authorizeGerant();

        $groupes = [
            'atelier' => Parametre::groupe('atelier'),
            'facturation' => Parametre::groupe('facturation'),
            'notifications' => Parametre::groupe('notifications'),
        ];

        return view('parametres.index', compact('groupes'));
    }

    /**
     * Met à jour les paramètres soumis.
     * - Seules les clés déjà existantes en base sont acceptées (pas de création arbitraire).
     * - Validation basique selon le type du paramètre.
     */
    public function update(Request $request): RedirectResponse
    {
        $this->authorizeGerant();

        $data = $request->input('parametres', []);

        if (! is_array($data)) {
            return back()->with('error', 'Données de paramètres invalides.');
        }

        $clesAutorisees = Parametre::pluck('cle')->all();

        foreach ($data as $cle => $valeur) {
            // Refuser toute clé inconnue (évite injection de nouvelles clés)
            if (! is_string($cle) || ! in_array($cle, $clesAutorisees, true)) {
                continue;
            }

            $param = Parametre::where('cle', $cle)->first();
            if (! $param) {
                continue;
            }

            // Normalisation / validation légère selon le type
            $valeurNormalisee = $this->normaliserValeur($valeur, $param->type);

            Parametre::set($cle, $valeurNormalisee, [
                'type' => $param->type,
                'groupe' => $param->groupe,
                'libelle' => $param->libelle,
                'description' => $param->description,
            ]);
        }

        return back()->with('success', 'Paramètres enregistrés.');
    }

    /**
     * Seul le gérant peut gérer les paramètres atelier.
     */
    protected function authorizeGerant(): void
    {
        $user = auth()->user();

        if (! $user || ! $user->hasRole('gerant')) {
            abort(403, 'Accès réservé au gérant.');
        }
    }

    /**
     * Normalise la valeur selon le type stocké.
     */
    protected function normaliserValeur(mixed $valeur, string $type): mixed
    {
        return match ($type) {
            'int', 'integer' => (int) $valeur,
            'bool', 'boolean' => filter_var($valeur, FILTER_VALIDATE_BOOLEAN),
            'json' => is_string($valeur) ? $valeur : json_encode($valeur),
            default => is_scalar($valeur) ? (string) $valeur : '',
        };
    }
}
