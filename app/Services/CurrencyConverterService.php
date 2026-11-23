<?php

namespace App\Services;

use App\Models\Devise;
use InvalidArgumentException;

/**
 * Service de conversion et formatage monétaire.
 * Devise de référence interne : XOF (sans décimales).
 */
class CurrencyConverterService
{
    /**
     * Convertit un montant d'une devise vers une autre.
     * Les montants XOF/XAF sont toujours retournés entiers.
     */
    public function convert(float $amount, string $from, string $to): float
    {
        $from = strtoupper($from);
        $to = strtoupper($to);

        if ($from === $to) {
            return $this->roundForCurrency($amount, $to);
        }

        $deviseFrom = Devise::findByCode($from);
        $deviseTo = Devise::findByCode($to);

        if (! $deviseFrom || ! $deviseTo) {
            throw new InvalidArgumentException("Devise inconnue : {$from} ou {$to}");
        }

        // Conversion via XOF comme pivot
        $montantEnXof = $amount * (float) $deviseFrom->taux_vers_xof;
        $resultat = $montantEnXof / (float) $deviseTo->taux_vers_xof;

        return $this->roundForCurrency($resultat, $to);
    }

    /**
     * Formate un montant pour affichage selon la devise.
     * Exemple XOF : "1 250 000 F CFA"
     */
    public function format(float $amount, string $currencyCode = 'XOF'): string
    {
        $devise = Devise::findByCode($currencyCode) ?? Devise::default();

        if (! $devise) {
            return number_format($amount, 0, ',', ' ').' XOF';
        }

        $decimals = (int) $devise->decimales;
        $formatted = number_format($amount, $decimals, ',', ' ');

        return $formatted.' '.$devise->symbole;
    }

    /**
     * Retourne la devise par défaut (XOF).
     */
    public function getDefaultCurrency(): ?Devise
    {
        return Devise::default();
    }

    /**
     * Arrondi adapté à la devise (0 décimale pour XOF/XAF).
     */
    protected function roundForCurrency(float $amount, string $code): float
    {
        $devise = Devise::findByCode($code);
        $decimals = $devise ? (int) $devise->decimales : 0;

        return round($amount, $decimals);
    }
}
