<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrdreReparationRequest;
use App\Http\Requests\UpdateOrdreStatutRequest;
use App\Models\Assurance;
use App\Models\Client;
use App\Models\Devise;
use App\Models\LigneOrdre;
use App\Models\MouvementStock;
use App\Models\OrdreReparation;
use App\Models\PieceDetachee;
use App\Models\Sinistre;
use App\Models\User;
use App\Models\Vehicule;
use App\Services\NotificationService;
use App\Services\PdfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrdreReparationController extends Controller
{
    /** Statuts qui déclenchent le déstockage des pièces. */
    protected array $statutsDestockage = ['accepte', 'en_cours', 'termine', 'facture', 'livre'];

    public function __construct(
        protected NotificationService $notifier,
        protected PdfService $pdf
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', OrdreReparation::class);

        $query = OrdreReparation::with(['client', 'vehicule'])->latest();

        // IDOR / fuite d'info : un mécanicien ne doit voir que les OR qui lui sont assignés
        $user = $request->user();
        if ($user && $user->hasRole('mecanicien')
            && ! $user->hasRole('gerant')
            && ! $user->hasRole('chef_atelier')
            && ! $user->hasRole('receptionniste')
            && ! $user->hasRole('comptable')
        ) {
            $query->whereJsonContains('mecaniciens_ids', $user->id);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('search')) {
            // Limitation de longueur pour éviter les abus de recherche (DoS léger / perf)
            $search = mb_substr(trim((string) $request->search), 0, 80);
            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('numero', 'like', "%{$search}%")
                        ->orWhereHas('client', fn ($c) => $c->where('nom', 'like', "%{$search}%")
                            ->orWhere('telephone', 'like', "%{$search}%"))
                        ->orWhereHas('vehicule', fn ($v) => $v->where('immatriculation', 'like', "%{$search}%"));
                });
            }
        }

        $ordres = $query->paginate(15)->withQueryString();

        return view('ordres.index', compact('ordres'));
    }

    public function create(): View
    {
        $this->authorize('create', OrdreReparation::class);

        $clients = Client::orderBy('nom')->limit(50)->get(['id', 'nom', 'prenom', 'telephone', 'type']);
        $mecaniciens = User::whereHas('role', fn ($q) => $q->whereIn('name', ['mecanicien', 'chef_atelier']))
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);
        $pieces = PieceDetachee::where('is_active', true)
            ->orderBy('designation')
            ->get(['id', 'reference', 'designation', 'prix_vente', 'stock_actuel', 'unite']);

        return view('ordres.create', compact('clients', 'mecaniciens', 'pieces'));
    }

    public function store(StoreOrdreReparationRequest $request): RedirectResponse
    {
        $this->authorize('create', OrdreReparation::class);

        $data = $request->validated();

        $ordre = DB::transaction(function () use ($data) {
            if (! empty($data['nouveau_client']) || empty($data['client_id'])) {
                $client = Client::create([
                    'type' => $data['client_type'] ?? 'particulier',
                    'nom' => $data['client_nom'],
                    'prenom' => $data['client_prenom'] ?? null,
                    'telephone' => $data['client_telephone'],
                    'created_by' => auth()->id(),
                ]);
            } else {
                $client = Client::findOrFail($data['client_id']);
            }

            if (! empty($data['nouveau_vehicule']) || empty($data['vehicule_id'])) {
                $vehicule = Vehicule::create([
                    'client_id' => $client->id,
                    'immatriculation' => strtoupper($data['immatriculation']),
                    'marque' => $data['marque'],
                    'modele' => $data['modele'],
                    'annee' => $data['annee'] ?? null,
                    'kilometrage_actuel' => $data['kilometrage_entree'],
                    'type_carburant' => $data['type_carburant'] ?? 'essence',
                ]);
            } else {
                $vehicule = Vehicule::findOrFail($data['vehicule_id']);
                $vehicule->update(['kilometrage_actuel' => $data['kilometrage_entree']]);
            }

            $ordre = OrdreReparation::create([
                'numero' => OrdreReparation::genererNumero(),
                'client_id' => $client->id,
                'vehicule_id' => $vehicule->id,
                'statut' => 'devis',
                'date_entree' => now(),
                'date_sortie_prevue' => $data['date_sortie_prevue'] ?? null,
                'kilometrage_entree' => $data['kilometrage_entree'],
                'description_travaux' => $data['description_travaux'],
                'mecaniciens_ids' => $data['mecaniciens_ids'] ?? [],
                'created_by' => auth()->id(),
            ]);

            if (! empty($data['lignes'])) {
                foreach ($data['lignes'] as $index => $ligneData) {
                    $pieceId = $ligneData['piece_id'] ?? null;
                    $designation = $ligneData['designation'];
                    $prix = $ligneData['prix_unitaire_ht'];

                    // Si une pièce du catalogue est sélectionnée, préremplir désignation / prix
                    if ($pieceId) {
                        $piece = PieceDetachee::find($pieceId);
                        if ($piece) {
                            $designation = $designation ?: $piece->designation;
                            $prix = $prix ?: $piece->prix_vente;
                        }
                    }

                    $ligne = new LigneOrdre([
                        'ordre_id' => $ordre->id,
                        'type_ligne' => $ligneData['type_ligne'],
                        'piece_id' => $pieceId,
                        'designation' => $designation,
                        'quantite' => $ligneData['quantite'],
                        'prix_unitaire_ht' => $prix,
                        'remise_pct' => $ligneData['remise_pct'] ?? 0,
                        'ordre_affichage' => $index,
                    ]);
                    $ligne->calculerMontant();
                    $ligne->save();
                }
                $ordre->recalculerMontants();
            }

            return $ordre;
        });

        try {
            $this->notifier->notifierDevisPret($ordre->load('client', 'vehicule'), 'whatsapp');
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()
            ->route('ordres.show', $ordre)
            ->with('success', "Ordre {$ordre->numero} créé avec succès.");
    }

    public function show(OrdreReparation $ordre): View
    {
        $this->authorize('view', $ordre);

        $ordre->load(['client', 'vehicule', 'lignes.piece', 'createdBy', 'notifications']);

        return view('ordres.show', compact('ordre'));
    }

    public function updateStatut(UpdateOrdreStatutRequest $request, OrdreReparation $ordre): RedirectResponse
    {
        $this->authorize('update', $ordre);

        $data = $request->validated();
        $ancienStatut = $ordre->statut;
        $nouveauStatut = $data['statut'];

        // Matrice de transitions autorisées (évite les sauts incohérents)
        if (! $this->transitionStatutAutorisee($ancienStatut, $nouveauStatut)) {
            return back()->with(
                'error',
                "Transition de statut interdite : « {$ancienStatut} » → « {$nouveauStatut} »."
            );
        }

        try {
            DB::transaction(function () use ($ordre, $data, $ancienStatut, $nouveauStatut) {
                $ordre->fill($data);

                if (in_array($nouveauStatut, ['termine', 'facture', 'livre'], true) && ! $ordre->date_sortie_reelle) {
                    $ordre->date_sortie_reelle = now();
                    $ordre->closed_by = auth()->id();
                }

                if (isset($data['acompte'])) {
                    $ordre->reste_a_payer = max(0, (float) $ordre->montant_ttc - (float) $data['acompte']);
                    $ordre->acompte = $data['acompte'];
                }

                $ordre->save();

                // Déstockage au premier passage vers un statut "consommateur"
                $dejaDestocke = in_array($ancienStatut, $this->statutsDestockage, true);
                $doitDestocker = in_array($nouveauStatut, $this->statutsDestockage, true);

                if ($doitDestocker && ! $dejaDestocke) {
                    $this->destockerPieces($ordre);
                }
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        if ($nouveauStatut === 'termine' && $ancienStatut !== 'termine') {
            try {
                $this->notifier->notifierVehiculeTermine($ordre->load('client', 'vehicule'), 'whatsapp');
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return back()->with('success', 'Statut mis à jour.');
    }

    /**
     * Transitions de statut autorisées (workflow métier garage).
     */
    protected function transitionStatutAutorisee(string $from, string $to): bool
    {
        if ($from === $to) {
            return true;
        }

        $transitions = [
            'brouillon' => ['devis', 'annule'],
            'devis' => ['accepte', 'annule', 'brouillon'],
            'accepte' => ['en_cours', 'annule'],
            'en_cours' => ['termine', 'annule'],
            'termine' => ['facture', 'livre'],
            'facture' => ['livre'],
            'livre' => [],          // terminal
            'annule' => [],          // terminal
        ];

        return in_array($to, $transitions[$from] ?? [], true);
    }

    public function updateFacturation(Request $request, OrdreReparation $ordre): RedirectResponse
    {
        $this->authorize('update', $ordre);

        $data = $request->validate([
            'devise_id' => ['nullable', 'exists:devises,id'],
            'acompte' => ['nullable', 'numeric', 'min:0'],
            'mode_paiement' => ['nullable', 'string', 'max:40'],
            'assurance_nom' => ['nullable', 'string', 'max:120'],
            'numero_sinistre' => ['nullable', 'string', 'max:80'],
            'franchise' => ['nullable', 'numeric', 'min:0'],
            'part_assurance' => ['nullable', 'numeric', 'min:0'],
            'part_client' => ['nullable', 'numeric', 'min:0'],
        ]);

        if (isset($data['devise_id']) && $data['devise_id']) {
            $ordre->devise_id = $data['devise_id'];
            $devise = Devise::find($data['devise_id']);
            if ($devise) {
                $ordre->taux_applique = $devise->taux_vers_xof;
            }
        }

        if (array_key_exists('acompte', $data)) {
            $ordre->acompte = $data['acompte'] ?? 0;
        }
        if (array_key_exists('mode_paiement', $data)) {
            $ordre->mode_paiement = $data['mode_paiement'];
        }
        foreach (['assurance_nom', 'numero_sinistre', 'franchise', 'part_assurance', 'part_client'] as $f) {
            if (array_key_exists($f, $data)) {
                $ordre->{$f} = $data[$f];
            }
        }

        $ordre->reste_a_payer = max(0, (float) $ordre->montant_ttc - (float) $ordre->acompte);
        $ordre->save();

        // Dossier sinistre / assurance (double payeur)
        if (! empty($data['assurance_nom']) || ! empty($data['numero_sinistre'])) {
            $assurance = null;
            if (! empty($data['assurance_nom'])) {
                $assurance = Assurance::firstOrCreate(
                    ['nom' => $data['assurance_nom']],
                    ['is_active' => true]
                );
            }

            $sinistre = Sinistre::firstOrNew(['ordre_id' => $ordre->id]);
            $sinistre->assurance_id = $assurance?->id;
            $sinistre->numero_sinistre = $data['numero_sinistre'] ?? $sinistre->numero_sinistre;
            $sinistre->franchise = $data['franchise'] ?? $sinistre->franchise ?? 0;
            $sinistre->part_assurance = $data['part_assurance'] ?? $sinistre->part_assurance ?? 0;
            $sinistre->part_client = $data['part_client'] ?? $sinistre->part_client ?? 0;
            $sinistre->statut = $sinistre->statut ?: 'ouvert';
            $sinistre->save();
        }

        return back()->with('success', 'Facturation / assurance mise à jour.');
    }

    public function destroy(OrdreReparation $ordre): RedirectResponse
    {
        $this->authorize('delete', $ordre);

        // Interdire la suppression soft des OR déjà facturés / livrés (piste d'audit)
        if (in_array($ordre->statut, ['facture', 'livre'], true)) {
            return back()->with(
                'error',
                'Impossible de supprimer un ordre facturé ou livré. Annulez-le si nécessaire via le statut.'
            );
        }

        $ordre->delete();

        return redirect()
            ->route('ordres.index')
            ->with('success', 'Ordre supprimé.');
    }

    public function pdfDevis(OrdreReparation $ordre)
    {
        $this->authorize('downloadPdf', $ordre);

        return $this->pdf->telecharger($ordre, 'devis');
    }

    public function pdfFacture(OrdreReparation $ordre)
    {
        $this->authorize('downloadPdf', $ordre);

        return $this->pdf->telecharger($ordre, 'facture');
    }

    public function pdfStream(OrdreReparation $ordre, string $type = 'devis')
    {
        $this->authorize('downloadPdf', $ordre);

        $type = in_array($type, ['devis', 'facture'], true) ? $type : 'devis';

        return $this->pdf->stream($ordre, $type);
    }

    /**
     * Décrémente le stock pour chaque ligne de type "piece" liée à une pièce catalogue.
     * Crée un MouvementStock et refuse si stock insuffisant.
     */
    protected function destockerPieces(OrdreReparation $ordre): void
    {
        $ordre->loadMissing('lignes');

        foreach ($ordre->lignes as $ligne) {
            if ($ligne->type_ligne !== 'piece' || ! $ligne->piece_id) {
                continue;
            }

            // Éviter un double déstockage pour le même ordre + pièce
            $deja = MouvementStock::where('ordre_id', $ordre->id)
                ->where('piece_id', $ligne->piece_id)
                ->where('type', 'sortie')
                ->exists();

            if ($deja) {
                continue;
            }

            $piece = PieceDetachee::lockForUpdate()->find($ligne->piece_id);
            if (! $piece) {
                continue;
            }

            $qty = (float) $ligne->quantite;

            if ($piece->stock_actuel < $qty) {
                throw new \RuntimeException(
                    "Stock insuffisant pour « {$piece->designation} » "
                    ."(disponible : {$piece->stock_actuel}, demandé : {$qty})."
                );
            }

            $piece->update(['stock_actuel' => $piece->stock_actuel - $qty]);

            MouvementStock::create([
                'piece_id' => $piece->id,
                'type' => 'sortie',
                'quantite' => $qty,
                'ordre_id' => $ordre->id,
                'user_id' => auth()->id(),
                'commentaire' => "Sortie OR {$ordre->numero}",
            ]);
        }
    }
}
