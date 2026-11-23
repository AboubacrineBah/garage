<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\OrdreReparation;
use App\Models\PieceDetachee;
use App\Models\Vehicule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tableau de bord principal (mobile-first).
     */
    public function index(Request $request): View
    {
        // Tous les utilisateurs authentifiés voient le dashboard,
        // mais les KPI financiers sensibles sont réservés aux rôles de direction.
        $user = $request->user();
        $peutVoirFinancier = $user && (
            $user->hasRole('gerant')
            || $user->hasRole('comptable')
            || $user->hasRole('receptionniste')
            || $user->hasRole('chef_atelier')
        );

        $aujourdhui = now()->toDateString();

        // Scope mécanicien pour les KPI opérationnels
        $estMecanicienSeul = $user && $user->hasRole('mecanicien')
            && ! $user->hasRole('gerant')
            && ! $user->hasRole('chef_atelier')
            && ! $user->hasRole('receptionniste')
            && ! $user->hasRole('comptable');

        $scopeMecanicien = function ($q) use ($user) {
            $q->whereJsonContains('mecaniciens_ids', $user->id);
        };

        // KPI du jour
        $ordresEnCours = OrdreReparation::whereIn('statut', ['accepte', 'en_cours'])
            ->when($estMecanicienSeul, $scopeMecanicien)
            ->count();

        $ordresDuJour = OrdreReparation::whereDate('date_entree', $aujourdhui)
            ->when($estMecanicienSeul, $scopeMecanicien)
            ->count();

        $caDuJour = $peutVoirFinancier
            ? OrdreReparation::whereDate('date_entree', $aujourdhui)
                ->whereIn('statut', ['termine', 'facture', 'livre'])
                ->sum('montant_ttc')
            : null;

        $stockCritique = PieceDetachee::where('is_active', true)
            ->whereColumn('stock_actuel', '<=', 'stock_minimum')
            ->count();

        $creditsClients = $peutVoirFinancier
            ? Client::where('solde_credit', '>', 0)->sum('solde_credit')
            : null;

        // Derniers ordres — mécanicien : uniquement ceux qui lui sont assignés
        $derniersOrdresQuery = OrdreReparation::with(['client', 'vehicule'])->latest();

        if ($user && $user->hasRole('mecanicien')
            && ! $user->hasRole('gerant')
            && ! $user->hasRole('chef_atelier')
            && ! $user->hasRole('receptionniste')
            && ! $user->hasRole('comptable')
        ) {
            $derniersOrdresQuery->whereJsonContains('mecaniciens_ids', $user->id);
        }

        $derniersOrdres = $derniersOrdresQuery->take(8)->get();

        // Répartition par statut (pour graphiques simples)
        $repartitionStatuts = OrdreReparation::select('statut', DB::raw('count(*) as total'))
            ->groupBy('statut')
            ->pluck('total', 'statut');

        // Données pour graphiques (couleurs & stock)
        $repartitionCouleurs = Vehicule::select('couleur', DB::raw('count(*) as total'))
            ->whereNotNull('couleur')
            ->where('couleur', '!=', '')
            ->groupBy('couleur')
            ->orderByDesc('total')
            ->pluck('total', 'couleur');

        $stockParCategorie = PieceDetachee::select('categorie', DB::raw('count(*) as total'), DB::raw('sum(stock_actuel) as quantite'))
            ->where('is_active', true)
            ->groupBy('categorie')
            ->orderBy('categorie')
            ->get();

        $stockCritiqueVsOk = [
            'critique' => $stockCritique,
            'ok'       => PieceDetachee::where('is_active', true)
                ->whereColumn('stock_actuel', '>', 'stock_minimum')
                ->count(),
        ];

        return view('dashboard.index', compact(
            'ordresEnCours',
            'ordresDuJour',
            'caDuJour',
            'stockCritique',
            'creditsClients',
            'derniersOrdres',
            'repartitionStatuts',
            'peutVoirFinancier',
            'repartitionCouleurs',
            'stockParCategorie',
            'stockCritiqueVsOk'
        ));
    }
}
