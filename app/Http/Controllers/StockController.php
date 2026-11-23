<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMouvementStockRequest;
use App\Http\Requests\StorePieceRequest;
use App\Models\MouvementStock;
use App\Models\PieceDetachee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockController extends Controller
{
    /**
     * Liste des pièces avec filtre stock critique.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', PieceDetachee::class);

        $query = PieceDetachee::query()->orderBy('designation');

        if ($request->filled('search')) {
            $search = mb_substr(trim((string) $request->search), 0, 80);
            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('reference', 'like', "%{$search}%")
                        ->orWhere('designation', 'like', "%{$search}%")
                        ->orWhere('categorie', 'like', "%{$search}%")
                        ->orWhere('marque', 'like', "%{$search}%");
                });
            }
        }

        if ($request->boolean('critique')) {
            $query->whereColumn('stock_actuel', '<=', 'stock_minimum');
        }

        if ($request->filled('categorie')) {
            $query->where('categorie', $request->categorie);
        }

        if ($request->has('actif')) {
            $query->where('is_active', $request->boolean('actif'));
        } else {
            $query->where('is_active', true);
        }

        $pieces = $query->paginate(20)->withQueryString();

        $categories = PieceDetachee::whereNotNull('categorie')
            ->distinct()
            ->orderBy('categorie')
            ->pluck('categorie');

        $nbCritiques = PieceDetachee::where('is_active', true)
            ->whereColumn('stock_actuel', '<=', 'stock_minimum')
            ->count();

        return view('stock.index', compact('pieces', 'categories', 'nbCritiques'));
    }

    /**
     * Formulaire d'ajout d'une pièce.
     */
    public function create(): View
    {
        $this->authorize('create', PieceDetachee::class);

        return view('stock.create');
    }

    /**
     * Enregistrement d'une nouvelle pièce.
     */
    public function store(StorePieceRequest $request): RedirectResponse
    {
        $this->authorize('create', PieceDetachee::class);

        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);
        $data['stock_actuel'] = $data['stock_actuel'] ?? 0;

        $piece = PieceDetachee::create($data);

        // Si stock initial > 0, créer un mouvement d'entrée
        if ($piece->stock_actuel > 0) {
            MouvementStock::create([
                'piece_id' => $piece->id,
                'type' => 'entree',
                'quantite' => $piece->stock_actuel,
                'user_id' => auth()->id(),
                'commentaire' => 'Stock initial à la création',
            ]);
        }

        return redirect()
            ->route('stock.show', $piece)
            ->with('success', 'Pièce créée avec succès.');
    }

    /**
     * Fiche détail d'une pièce + historique mouvements.
     */
    public function show(PieceDetachee $piece): View
    {
        $this->authorize('view', $piece);

        $mouvements = $piece->mouvements()
            ->with('user', 'ordre')
            ->latest()
            ->paginate(15);

        return view('stock.show', compact('piece', 'mouvements'));
    }

    /**
     * Formulaire d'édition.
     */
    public function edit(PieceDetachee $piece): View
    {
        $this->authorize('update', $piece);

        return view('stock.edit', compact('piece'));
    }

    /**
     * Mise à jour d'une pièce (sans toucher au stock_actuel directement).
     */
    public function update(StorePieceRequest $request, PieceDetachee $piece): RedirectResponse
    {
        $this->authorize('update', $piece);

        $data = $request->validated();
        // On n'autorise pas la modification directe du stock via ce formulaire
        unset($data['stock_actuel']);
        $data['is_active'] = $request->boolean('is_active', true);

        $piece->update($data);

        return redirect()
            ->route('stock.show', $piece)
            ->with('success', 'Pièce mise à jour.');
    }

    /**
     * Formulaire d'entrée / sortie / ajustement de stock.
     */
    public function mouvementForm(Request $request): View
    {
        $this->authorize('mouvement', PieceDetachee::class);

        $pieces = PieceDetachee::where('is_active', true)
            ->orderBy('designation')
            ->get(['id', 'reference', 'designation', 'stock_actuel', 'unite']);

        $type = $request->get('type', 'entree');
        $pieceId = $request->get('piece_id');

        return view('stock.mouvement', compact('pieces', 'type', 'pieceId'));
    }

    /**
     * Enregistrement d'un mouvement de stock (entrée, sortie, ajustement).
     */
    public function storeMouvement(StoreMouvementStockRequest $request): RedirectResponse
    {
        $this->authorize('mouvement', PieceDetachee::class);

        $data = $request->validated();
        $piece = PieceDetachee::findOrFail($data['piece_id']);

        try {
            DB::transaction(function () use ($data, $piece) {
                // Verrouillage pessimiste pour éviter les conditions de course sur le stock
                $piece = PieceDetachee::lockForUpdate()->findOrFail($piece->id);

                $quantite = (float) $data['quantite'];
                $type = $data['type'];

                $nouveauStock = match ($type) {
                    'entree', 'inventaire' => $piece->stock_actuel + $quantite,
                    'sortie' => $piece->stock_actuel - $quantite,
                    'ajustement' => $quantite,
                    default => $piece->stock_actuel,
                };

                if ($nouveauStock < 0) {
                    throw new \RuntimeException(
                        "Stock insuffisant pour « {$piece->designation} » "
                        ."(disponible : {$piece->stock_actuel}, demandé : {$quantite})."
                    );
                }

                $quantiteMouvement = $type === 'ajustement'
                    ? abs($nouveauStock - $piece->stock_actuel)
                    : $quantite;

                $typeMouvement = $type;
                if ($type === 'ajustement') {
                    $typeMouvement = $nouveauStock >= $piece->stock_actuel ? 'entree' : 'sortie';
                }

                MouvementStock::create([
                    'piece_id' => $piece->id,
                    'type' => $type === 'ajustement' ? 'ajustement' : $typeMouvement,
                    'quantite' => $quantiteMouvement,
                    'ordre_id' => $data['ordre_id'] ?? null,
                    'user_id' => auth()->id(),
                    'commentaire' => $data['commentaire'] ?? null,
                ]);

                $piece->update(['stock_actuel' => $nouveauStock]);
            });
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('stock.show', $piece)
            ->with('success', 'Mouvement de stock enregistré.');
    }

    /**
     * Laboratoire peinture : consommables au gramme / millilitre.
     */
    public function laboratoire(): View
    {
        $this->authorize('viewAny', PieceDetachee::class);

        $pieces = PieceDetachee::where('is_active', true)
            ->where(function ($q) {
                $q->where('categorie', 'like', '%peint%')
                    ->orWhere('categorie', 'like', '%vernis%')
                    ->orWhere('categorie', 'like', '%mastic%')
                    ->orWhere('categorie', 'like', '%appr%')
                    ->orWhereIn('unite', ['g', 'ml', 'kg', 'l']);
            })
            ->orderBy('designation')
            ->get();

        return view('stock.laboratoire', compact('pieces'));
    }

    /**
     * Liste des pièces en stock critique (alerte).
     */
    public function alertes(): View
    {
        $this->authorize('viewAny', PieceDetachee::class);

        $pieces = PieceDetachee::where('is_active', true)
            ->whereColumn('stock_actuel', '<=', 'stock_minimum')
            ->orderBy('stock_actuel')
            ->paginate(30);

        return view('stock.alertes', compact('pieces'));
    }
}
