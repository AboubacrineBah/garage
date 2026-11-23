@extends('layouts.app')

@section('title', 'Ordres de réparation')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h1 class="h4 mb-0 fw-bold text-primary-gm">Ordres de réparation</h1>
    <a href="{{ route('ordres.create') }}" class="btn btn-accent btn-sm">
        <i class="bi bi-plus-lg"></i> Nouvel ordre
    </a>
</div>

{{-- Filtres --}}
<form method="GET" class="row g-2 mb-3">
    <div class="col-12 col-md-5">
        <input type="search" name="search" value="{{ request('search') }}"
               class="form-control form-control-sm"
               placeholder="N°, client, immatriculation...">
    </div>
    <div class="col-8 col-md-3">
        <select name="statut" class="form-select form-select-sm">
            <option value="">Tous les statuts</option>
            @foreach(['devis','accepte','en_cours','termine','facture','livre','annule'] as $s)
                <option value="{{ $s }}" @selected(request('statut') === $s)>
                    {{ ucfirst(str_replace('_', ' ', $s)) }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-4 col-md-2">
        <button type="submit" class="btn btn-primary btn-sm w-100">Filtrer</button>
    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover table-mobile mb-0">
            <thead class="table-light">
                <tr>
                    <th>N°</th>
                    <th>Client</th>
                    <th class="d-none d-md-table-cell">Véhicule</th>
                    <th>Statut</th>
                    <th class="text-end">Montant</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($ordres as $ordre)
                    <tr>
                        <td class="fw-semibold">
                            <a href="{{ route('ordres.show', $ordre) }}" class="text-decoration-none">
                                {{ $ordre->numero }}
                            </a>
                        </td>
                        <td>
                            <div>{{ $ordre->client->nom_complet ?? '—' }}</div>
                            <div class="small text-muted d-md-none">
                                {{ $ordre->vehicule->immatriculation ?? '' }}
                            </div>
                        </td>
                        <td class="d-none d-md-table-cell small">
                            {{ $ordre->vehicule->immatriculation ?? '' }}
                            <br>{{ $ordre->vehicule->marque ?? '' }} {{ $ordre->vehicule->modele ?? '' }}
                        </td>
                        <td>@include('partials.statut-badge', ['statut' => $ordre->statut])</td>
                        <td class="text-end fw-semibold">
                            {{ number_format($ordre->montant_ttc, 0, ',', ' ') }}
                        </td>
                        <td class="text-end">
                            <a href="{{ route('ordres.show', $ordre) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            Aucun ordre trouvé.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($ordres->hasPages())
        <div class="card-footer bg-white">
            {{ $ordres->links() }}
        </div>
    @endif
</div>
@endsection
