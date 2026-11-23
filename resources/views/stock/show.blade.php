@extends('layouts.app')

@section('title', $piece->reference . ' – Stock')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
    <div>
        <a href="{{ route('stock.index') }}" class="text-decoration-none small text-muted">
            <i class="bi bi-arrow-left"></i> Stock
        </a>
        <h1 class="h4 fw-bold text-primary-gm mb-0">{{ $piece->reference }}</h1>
        <div class="text-muted">{{ $piece->designation }}</div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('stock.mouvement', ['type' => 'entree', 'piece_id' => $piece->id]) }}"
           class="btn btn-success btn-sm">
            <i class="bi bi-plus-lg"></i> Entrée
        </a>
        <a href="{{ route('stock.mouvement', ['type' => 'sortie', 'piece_id' => $piece->id]) }}"
           class="btn btn-outline-danger btn-sm">
            <i class="bi bi-dash-lg"></i> Sortie
        </a>
        <a href="{{ route('stock.edit', $piece) }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-pencil"></i> Modifier
        </a>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-6 col-md-3">
        <div class="card card-kpi h-100 {{ $piece->stock_critique ? 'border-warning' : '' }}">
            <div class="card-body py-3">
                <div class="text-muted small">Stock actuel</div>
                <div class="fs-3 fw-bold {{ $piece->stock_critique ? 'text-danger' : 'text-primary-gm' }}">
                    {{ number_format($piece->stock_actuel, $piece->unite === 'piece' ? 0 : 2, ',', ' ') }}
                </div>
                <div class="small text-muted">{{ $piece->unite }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card card-kpi h-100">
            <div class="card-body py-3">
                <div class="text-muted small">Stock minimum</div>
                <div class="fs-3 fw-bold">{{ number_format($piece->stock_minimum, 0, ',', ' ') }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card card-kpi h-100">
            <div class="card-body py-3">
                <div class="text-muted small">Prix de vente</div>
                <div class="fs-5 fw-bold text-accent">
                    {{ number_format($piece->prix_vente, 0, ',', ' ') }}
                </div>
                <div class="small text-muted">F CFA</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card card-kpi h-100">
            <div class="card-body py-3">
                <div class="text-muted small">Prix d'achat</div>
                <div class="fs-5 fw-bold">
                    {{ number_format($piece->prix_achat, 0, ',', ' ') }}
                </div>
                <div class="small text-muted">F CFA</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Informations</div>
            <div class="card-body small">
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Catégorie</span>
                    <span>{{ $piece->categorie ?? '—' }}</span>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Marque</span>
                    <span>{{ $piece->marque ?? '—' }}</span>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Emplacement</span>
                    <span>{{ $piece->emplacement ?? '—' }}</span>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Fournisseur</span>
                    <span>{{ $piece->fournisseur_principal ?? '—' }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Statut</span>
                    <span class="badge {{ $piece->is_active ? 'bg-success' : 'bg-secondary' }}">
                        {{ $piece->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Historique des mouvements</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th class="text-end">Quantité</th>
                            <th class="d-none d-md-table-cell">Par</th>
                            <th class="d-none d-md-table-cell">Commentaire</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mouvements as $mvt)
                            <tr>
                                <td class="small">{{ $mvt->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @php
                                        $badge = match($mvt->type) {
                                            'entree'     => 'success',
                                            'sortie'     => 'danger',
                                            'ajustement' => 'warning',
                                            'inventaire' => 'info',
                                            default      => 'secondary',
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $badge }}">{{ $mvt->type }}</span>
                                </td>
                                <td class="text-end fw-semibold">
                                    {{ $mvt->type === 'sortie' ? '−' : '+' }}{{ number_format($mvt->quantite, 0, ',', ' ') }}
                                </td>
                                <td class="d-none d-md-table-cell small">{{ $mvt->user?->name ?? '—' }}</td>
                                <td class="d-none d-md-table-cell small text-muted">{{ $mvt->commentaire ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">Aucun mouvement</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($mouvements->hasPages())
                <div class="card-footer bg-white">{{ $mouvements->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
