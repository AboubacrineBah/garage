<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePieceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $pieceId = $this->route('piece')?->id;

        return [
            'reference' => [
                'required', 'string', 'max:50',
                Rule::unique('pieces_detachees', 'reference')->ignore($pieceId),
            ],
            'designation' => ['required', 'string', 'max:255'],
            'categorie' => ['nullable', 'string', 'max:80'],
            'marque' => ['nullable', 'string', 'max:80'],
            'prix_achat' => ['required', 'numeric', 'min:0'],
            'prix_vente' => ['required', 'numeric', 'min:0'],
            'stock_actuel' => ['nullable', 'numeric', 'min:0'],
            'stock_minimum' => ['required', 'numeric', 'min:0'],
            'unite' => ['required', 'string', 'max:20'],
            'emplacement' => ['nullable', 'string', 'max:50'],
            'fournisseur_principal' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
