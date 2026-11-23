@extends('layouts.app')

@section('title', 'Modifier ' . $piece->reference)

@section('content')
<div class="mb-3">
    <a href="{{ route('stock.show', $piece) }}" class="text-decoration-none small text-muted">
        <i class="bi bi-arrow-left"></i> Retour
    </a>
    <h1 class="h4 fw-bold text-primary-gm mt-1">Modifier la pièce</h1>
</div>

<form method="POST" action="{{ route('stock.update', $piece) }}" class="card border-0 shadow-sm">
    @csrf
    @method('PUT')
    <div class="card-body">
        <div class="row g-3">
            <div class="col-12 col-md-4">
                <label class="form-label">Référence *</label>
                <input type="text" name="reference" class="form-control" value="{{ old('reference', $piece->reference) }}" required>
                @error('reference') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-md-8">
                <label class="form-label">Désignation *</label>
                <input type="text" name="designation" class="form-control" value="{{ old('designation', $piece->designation) }}" required>
            </div>
            <div class="col-6 col-md-4">
                <label class="form-label">Catégorie</label>
                <input type="text" name="categorie" class="form-control" value="{{ old('categorie', $piece->categorie) }}">
            </div>
            <div class="col-6 col-md-4">
                <label class="form-label">Marque</label>
                <input type="text" name="marque" class="form-control" value="{{ old('marque', $piece->marque) }}">
            </div>
            <div class="col-6 col-md-4">
                <label class="form-label">Unité *</label>
                <select name="unite" class="form-select" required>
                    @foreach(['piece','litre','kg','metre','boite','jeu'] as $u)
                        <option value="{{ $u }}" @selected(old('unite', $piece->unite) === $u)>{{ $u }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-4">
                <label class="form-label">Prix d'achat (F CFA) *</label>
                <input type="number" name="prix_achat" class="form-control" value="{{ old('prix_achat', $piece->prix_achat) }}" min="0" required>
            </div>
            <div class="col-6 col-md-4">
                <label class="form-label">Prix de vente (F CFA) *</label>
                <input type="number" name="prix_vente" class="form-control" value="{{ old('prix_vente', $piece->prix_vente) }}" min="0" required>
            </div>
            <div class="col-6 col-md-4">
                <label class="form-label">Stock minimum *</label>
                <input type="number" name="stock_minimum" class="form-control" value="{{ old('stock_minimum', $piece->stock_minimum) }}" min="0" required>
            </div>
            <div class="col-6 col-md-4">
                <label class="form-label">Emplacement</label>
                <input type="text" name="emplacement" class="form-control" value="{{ old('emplacement', $piece->emplacement) }}">
            </div>
            <div class="col-6 col-md-4">
                <label class="form-label">Fournisseur principal</label>
                <input type="text" name="fournisseur_principal" class="form-control" value="{{ old('fournisseur_principal', $piece->fournisseur_principal) }}">
            </div>
            <div class="col-12 col-md-4">
                <div class="form-check mt-4">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                           @checked(old('is_active', $piece->is_active))>
                    <label class="form-check-label" for="is_active">Pièce active</label>
                </div>
            </div>
            <div class="col-12">
                <div class="alert alert-info small mb-0">
                    <i class="bi bi-info-circle"></i>
                    Pour modifier le stock actuel, utilisez les mouvements (entrée / sortie / ajustement).
                    Stock actuel : <strong>{{ number_format($piece->stock_actuel, 0, ',', ' ') }} {{ $piece->unite }}</strong>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer bg-white d-flex justify-content-end gap-2">
        <a href="{{ route('stock.show', $piece) }}" class="btn btn-outline-secondary">Annuler</a>
        <button type="submit" class="btn btn-primary">Enregistrer</button>
    </div>
</form>
@endsection
