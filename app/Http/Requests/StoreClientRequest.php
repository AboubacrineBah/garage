<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $rules = [
            'type' => ['required', 'in:particulier,entreprise'],
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['nullable', 'string', 'max:100'],
            'telephone' => ['required', 'string', 'max:30'],
            'telephone_2' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'adresse' => ['nullable', 'string', 'max:500'],
            'ville' => ['nullable', 'string', 'max:100'],
            'numero_contribuable' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];

        // Seuls gérant et comptable peuvent définir / modifier le plafond de crédit
        $user = $this->user();
        if ($user && ($user->hasRole('gerant') || $user->hasRole('comptable'))) {
            $rules['credit_autorise'] = ['nullable', 'numeric', 'min:0'];
        }

        return $rules;
    }

    protected function prepareForValidation(): void
    {
        $user = $this->user();

        // Empêcher un réceptionniste de forcer credit_autorise
        if (! $user || (! $user->hasRole('gerant') && ! $user->hasRole('comptable'))) {
            $this->request->remove('credit_autorise');
        }
    }
}
