@extends('layouts.app')

@section('title', $client->nom_complet)

@section('content')
<div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
    <div>
        <a href="{{ route('clients.index') }}" class="text-decoration-none small text-muted">
            <i class="bi bi-arrow-left"></i> Clients
        </a>
        <h1 class="h4 fw-bold text-primary-gm mb-0">{{ $client->nom_complet }}</h1>
        <span class="badge bg-secondary">{{ $client->type }}</span>
    </div>
    <a href="{{ route('clients.edit', $client) }}" class="btn btn-outline-primary btn-sm">
        <i class="bi bi-pencil"></i> Modifier
    </a>
</div>

<div class="row g-3">
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="mb-2"><i class="bi bi-telephone me-2"></i>{{ $client->telephone }}</div>
                @if($client->telephone_2)
                    <div class="mb-2"><i class="bi bi-telephone me-2"></i>{{ $client->telephone_2 }}</div>
                @endif
                @if($client->email)
                    <div class="mb-2"><i class="bi bi-envelope me-2"></i>{{ $client->email }}</div>
                @endif
                @if($client->adresse)
                    <div class="mb-2"><i class="bi bi-geo-alt me-2"></i>{{ $client->adresse }}</div>
                @endif
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Crédit autorisé</span>
                    <strong>{{ number_format($client->credit_autorise, 0, ',', ' ') }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Encours</span>
                    <strong class="{{ $client->solde_credit > 0 ? 'text-danger' : '' }}">
                        {{ number_format($client->solde_credit, 0, ',', ' ') }}
                    </strong>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-8">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold">Véhicules ({{ $client->vehicules->count() }})</div>
            <ul class="list-group list-group-flush">
                @forelse($client->vehicules as $v)
                    <li class="list-group-item d-flex justify-content-between">
                        <span>
                            <strong>{{ $v->immatriculation }}</strong>
                            — {{ $v->marque }} {{ $v->modele }}
                        </span>
                        <span class="text-muted small">{{ number_format($v->kilometrage_actuel, 0, ',', ' ') }} km</span>
                    </li>
                @empty
                    <li class="list-group-item text-muted">Aucun véhicule</li>
                @endforelse
            </ul>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Derniers ordres</div>
            <ul class="list-group list-group-flush">
                @forelse($client->ordres as $o)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <a href="{{ route('ordres.show', $o) }}" class="text-decoration-none">
                            {{ $o->numero }}
                        </a>
                        @include('partials.statut-badge', ['statut' => $o->statut])
                        <span class="fw-semibold">{{ number_format($o->montant_ttc, 0, ',', ' ') }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-muted">Aucun ordre</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
