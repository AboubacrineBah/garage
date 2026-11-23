@extends('layouts.app')

@section('title', 'Réception & inspection carrosserie 360°')

@section('content')
<div class="d-flex align-items-center mb-3">
    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm me-2">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h1 class="h4 fw-bold text-primary-gm mb-0">Réception véhicule – Inspection 360°</h1>
</div>

<p class="text-muted small mb-3">
    Relevé visuel et photographique obligatoire à la réception sur parking pour éviter tout litige.
    <strong>4 angles + zones choquées + code teinte constructeur.</strong>
</p>

<form method="POST" action="{{ route('reception.inspection.store') }}" enctype="multipart/form-data">
    @csrf

    {{-- Identification véhicule --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white fw-semibold">
            <i class="bi bi-car-front me-1"></i> Identification du véhicule
        </div>
        <div class="card-body">
            <div class="row g-2">
                <div class="col-12 col-md-4">
                    <label class="form-label">Immatriculation <span class="text-danger">*</span></label>
                    <input type="text" name="immatriculation" class="form-control text-uppercase"
                           value="{{ old('immatriculation') }}" required maxlength="20">
                </div>
                <div class="col-6 col-md-4">
                    <label class="form-label">Marque <span class="text-danger">*</span></label>
                    <input type="text" name="marque" class="form-control" value="{{ old('marque') }}" required maxlength="60">
                </div>
                <div class="col-6 col-md-4">
                    <label class="form-label">Modèle <span class="text-danger">*</span></label>
                    <input type="text" name="modele" class="form-control" value="{{ old('modele') }}" required maxlength="60">
                </div>
                <div class="col-6 col-md-4">
                    <label class="form-label">Année</label>
                    <input type="number" name="annee" class="form-control" value="{{ old('annee') }}" min="1950" max="2100">
                </div>
                <div class="col-6 col-md-4">
                    <label class="form-label">Kilométrage</label>
                    <input type="number" name="kilometrage" class="form-control" value="{{ old('kilometrage') }}" min="0">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label">Code teinte constructeur</label>
                    <input type="text" name="code_teinte" class="form-control text-uppercase"
                           value="{{ old('code_teinte') }}" maxlength="40" placeholder="ex: L 90E / 475">
                    <div class="form-text">Indispensable pour la formulation peinture.</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Photos 360° --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white fw-semibold">
            <i class="bi bi-camera me-1"></i> Photos 360° (4 angles)
        </div>
        <div class="card-body">
            <div class="row g-3">
                @foreach([
                    'photo_avant'  => ['label' => 'Vue avant',  'icon' => 'bi-arrow-up-circle'],
                    'photo_arriere'=> ['label' => 'Vue arrière', 'icon' => 'bi-arrow-down-circle'],
                    'photo_gauche' => ['label' => 'Côté gauche', 'icon' => 'bi-arrow-left-circle'],
                    'photo_droite' => ['label' => 'Côté droit',  'icon' => 'bi-arrow-right-circle'],
                ] as $field => $meta)
                    <div class="col-6 col-md-3">
                        <label class="form-label small fw-semibold">{{ $meta['label'] }}</label>
                        <div class="border rounded-3 p-2 text-center bg-light"
                             style="min-height:120px;display:flex;flex-direction:column;align-items:center;justify-content:center;">
                            <i class="bi {{ $meta['icon'] }} fs-2 text-primary-gm mb-1"></i>
                            <input type="file" name="{{ $field }}" accept="image/*" capture="environment"
                                   class="form-control form-control-sm">
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="form-text mt-2">
                <i class="bi bi-info-circle"></i> Sur smartphone, le bouton ouvre directement l'appareil photo.
                Max 5 Mo par photo.
            </div>
        </div>
    </div>

    {{-- Zones choquées --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white fw-semibold">
            <i class="bi bi-exclamation-triangle me-1 text-warning"></i> Zones choquées / dommages constatés
        </div>
        <div class="card-body">
            <textarea name="zones_choquees" class="form-control" rows="4"
                      placeholder="Ex : choc pare-choc avant gauche, rayure portière ARD, optique cassé...">{{ old('zones_choquees') }}</textarea>
            <div class="form-text">Détaillez toutes les zones endommagées pour éviter tout litige à la livraison.</div>
        </div>
    </div>

    <div class="d-flex gap-2 justify-content-end">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Annuler</a>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check2-circle me-1"></i> Enregistrer l'inspection
        </button>
    </div>
</form>
@endsection