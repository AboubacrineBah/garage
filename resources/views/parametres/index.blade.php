@extends('layouts.app')

@section('title', 'Paramètres atelier')

@section('content')
<div class="mb-3">
    <h1 class="h4 fw-bold text-primary-gm">Paramètres de l'atelier</h1>
    <p class="text-muted small mb-0">Coordonnées, facturation et modèles de messages WhatsApp/SMS.</p>
</div>

<form method="POST" action="{{ route('parametres.update') }}">
    @csrf
    @method('PUT')

    @foreach($groupes as $nomGroupe => $params)
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold text-capitalize">
                {{ $nomGroupe === 'notifications' ? 'Notifications (templates)' : ucfirst($nomGroupe) }}
            </div>
            <div class="card-body">
                @forelse($params as $param)
                    <div class="mb-3">
                        <label class="form-label">
                            {{ $param->libelle ?? $param->cle }}
                        </label>
                        @if($param->type === 'text')
                            <textarea name="parametres[{{ $param->cle }}]" class="form-control" rows="3">{{ old('parametres.'.$param->cle, $param->valeur) }}</textarea>
                            <div class="form-text">
                                Variables disponibles : {{ 'client' }}, {{ 'immatriculation' }}, {{ 'montant' }}, {{ 'atelier' }}
                            </div>
                        @else
                            <input type="{{ $param->type === 'int' ? 'number' : 'text' }}"
                                   name="parametres[{{ $param->cle }}]"
                                   class="form-control"
                                   value="{{ old('parametres.'.$param->cle, $param->valeur) }}">
                        @endif
                        @if($param->description)
                            <div class="form-text">{{ $param->description }}</div>
                        @endif
                    </div>
                @empty
                    <p class="text-muted mb-0">Aucun paramètre dans ce groupe.</p>
                @endforelse
            </div>
        </div>
    @endforeach

    <div class="d-grid d-md-flex justify-content-md-end mb-4">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check2"></i> Enregistrer les paramètres
        </button>
    </div>
</form>
@endsection
