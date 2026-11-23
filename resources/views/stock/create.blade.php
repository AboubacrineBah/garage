@extends('layouts.app')

@section('title', 'Nouvelle pièce')

@section('content')
<div class="mb-3">
    <a href="{{ route('stock.index') }}" class="text-decoration-none small text-muted">
        <i class="bi bi-arrow-left"></i> Stock
    </a>
    <h1 class="h4 fw-bold text-primary-gm mt-1">Nouvelle pièce</h1>
</div>

<form method="POST" action="{{ route('stock.store') }}" class="card border-0 shadow-sm">
    @csrf
    <div class="card-body">
        <div class="row g-3">
            <div class="col-12 col-md-4">
                <label class="form-label">Référence *</label>
                <input type="text" name="reference" class="form-control" value="{{ old('reference') }}" required>
                @error('reference') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-md-8">
                <label class="form-label">Désignation *</label>
                <input type="text" name="designation" class="form-control" value="{{ old('designation') }}" required>
            </div>
            <div class="col-6 col-md-4">
                <label class="form-label">Catégorie</label>
                <input type="text" name="categorie" class="form-control" value="{{ old('categorie') }}"
                       placeholder="filtre, huile, freinage...">
            </div>
            <div class="col-6 col-md-4">
                <label class="form-label">Marque</label>
                <input type="text" name="marque" class="form-control" value="{{ old('marque') }}">
            </div>
            <div class="col-6 col-md-4">
                <label class="form-label">Unité *</label>
                <select name="unite" class="form-select" required>
                    @foreach(['piece','litre','kg','metre','boite','jeu'] as $u)
                        <option value="{{ $u }}" @selected(old('unite', 'piece') === $u)>{{ $u }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label">Prix d'achat (F CFA) *</label>
                <input type="number" name="prix_achat" class="form-control" value="{{ old('prix_achat', 0) }}" min="0" required>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label">Prix de vente (F CFA) *</label>
                <input type="number" name="prix_vente" class="form-control" value="{{ old('prix_vente', 0) }}" min="0" required>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label">Stock initial</label>
                <input type="number" name="stock_actuel" class="form-control" value="{{ old('stock_actuel', 0) }}" min="0" step="0.01">
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label">Stock minimum *</label>
                <input type="number" name="stock_minimum" class="form-control" value="{{ old('stock_minimum', 1) }}" min="0" required>
            </div>
            <div class="col-6 col-md-4">
                <label class="form-label">Emplacement</label>
                <input type="text" name="emplacement" class="form-control" value="{{ old('emplacement') }}"
                       placeholder="Rayon A3, Bac 12...">
            </div>
            <div class="col-6 col-md-4">
                <label class="form-label">Fournisseur principal</label>
                <input type="text" name="fournisseur_principal" class="form-control" value="{{ old('fournisseur_principal') }}">
            </div>
            <div class="col-12 col-md-4">
                <div class="form-check mt-4">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" checked>
                    <label class="form-check-label" for="is_active">Pièce active</label>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer bg-white d-flex justify-content-end gap-2">
        <a href="{{ route('stock.index') }}" class="btn btn-outline-secondary">Annuler</a>
        <button type="submit" class="btn btn-primary">Enregistrer</button>
    </div>
</form>
@endsection
