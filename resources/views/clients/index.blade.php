@extends('layouts.app')

@section('title', 'Clients')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h1 class="h4 mb-0 fw-bold text-primary-gm">Clients</h1>
    <a href="{{ route('clients.create') }}" class="btn btn-accent btn-sm">
        <i class="bi bi-plus-lg"></i> Nouveau client
    </a>
</div>

<form method="GET" class="mb-3">
    <div class="input-group">
        <input type="search" name="search" value="{{ request('search') }}"
               class="form-control" placeholder="Nom, prénom, téléphone...">
        <button class="btn btn-primary" type="submit">Rechercher</button>
    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nom</th>
                    <th class="d-none d-md-table-cell">Téléphone</th>
                    <th class="d-none d-md-table-cell">Véhicules</th>
                    <th class="text-end">Crédit</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $client)
                    <tr>
                        <td>
                            <a href="{{ route('clients.show', $client) }}" class="text-decoration-none fw-semibold">
                                {{ $client->nom_complet }}
                            </a>
                            <div class="small text-muted d-md-none">{{ $client->telephone }}</div>
                        </td>
                        <td class="d-none d-md-table-cell">{{ $client->telephone }}</td>
                        <td class="d-none d-md-table-cell">{{ $client->vehicules_count }}</td>
                        <td class="text-end">
                            @if($client->solde_credit > 0)
                                <span class="text-danger fw-semibold">
                                    {{ number_format($client->solde_credit, 0, ',', ' ') }}
                                </span>
                            @else
                                <span class="text-muted">0</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('clients.show', $client) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Aucun client.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($clients->hasPages())
        <div class="card-footer bg-white">{{ $clients->links() }}</div>
    @endif
</div>
@endsection
