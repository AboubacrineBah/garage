<?php

namespace App\Http\Controllers;

use App\Models\InspectionCarrosserie;
use App\Models\OrdreReparation;
use App\Models\Vehicule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Contrôleur de réception véhicule & inspection carrosserie 360°.
 */
class ReceptionController extends Controller
{
    public function inspectionForm(): View
    {
        $this->authorize('create', OrdreReparation::class);

        return view('reception.inspection');
    }

    public function storeInspection(Request $request): RedirectResponse
    {
        $this->authorize('create', OrdreReparation::class);

        $request->validate([
            'immatriculation' => ['required', 'string', 'max:20'],
            'marque' => ['required', 'string', 'max:60'],
            'modele' => ['required', 'string', 'max:60'],
            'code_teinte' => ['nullable', 'string', 'max:40'],
            'kilometrage' => ['nullable', 'integer', 'min:0'],
            'photo_avant' => ['nullable', 'image', 'max:5120'],
            'photo_arriere' => ['nullable', 'image', 'max:5120'],
            'photo_gauche' => ['nullable', 'image', 'max:5120'],
            'photo_droite' => ['nullable', 'image', 'max:5120'],
            'zones_choquees' => ['nullable', 'string', 'max:2000'],
        ]);

        $paths = [];
        foreach (['avant', 'arriere', 'gauche', 'droite'] as $angle) {
            $field = 'photo_'.$angle;
            if ($request->hasFile($field)) {
                $paths[$field] = $request->file($field)->store('inspections', 'public');
            }
        }

        $vehicule = Vehicule::where('immatriculation', strtoupper($request->immatriculation))->first();

        InspectionCarrosserie::create(array_merge([
            'vehicule_id' => $vehicule?->id,
            'user_id' => auth()->id(),
            'kilometrage' => $request->kilometrage,
            'code_teinte' => $request->code_teinte,
            'zones_choquees' => $request->zones_choquees,
            'date_inspection' => now(),
        ], $paths));

        return redirect()
            ->route('ordres.create')
            ->with('success', 'Inspection 360° enregistrée (photos + relevé). Vous pouvez créer l\'ordre de réparation.');
    }
}
