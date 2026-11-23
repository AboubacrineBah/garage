<?php

namespace App\Http\Controllers;

use App\Models\ComposantFormulation;
use App\Models\FormulationPeinture;
use App\Models\OrdreReparation;
use App\Models\PieceDetachee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FormulationPeintureController extends Controller
{
    public function create(): View
    {
        $this->authorize('create', PieceDetachee::class);

        $ordres = OrdreReparation::latest()->take(20)->get(['id', 'numero']);
        $pieces = PieceDetachee::where('is_active', true)->orderBy('designation')->get();

        return view('stock.formulation-create', compact('ordres', 'pieces'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', PieceDetachee::class);

        $data = $request->validate([
            'nom' => ['required', 'string', 'max:120'],
            'code_teinte' => ['nullable', 'string', 'max:40'],
            'ordre_id' => ['nullable', 'exists:ordres_reparation,id'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'composants' => ['nullable', 'array'],
            'composants.*.designation' => ['required_with:composants', 'string', 'max:160'],
            'composants.*.quantite' => ['required_with:composants', 'numeric', 'min:0'],
            'composants.*.unite' => ['nullable', 'string', 'max:10'],
            'composants.*.piece_id' => ['nullable', 'exists:pieces_detachees,id'],
        ]);

        $formulation = FormulationPeinture::create([
            'nom' => $data['nom'],
            'code_teinte' => $data['code_teinte'] ?? null,
            'ordre_id' => $data['ordre_id'] ?? null,
            'notes' => $data['notes'] ?? null,
            'user_id' => auth()->id(),
        ]);

        foreach ($data['composants'] ?? [] as $c) {
            if (empty($c['designation'])) {
                continue;
            }
            ComposantFormulation::create([
                'formulation_id' => $formulation->id,
                'piece_id' => $c['piece_id'] ?? null,
                'designation' => $c['designation'],
                'quantite' => $c['quantite'],
                'unite' => $c['unite'] ?? 'g',
            ]);
        }

        return redirect()
            ->route('stock.laboratoire')
            ->with('success', 'Formulation de teinte enregistrée.');
    }
}
