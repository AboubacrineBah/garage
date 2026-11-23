@extends('layouts.app')

@section('title', 'Laboratoire peinture – Consommables')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <a href="{{ route('stock.index') }}" class="text-decoration-none small text-muted">
            <i class="bi bi-arrow-left"></i> Stock pièces
        </a>
        <h1 class="h4 fw-bold text-primary-gm mb-0">Laboratoire peinture</h1>
    </div>
    <a href="{{ route('stock.mouvement', ['type' => 'sortie']) }}" class="btn btn-primary btn-sm">
        <i class="bi bi-droplet"></i> Consommation
    </a>
</div>

<p class="text-muted small mb-3">
    Suivi strict des produits chimiques coûteux (peintures, durcisseurs, vernis, apprêts, mastics)
    au <strong>gramme / millilitre</strong>, avec fiche de formulation de teinte.
</p>

{{-- Alerte stock critique --}}
@php
    $critiques = $pieces->filter(fn ($p) => $p->stock_critique);
@endphp
@if($critiques->isNotEmpty())
    <div class="alert alert-warning d-flex align-items-center mb-3" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <div>
            <strong>{{ $critiques->count() }} consommable(s)</strong> sous le seuil minimum.
            <a href="{{ route('stock.alertes') }}" class="alert-link ms-1">Voir les alertes</a>
        </div>
    </div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-semibold">
        <i class="bi bi-palette me-1"></i> Consommables peinture
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Référence</th>
                    <th>Désignation</th>
                    <th>Catégorie</th>
                    <th class="text-end">Stock</th>
                    <th>Unité</th>
                    <th>Code teinte</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pieces as $piece)
                    <tr class="{{ $piece->stock_critique ? 'table-warning' : '' }}">
                        <td class="fw-semibold small">{{ $piece->reference }}</td>
                        <td>{{ $piece->designation }}</td>
                        <td><span class="badge bg-light text-dark">{{ $piece->categorie ?? '—' }}</span></td>
                        <td class="text-end fw-bold {{ $piece->stock_critique ? 'text-danger' : '' }}">
                            {{ number_format($piece->stock_actuel, 2, ',', ' ') }}
                        </td>
                        <td>{{ $piece->unite }}</td>
                        <td class="small text-muted">—</td>
                        <td class="text-end">
                            <a href="{{ route('stock.mouvement', ['piece_id' => $piece->id, 'type' => 'sortie']) }}"
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-dash-circle"></i>
                            </a>
                            <a href="{{ route('stock.show', $piece) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            Aucun consommable peinture enregistré.
                            <a href="{{ route('stock.create') }}">Ajouter une pièce</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Fiche de formulation (placeholder) --}}
<div class="card border-0 shadow-sm mt-3">
    <div class="card-header bg-white fw-semibold">
        <i class="bi bi-file-earmark-text me-1"></i> Fiche de formulation de teinte
    </div>
    <div class="card-body">
        <p class="text-muted small mb-2">
            Saisie des composants (base, durcisseur, vernis, pigment) en grammes pour reproduire une teinte constructeur.
            <em>La persistance dédiée (modèle FormulationPeinture) sera branchée avec le Module 3.</em>
        </p>
        <form class="row g-2">
            <div class="col-12 col-md-4">
                <label class="form-label small">Code teinte constructeur</label>
                <input type="text" class="form-control form-control-sm text-uppercase" placeholder="ex: L 90E">
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label small">Véhicule / OR</label>
                <input type="text" class="form-control form-control-sm" placeholder="OR-2026-00001">
            </div>
            <div class="col-12 col-md-4 d-flex align-items-end">
                <a href="{{ route('stock.formulation.create') }}" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-plus-circle"></i> Nouvelle formulation
                </a>
            </div>
        </form>
    </div>
</div>
@endsection