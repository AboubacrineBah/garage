# Cahier des Charges Technique — Garage Moderne

## 1. Architecture générale
- **Serveur** : XAMPP (Apache, MySQL/MariaDB, PHP 8.3+).
- **Backend** : Laravel 13 (PHP 8.3+), Eloquent, Form Requests, Policies.
- **Frontend** : Blade, Bootstrap 5, Vite, mobile-first (PWA).
- **Qualité** : Pint (PSR-12), PHPStan niveau 5, PHPUnit.

## 2. MCD / MLD

### Entités principales

| Entité | Table | Attributs clés |
|---|---|---|
| User | `users` | id, name, email, password, phone, role_id, is_active, last_login_at |
| Role | `roles` | id, name, display_name, permissions (json), description |
| Client | `clients` | id, type, nom, prenom, telephone, telephone_2, email, adresse, ville, numero_contribuable, credit_autorise, solde_credit, notes, created_by, deleted_at |
| Vehicule | `vehicules` | id, client_id, immatriculation, marque, modele, annee, couleur, numero_chassis, kilometrage_actuel, type_carburant, date_mise_circulation, photo_path, notes, deleted_at |
| Assurance | `assurances` | id, nom, code, telephone, email, adresse, is_active, deleted_at |
| Sinistre | `sinistres` | id, ordre_id, assurance_id, numero_sinistre, franchise, part_assurance, part_client, statut, notes |
| OrdreReparation | `ordres_reparation` | id, numero, client_id, vehicule_id, statut, date_entree, date_sortie_prevue, date_sortie_reelle, kilometrage_entree, description_travaux, diagnostic, mecaniciens_ids (json), montant_ht, montant_tva, montant_ttc, devise_id, taux_applique, acompte, reste_a_payer, mode_paiement, assurance_nom, numero_sinistre, franchise, part_assurance, part_client, created_by, closed_by, notes_internes, deleted_at |
| LigneOrdre | `lignes_ordre` | id, ordre_id, type_ligne, piece_id, designation, quantite, prix_unitaire_ht, remise_pct, montant_ht, ordre_affichage |
| PieceDetachee | `pieces_detachees` | id, reference, designation, categorie, marque, prix_achat, prix_vente, stock_actuel, stock_minimum, unite, emplacement, fournisseur_principal, is_active, deleted_at |
| MouvementStock | `mouvements_stock` | id, piece_id, type (entree/sortie/ajustement/inventaire), quantite, ordre_id, user_id, commentaire |
| InspectionCarrosserie | `inspections_carrosserie` | id, vehicule_id, ordre_id, user_id, kilometrage, code_teinte, zones_choquees, photo_avant, photo_arriere, photo_gauche, photo_droite, date_inspection |
| FormulationPeinture | `formulations_peinture` | id, ordre_id, vehicule_id, user_id, code_teinte, nom, notes |
| ComposantFormulation | `composants_formulation` | id, formulation_id, piece_id, designation, quantite, unite |
| Devise | `devises` | id, code, nom, symbole, taux_vers_xof, decimales, is_default, is_active |
| Parametre | `parametres` | id, cle, valeur, type, groupe, libelle, description |
| NotificationLog | `notifications_logs` | id, ordre_id, client_id, canal, destinataire, type_notification, message, statut, provider_response (json), sent_at |

### Relations
- Client 1..* Vehicule
- Client 1..* OrdreReparation
- Vehicule 1..* OrdreReparation
- Vehicule 1..* InspectionCarrosserie
- OrdreReparation 1..* LigneOrdre
- OrdreReparation 1..* NotificationLog
- OrdreReparation 1..1 Sinistre (optionnel)
- OrdreReparation 1..* FormulationPeinture
- Assurance 1..* Sinistre
- PieceDetachee 1..* LigneOrdre
- PieceDetachee 1..* MouvementStock
- PieceDetachee 1..* ComposantFormulation
- FormulationPeinture 1..* ComposantFormulation
- Devise 1..* OrdreReparation
- Role 1..* User

## 3. RBAC (rôles & permissions)

| Rôle | Permissions clés |
|---|---|
| `gerant` | Accès total (`*`) |
| `receptionniste` | clients (view/create/edit), vehicules (view/create/edit), ordres (view/create/edit), notifications.send, stock.view, reception.inspection |
| `chef_atelier` | ordres (view/edit/assign), stock (view/sortie), vehicules.view, reception.inspection |
| `peintre` | ordres (view_assigned/update_status), stock (view/sortie), vehicules.view, formulations (create) |
| `mecanicien` | ordres (view_assigned/update_status), vehicules.view |
| `comptable` | ordres (view/facturer), clients (view/credit), rapports.view, paiements.manage, facturation.update |

Implémentation : modèle `Role` custom + Policies (`OrdreReparationPolicy`, `ClientPolicy`, `PieceDetacheePolicy`) + `AuthServiceProvider`.

## 4. Services

### CurrencyConverterService
- `convert(float $amount, string $from, string $to): float` — pivot XOF.
- `format(float $amount, string $currencyCode): string`.
- `getDefaultCurrency(): ?Devise`.

### NotificationChannelInterface (Strategy)
- `send(string $to, string $message, array $options): array`
- `getName(): string`
- `isAvailable(): bool`
- Implémentations : `WhatsAppUltraMsgChannel`, `WhatsAppTwilioChannel`, `SmsOrangeChannel`, `SmsAfricasTalkingChannel`, `EmailChannel`.
- Orchestration : `NotificationServiceProvider` enregistre le premier canal disponible par type + Email en fallback.

## 5. Workflow carrosserie & peinture

Statuts : `brouillon → devis → accepte → en_cours → termine → facture → livre` (+ `annule`).

Sous-étapes (stepper UI) : Expertise/Devis, Tôlerie/Débosselage, Préparation/Masticage, Cabine Peinture/Vernis, Mécanique rapide/Finition, Prêt pour livraison.

Transitions validées côté serveur (`OrdreReparationController::transitionStatutAutorisee`).

## 6. Multi-devises & paiements
- Devise par défaut : XOF (0 décimale), affichage `number_format($m, 0, ',', ' ') . ' F CFA'`.
- Support XOF, XAF, EUR, USD avec taux configurables (`devises.taux_vers_xof`).
- Moyens de paiement : especes, orange_money, wave, moov, virement, assurance.
- Suivi acompte / reste à payer / crédits clients.

## 7. Inspection carrosserie 360°
- Relevé obligatoire à la réception : 4 angles photo + zones choquées + code teinte constructeur + kilométrage.
- Stockage photos : `storage/app/public/inspections`.
- Modèle `InspectionCarrosserie` lié au véhicule (et à l'OR si créé).

## 8. Consommables peinture
- Pièces génériques (`pieces_detachees`) avec unité g/ml/kg/l.
- `FormulationPeinture` + `ComposantFormulation` pour la fiche de formulation de teinte (composants en g/ml).
- Mouvements de stock typés (entree/sortie/ajustement/inventaire) avec protection stock négatif.

## 9. Sécurité
- CSRF : `@csrf` sur tous les formulaires.
- XSS : échappement Blade `{{ }}`.
- IDOR : mécanicien ne voit que ses OR (`whereJsonContains mecaniciens_ids`).
- Uploads : validation MIME (`image`) + taille max 5 Mo.
- Autorisation : Policies + `authorize()` dans les contrôleurs.