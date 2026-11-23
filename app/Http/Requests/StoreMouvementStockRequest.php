<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMouvementStockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'piece_id' => ['required', 'exists:pieces_detachees,id'],
            'type' => ['required', Rule::in(['entree', 'sortie', 'ajustement', 'inventaire'])],
            'quantite' => ['required', 'numeric', 'min:0.01'],
            'ordre_id' => ['nullable', 'exists:ordres_reparation,id'],
            'commentaire' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'piece_id.required' => 'Veuillez sélectionner une pièce.',
            'quantite.required' => 'La quantité est obligatoire.',
            'quantite.min' => 'La quantité doit être supérieure à 0.',
        ];
    }
}
