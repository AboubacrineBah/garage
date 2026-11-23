@extends('layouts.app')

@section('title', 'Stock de pièces')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h1 class="h4 mb-0 fw-bold text-primary-gm">Stock de pièces</h1>
        @if($nbCritiques > 0)
            <a href="{{ route('stock.alertes') }}" class="badge bg-warning text-dark text-decoration-none">
                {{ $nbCritiques }} alerte{{ $nbCritiques > 1 ? 's' : '' }} stock
            </a>
        @endif
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('stock.mouvement', ['type' => 'entree']) }}" class="btn btn-outline-success btn-sm">
            <i class="bi bi-box-arrow-in-down"></i> Entrée
        </a>
        <a href="{{ route('stock.mouvement', ['type' => 'sortie']) }}" class="btn btn-outline-danger btn-sm">
            <i class="bi bi-box-arrow-up"></i> Sortie
        </a>
        <a href="{{ route('stock.create') }}" class="btn btn-accent btn-sm">
            <i class="bi bi-plus-lg"></i> Pièce
        </a>
    </div>
</div>

{{-- Filtres --}}
<form method="GET" class="row g-2 mb-3">
    <div class="col-12 col-md-4">
        <input type="search" name="search" value="{{ request('search') }}"
               class="form-control form-control-sm"
               placeholder="Référence, désignation, marque...">
    </div>
    <div class="col-6 col-md-3">
        <select name="categorie" class="form-select form-select-sm">
            <option value="">Toutes catégories</option>
            @foreach($categories as $cat)
                <option value="{{ $cat }}" @selected(request('categorie') === $cat)>{{ $cat }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-6 col-md-2">
        <div class="form-check mt-1">
            <input class="form-check-input" type="checkbox" name="critique" value="1"
                   id="filtre-critique" @checked(request()->boolean('critique'))>
            <label class="form-check-label small" for="filtre-critique">Stock critique uniquement</label>
        </div>
    </div>
    <div class="col-12 col-md-2">
        <button type="submit" class="btn btn-primary btn-sm w-100">Filtrer</button>
    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover table-mobile mb-0">
            <thead class="table-light">
                <tr>
                    <th>Référence</th>
                    <th>Désignation</th>
                    <th class="d-none d-md-table-cell">Catégorie</th>
                    <th class="text-end">Stock</th>
                    <th class="text-end d-none d-md-table-cell">Mini</th>
                    <th class="text-end">P.Vente</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($pieces as $piece)
                    <tr class="{{ $piece->stock_critique ? 'table-warning' : '' }}">
                        <td class="fw-semibold">
                            <a href="{{ route('stock.show', $piece) }}" class="text-decoration-none">
                                {{ $piece->reference }}
                            </a>
                        </td>
                        <td>
                            {{ $piece->designation }}
                            @if($piece->stock_critique)
                                <i class="bi bi-exclamation-triangle-fill text-warning" title="Stock critique"></i>
                            @endif
                        </td>
                        <td class="d-none d-md-table-cell small">{{ $piece->categorie ?? '—' }}</td>
                        <td class="text-end fw-bold {{ $piece->stock_critique ? 'text-danger' : '' }}">
                            {{ number_format($piece->stock_actuel, $piece->unite === 'piece' ? 0 : 2, ',', ' ') }}
                            <span class="text-muted small">{{ $piece->unite }}</span>
                        </td>
                        <td class="text-end d-none d-md-table-cell text-muted">
                            {{ number_format($piece->stock_minimum, 0, ',', ' ') }}
                        </td>
                        <td class="text-end">
                            {{ number_format($piece->prix_vente, 0, ',', ' ') }}
                        </td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('stock.mouvement', ['type' => 'entree', 'piece_id' => $piece->id]) }}"
                               class="btn btn-sm btn-outline-success" title="Entrée">
                                <i class="bi bi-plus"></i>
                            </a>
                            <a href="{{ route('stock.mouvement', ['type' => 'sortie', 'piece_id' => $piece->id]) }}"
                               class="btn btn-sm btn-outline-danger" title="Sortie">
                                <i class="bi bi-dash"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            Aucune pièce trouvée.
                            <a href="{{ route('stock.create') }}">Ajouter une pièce</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($pieces->hasPages())
        <div class="card-footer bg-white">{{ $pieces->links() }}</div>
    @endif
</div>
@endsection
