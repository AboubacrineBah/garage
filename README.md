# Garage Moderne – Code source de base

Structure prête à intégrer dans un projet Laravel (dernière version stable).

## Contenu actuel

```
garage-moderne/
├── app/
│   ├── Contracts/
│   │   └── NotificationChannelInterface.php
│   ├── Models/                  # 10 modèles Eloquent
│   ├── Providers/
│   │   └── NotificationServiceProvider.php
│   └── Services/
│       ├── CurrencyConverterService.php
│       ├── NotificationService.php
│       └── Channels/
│           ├── WhatsAppUltraMsgChannel.php
│           ├── WhatsAppTwilioChannel.php
│           ├── SmsOrangeChannel.php
│           ├── SmsAfricasTalkingChannel.php
│           └── EmailChannel.php
├── config/
│   └── services_notifications.php   # À fusionner dans config/services.php
├── database/
│   ├── migrations/              # 10 migrations
│   └── seeders/
└── README.md
```

## Canaux de notification implémentés

| Canal              | Provider              | Priorité | Variables .env principales                          |
|--------------------|-----------------------|----------|-----------------------------------------------------|
| WhatsApp           | UltraMsg              | 1        | `ULTRAMSG_INSTANCE_ID`, `ULTRAMSG_TOKEN`            |
| WhatsApp           | Twilio                | 2        | `TWILIO_SID`, `TWILIO_TOKEN`, `TWILIO_WHATSAPP_FROM`|
| SMS                | Orange SMS API        | 1        | `ORANGE_SMS_CLIENT_ID`, `ORANGE_SMS_CLIENT_SECRET`  |
| SMS                | Africa's Talking      | 2        | `AFRICASTALKING_USERNAME`, `AFRICASTALKING_API_KEY` |
| Email              | Laravel Mailer        | Fallback | Configuration `MAIL_*` standard                     |

Le `NotificationServiceProvider` enregistre automatiquement le premier canal disponible pour chaque type (WhatsApp puis SMS) + Email en fallback.

### Exemple d’utilisation

```php
use App\Services\NotificationService;
use App\Models\OrdreReparation;

// Dans un contrôleur ou un Job
public function cloturer(OrdreReparation $ordre, NotificationService $notifier)
{
    $ordre->update(['statut' => 'termine', 'closed_by' => auth()->id()]);

    // Envoie WhatsApp (ou SMS selon disponibilité)
    $notifier->notifierVehiculeTermine($ordre, 'whatsapp');
}
```

## Installation rapide

1. Créer un projet Laravel :
   ```bash
   composer create-project laravel/laravel garage-app
   cd garage-app
   ```

2. Copier les dossiers `app/`, `database/` et le contenu de `config/services_notifications.php` dans le projet.

3. Enregistrer le provider :
   - Laravel 11+ : ajouter dans `bootstrap/providers.php`
   - Laravel 10 : ajouter dans `config/app.php` → `providers`

4. Configurer `.env` (voir Module 4 du PDF + variables des canaux ci-dessus).

5. Lancer :
   ```bash
   php artisan migrate
   php artisan db:seed
   php artisan storage:link
   ```

6. Compte admin par défaut :
   - Email : `admin@garage-moderne.local`
   - Mot de passe : `password` (**à changer immédiatement**)

## Prochaines étapes recommandées

1. Créer les contrôleurs + Form Requests + Policies
2. Développer les vues Blade mobile-first (tableau de bord + ordre de réparation rapide)
3. Ajouter Spatie Laravel Permission (optionnel mais recommandé)
4. Écrire les tests PHPUnit (conversion monétaire + notification)

Document de référence : `Garage_Moderne_Modules_1_a_5_Complet.pdf`

## Contrôleurs, Form Requests & Vues (ajoutés)

- **Contrôleurs** : DashboardController, OrdreReparationController, ClientController
- **Form Requests** : StoreOrdreReparationRequest, UpdateOrdreStatutRequest, StoreClientRequest
- **Vues Blade mobile-first** :
  - Layout principal avec bottom navigation (Bootstrap 5)
  - Tableau de bord (KPI + derniers ordres)
  - Liste / création rapide / détail des ordres
  - Liste / création / édition / détail clients
- **Routes** : `routes/web_garage.php` (à inclure dans `web.php`)

## Prochaines étapes recommandées

1. Ajouter Spatie Laravel Permission + Policies
2. Module Stock (contrôleur + vues)
3. Génération PDF devis/facture
4. Écrire les tests PHPUnit

## Module Stock (ajouté)

- **Contrôleur** : `StockController`
- **Form Requests** : `StorePieceRequest`, `StoreMouvementStockRequest`
- **Vues** : index, alertes, mouvement (entrée/sortie/ajustement), create, edit, show
- **Fonctionnalités** :
  - Liste filtrable (recherche, catégorie, stock critique)
  - Alertes stock (seuil minimum)
  - Entrées / sorties / ajustements avec historique
  - Protection contre stock négatif
  - Lien depuis la navigation mobile et desktop

## PDF Devis / Facture

- **Service** : `App\Services\PdfService`
- **Vue** : `resources/views/pdf/ordre.blade.php`
- **Routes** :
  - `GET /ordres/{ordre}/pdf/devis` → téléchargement devis
  - `GET /ordres/{ordre}/pdf/facture` → téléchargement facture
- **Prérequis** : `composer require barryvdh/laravel-dompdf`
- Boutons PDF ajoutés sur la fiche ordre

## Policies & Spatie Permission

- **Policies** : `OrdreReparationPolicy`, `ClientPolicy`, `PieceDetacheePolicy`
- **AuthServiceProvider** fourni pour l’enregistrement
- **Guide complet** : `docs/SPATIE_PERMISSION.md`

## Tests PHPUnit

- `tests/Unit/CurrencyConverterServiceTest.php` — conversions XOF/EUR/USD/XAF + formatage
- `tests/Feature/OrdreNotificationTest.php` — notification à la clôture + log
- `tests/Feature/PdfOrdreTest.php` — génération HTML devis/facture

Lancer : `php artisan test`

## Module Paramètres atelier

- Migration `parametres` (clé/valeur typée)
- Modèle `Parametre` avec cache
- Seeder (coordonnées + templates WhatsApp/SMS)
- Interface `/parametres` (groupes atelier, facturation, notifications)
- PDF et notifications utilisent ces paramètres

## PWA / mode hors-ligne partiel

- `public/manifest.json`
- `public/sw.js` (Network-first pages, Cache-first assets)
- `public/offline.html`
- Meta tags + enregistrement SW dans le layout
- Icônes à placer dans `public/icons/icon-192.png` et `icon-512.png`
