@extends('layouts.app')

@section('title', 'Nouveau client')

@section('content')
<div class="mb-3">
    <a href="{{ route('clients.index') }}" class="text-decoration-none small text-muted">
        <i class="bi bi-arrow-left"></i> Retour
    </a>
    <h1 class="h4 fw-bold text-primary-gm mt-1">Nouveau client</h1>
</div>

<form method="POST" action="{{ route('clients.store') }}" class="card border-0 shadow-sm">
    @csrf
    <div class="card-body">
        <div class="row g-3">
            <div class="col-12 col-md-4">
                <label class="form-label">Type *</label>
                <select name="type" class="form-select" required>
                    <option value="particulier" @selected(old('type') === 'particulier')>Particulier</option>
                    <option value="entreprise" @selected(old('type') === 'entreprise')>Entreprise</option>
                </select>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label">Nom / Raison sociale *</label>
                <input type="text" name="nom" class="form-control" value="{{ old('nom') }}" required>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label">Prénom</label>
                <input type="text" name="prenom" class="form-control" value="{{ old('prenom') }}">
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label">Téléphone *</label>
                <input type="tel" name="telephone" class="form-control" value="{{ old('telephone') }}" required>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label">Téléphone 2</label>
                <input type="tel" name="telephone_2" class="form-control" value="{{ old('telephone_2') }}">
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}">
            </div>
            <div class="col-12">
                <label class="form-label">Adresse</label>
                <textarea name="adresse" class="form-control" rows="2">{{ old('adresse') }}</textarea>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label">Ville</label>
                <input type="text" name="ville" class="form-control" value="{{ old('ville') }}">
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label">Crédit autorisé (F CFA)</label>
                <input type="number" name="credit_autorise" class="form-control" value="{{ old('credit_autorise', 0) }}" min="0">
            </div>
        </div>
    </div>
    <div class="card-footer bg-white d-flex justify-content-end gap-2">
        <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary">Annuler</a>
        <button type="submit" class="btn btn-primary">Enregistrer</button>
    </div>
</form>
@endsection
