<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrdreReparationRequest extends FormRequest
{
    public function authorize(): bool
    {
        // L'autorisation fine est gérée dans le contrôleur via Policy.
        // Ici on s'assure juste que l'utilisateur est authentifié.
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            // Client existant ou nouveau
            'client_id' => ['nullable', 'exists:clients,id'],
            'nouveau_client' => ['nullable', 'boolean'],
            'client_nom' => ['required_if:nouveau_client,1', 'string', 'max:255'],
            'client_prenom' => ['nullable', 'string', 'max:100'],
            'client_telephone' => ['required_if:nouveau_client,1', 'string', 'max:30'],
            'client_type' => ['nullable', 'in:particulier,entreprise'],

            // Véhicule existant ou nouveau
            'vehicule_id' => ['nullable', 'exists:vehicules,id'],
            'nouveau_vehicule' => ['nullable', 'boolean'],
            'immatriculation' => ['required_if:nouveau_vehicule,1', 'string', 'max:20'],
            'marque' => ['required_if:nouveau_vehicule,1', 'string', 'max:80'],
            'modele' => ['required_if:nouveau_vehicule,1', 'string', 'max:80'],
            'kilometrage_entree' => ['required', 'integer', 'min:0'],
            'type_carburant' => ['nullable', 'in:essence,diesel,hybride,electrique,gpl'],
            'annee' => ['nullable', 'integer', 'min:1980', 'max:'.(date('Y') + 1)],

            // Travaux
            'description_travaux' => ['required', 'string', 'max:2000'],
            'date_sortie_prevue' => ['nullable', 'date', 'after_or_equal:today'],
            'mecaniciens_ids' => ['nullable', 'array'],
            'mecaniciens_ids.*' => ['exists:users,id'],

            // Lignes (pièces / main-d'œuvre)
            'lignes' => ['nullable', 'array'],
            'lignes.*.type_ligne' => ['required_with:lignes', 'in:piece,main_oeuvre,forfait,autre'],
            'lignes.*.piece_id' => ['nullable', 'exists:pieces_detachees,id'],
            'lignes.*.designation' => ['required_with:lignes', 'string', 'max:255'],
            'lignes.*.quantite' => ['required_with:lignes', 'numeric', 'min:0.01'],
            'lignes.*.prix_unitaire_ht' => ['required_with:lignes', 'numeric', 'min:0'],
            'lignes.*.remise_pct' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'client_nom.required_if' => 'Le nom du client est obligatoire.',
            'client_telephone.required_if' => 'Le téléphone du client est obligatoire.',
            'immatriculation.required_if' => 'L\'immatriculation est obligatoire.',
            'marque.required_if' => 'La marque est obligatoire.',
            'modele.required_if' => 'Le modèle est obligatoire.',
            'kilometrage_entree.required' => 'Le kilométrage d\'entrée est obligatoire.',
            'description_travaux.required' => 'La description des travaux est est obligatoire.',
        ];
    }
}
