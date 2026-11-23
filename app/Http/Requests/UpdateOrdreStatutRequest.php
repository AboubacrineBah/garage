<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrdreStatutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $user = $this->user();
        $estMecanicienSeul = $user
            && $user->hasRole('mecanicien')
            && ! $user->hasRole('gerant')
            && ! $user->hasRole('receptionniste')
            && ! $user->hasRole('chef_atelier')
            && ! $user->hasRole('comptable');

        $rules = [
            'statut' => [
                'required',
                Rule::in(['brouillon', 'devis', 'accepte', 'en_cours', 'termine', 'facture', 'annule', 'livre']),
            ],
            'diagnostic' => ['nullable', 'string', 'max:2000'],
            'date_sortie_reelle' => ['nullable', 'date'],
            'notes_internes' => ['nullable', 'string', 'max:1000'],
        ];

        // Les champs financiers ne sont accessibles qu'aux rôles administratifs / comptables
        if (! $estMecanicienSeul) {
            $rules['acompte'] = ['nullable', 'numeric', 'min:0'];
            $rules['mode_paiement'] = ['nullable', 'in:especes,orange_money,wave,moov,virement'];
        }

        return $rules;
    }

    /**
     * Empêche un mécanicien de forcer des champs financiers même s'ils sont présents dans la requête.
     */
    protected function prepareForValidation(): void
    {
        $user = $this->user();

        if ($user
            && $user->hasRole('mecanicien')
            && ! $user->hasRole('gerant')
            && ! $user->hasRole('receptionniste')
            && ! $user->hasRole('chef_atelier')
            && ! $user->hasRole('comptable')
        ) {
            $this->request->remove('acompte');
            $this->request->remove('mode_paiement');
        }
    }
}
