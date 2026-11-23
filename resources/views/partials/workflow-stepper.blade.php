@php
    // Étapes métier carrosserie / peinture (skill garage-carrosserie)
    $etapes = [
        ['key' => 'devis',     'label' => 'Expertise / Devis',      'icon' => 'bi-clipboard-check'],
        ['key' => 'tolerie',   'label' => 'Tôlerie / Débosselage',  'icon' => 'bi-hammer'],
        ['key' => 'prep',      'label' => 'Préparation / Masticage', 'icon' => 'bi-brush'],
        ['key' => 'peinture',  'label' => 'Cabine Peinture / Vernis','icon' => 'bi-palette'],
        ['key' => 'mecanique', 'label' => 'Mécanique rapide / Finition', 'icon' => 'bi-gear'],
        ['key' => 'livraison', 'label' => 'Prêt pour livraison',     'icon' => 'bi-check2-circle'],
    ];

    // Mapping statut actuel → étape courante (index 0..5)
    $map = [
        'brouillon' => 0, 'devis' => 0,
        'accepte'   => 1,
        'en_cours'  => 2,
        'termine'   => 4,
        'facture'   => 5,
        'livre'     => 5,
        'annule'    => -1,
    ];
    $courant = $map[$statut ?? 'devis'] ?? 0;
@endphp

<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white fw-semibold">
        <i class="bi bi-diagram-3 me-1"></i> Workflow carrosserie &amp; peinture
    </div>
    <div class="card-body">
        <div class="d-flex flex-wrap gap-2">
            @foreach($etapes as $i => $e)
                @php
                    $done = ($courant >= 0 && $i < $courant);
                    $active = ($i === $courant);
                @endphp
                <div class="d-flex align-items-center">
                    <span class="badge rounded-pill
                        {{ $active ? 'bg-primary' : ($done ? 'bg-success' : 'bg-light text-dark border') }}
                        px-3 py-2">
                        <i class="bi {{ $e['icon'] }} me-1"></i>{{ $e['label'] }}
                    </span>
                    @if(!$loop->last)
                        <i class="bi bi-chevron-right mx-1 text-muted small"></i>
                    @endif
                </div>
            @endforeach
        </div>
        @if(($statut ?? '') === 'annule')
            <div class="mt-2">
                <span class="badge bg-danger">Ordre annulé</span>
            </div>
        @endif
    </div>
</div>