@extends('layouts.app')

@section('title', 'Alertes stock')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <a href="{{ route('stock.index') }}" class="text-decoration-none small text-muted">
            <i class="bi bi-arrow-left"></i> Stock
        </a>
        <h1 class="h4 fw-bold text-primary-gm mb-0">
            <i class="bi bi-exclamation-triangle text-warning"></i> Alertes stock
        </h1>
    </div>
    <a href="{{ route('stock.mouvement', ['type' => 'entree']) }}" class="btn btn-success btn-sm">
        <i class="bi bi-box-arrow-in-down"></i> Faire une entrée
    </a>
</div>

@if($pieces->isEmpty())
    <div class="alert alert-success">
        <i class="bi bi-check-circle"></i> Aucune pièce en stock critique. Tout est sous contrôle.
    </div>
@else
    <div class="alert alert-warning">
        <strong>{{ $pieces->total() }}</strong> pièce(s) ont atteint ou dépassé le seuil minimum.
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Référence</th>
                        <th>Désignation</th>
                        <th class="text-end">Stock actuel</th>
                        <th class="text-end">Seuil mini</th>
                        <th class="text-end">Manque</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pieces as $piece)
                        @php $manque = max(0, $piece->stock_minimum - $piece->stock_actuel); @endphp
                        <tr>
                            <td class="fw-semibold">
                                <a href="{{ route('stock.show', $piece) }}">{{ $piece->reference }}</a>
                            </td>
                            <td>{{ $piece->designation }}</td>
                            <td class="text-end text-danger fw-bold">
                                {{ number_format($piece->stock_actuel, 0, ',', ' ') }}
                            </td>
                            <td class="text-end">{{ number_format($piece->stock_minimum, 0, ',', ' ') }}</td>
                            <td class="text-end text-warning fw-semibold">
                                {{ number_format($manque, 0, ',', ' ') }}
                            </td>
                            <td class="text-end">
                                <a href="{{ route('stock.mouvement', ['type' => 'entree', 'piece_id' => $piece->id]) }}"
                                   class="btn btn-sm btn-success">
                                    Réapprovisionner
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($pieces->hasPages())
            <div class="card-footer bg-white">{{ $pieces->links() }}</div>
        @endif
    </div>
@endif
@endsection
