@php
    $classes = match($statut) {
        'brouillon' => 'bg-secondary',
        'devis'     => 'bg-info text-dark',
        'accepte'   => 'bg-primary',
        'en_cours'  => 'bg-warning text-dark',
        'termine'   => 'bg-success',
        'facture'   => 'bg-success',
        'livre'     => 'bg-dark',
        'annule'    => 'bg-danger',
        default     => 'bg-secondary',
    };
    $labels = [
        'brouillon' => 'Brouillon',
        'devis'     => 'Devis',
        'accepte'   => 'Accepté',
        'en_cours'  => 'En cours',
        'termine'   => 'Terminé',
        'facture'   => 'Facturé',
        'livre'     => 'Livré',
        'annule'    => 'Annulé',
    ];
@endphp
<span class="badge statut-badge {{ $classes }}">
    {{ $labels[$statut] ?? $statut }}
</span>
