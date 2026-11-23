<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Présentation — Garage Moderne</title>
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1A1A2E; margin: 20px; }
    h1 { color: #0F2942; font-size: 22px; border-bottom: 3px solid #F97316; padding-bottom: 6px; }
    h2 { color: #0F2942; font-size: 16px; margin-top: 16px; border-left: 4px solid #F97316; padding-left: 8px; }
    h3 { color: #0F2942; font-size: 13px; margin-top: 10px; }
    p { line-height: 1.5; margin: 6px 0; }
    ul { margin: 4px 0 8px 18px; }
    li { margin: 2px 0; }
    table { width: 100%; border-collapse: collapse; margin: 8px 0; font-size: 10px; }
    th, td { border: 1px solid #ccc; padding: 4px 6px; }
    th { background: #0F2942; color: #fff; }
    tr:nth-child(even) { background: #F0F4F8; }
    .badge { display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 9px; color: #fff; }
    .ok { background: #198754; }
    .warn { background: #F97316; }
    .kpi { display: inline-block; margin: 4px; padding: 8px 12px; background: #F0F4F8; border-radius: 8px; text-align: center; }
    .kpi .v { font-size: 18px; font-weight: bold; color: #0F2942; }
    .kpi .l { font-size: 9px; color: #6C757D; }
    .page-break { page-break-after: always; }
    .footer { text-align: center; font-size: 9px; color: #6C757D; margin-top: 20px; }
</style>
</head>
<body>

<h1>Garage Moderne — Présentation du projet</h1>
<p><strong>Application web de gestion d'atelier de tôlerie, peinture automobile et mécanique rapide</strong><br>
Marché ouest-africain (UEMOA/CEMAC) — XAMPP / Laravel / Bootstrap 5</p>

<div class="kpi"><div class="v">19</div><div class="l">Tests</div></div>
<div class="kpi"><div class="v">32</div><div class="l">Assertions</div></div>
<div class="kpi"><div class="v">0</div><div class="l">Erreurs PHPStan</div></div>
<div class="kpi"><div class="v">15</div><div class="l">Migrations</div></div>
<div class="kpi"><div class="v">10+</div><div class="l">Modèles</div></div>

<h2>1. Présentation brève (Executive Summary)</h2>
<p><strong>Garage Moderne</strong> est une application web responsive (mobile-first) destinée aux ateliers de tôlerie, peinture et mécanique rapide en Afrique de l'Ouest. Elle digitalise l'ensemble du parcours client : réception véhicule, inspection 360°, devis, suivi atelier par étapes, facturation multi-devises et assurances, gestion de stock pièces et consommables peinture, notifications WhatsApp/SMS.</p>

<h3>Proposition de valeur</h3>
<ul>
    <li><strong>Traçabilité complète</strong> : de la réception à la livraison, chaque véhicule est suivi par étapes (tôlerie, préparation, cabine peinture, mécanique rapide, finition).</li>
    <li><strong>Inspection 360°</strong> : relevé photographique obligatoire (4 angles + zones choquées + code teinte) pour éviter les litiges.</li>
    <li><strong>Multi-devises & assurances</strong> : XOF/XAF/EUR/USD, double payeur (assurance + client/franchise), acomptes, crédits.</li>
    <li><strong>Consommables peinture</strong> : suivi au gramme/millilitre avec fiche de formulation de teinte.</li>
    <li><strong>Mobile-first</strong> : utilisable sur parking et en atelier (smartphone/tablette), haute visibilité (Bleu Acier #0F2942 / Orange Sécurité #F97316).</li>
    <li><strong>Notifications automatiques</strong> : WhatsApp/SMS (devis prêt, véhicule terminé, rappels).</li>
</ul>

<h3>Stack technique</h3>
<ul>
    <li>Serveur : XAMPP (Apache, MySQL/MariaDB, PHP 8.3+)</li>
    <li>Backend : Laravel 13, Eloquent, Form Requests, Policies</li>
    <li>Frontend : Blade, Bootstrap 5, Vite, PWA</li>
    <li>Qualité : PHPUnit (19 tests), Laravel Pint (PSR-12), Larastan/PHPStan (niveau 5)</li>
    <li>Versioning : Git & GitHub (CI GitHub Actions)</li>
</ul>

<h3>État d'avancement</h3>
<p><span class="badge ok">Livrés</span> UI/UX, backend core, multi-devises, notifications, PDF, RBAC, tests, CI, seeders démo. <span class="badge warn">Optionnel</span> Tests Dusk, audit sécurité avancé, déploiement production.</p>

<div class="page-break"></div>

<h2>2. Présentation détaillée</h2>

<h3>2.1 Module 1 — Business Plan & Stratégie</h3>
<p><strong>Marché :</strong> ateliers de carrosserie/peinture en zone UEMOA/CEMAC (Mali, Sénégal, Côte d'Ivoire, Burkina Faso, etc.).</p>
<p><strong>Modèle :</strong> Licence On-Premise (XAMPP local, données chez le client) avec option SaaS hébergé. Paiements Mobile Money (Orange Money, Wave, Moov) et espèces.</p>
<p><strong>Analyse financière :</strong> devises XOF (par défaut, 0 décimale), XAF, EUR, USD. Taux configurables. Suivi acomptes et créances clients.</p>

<h3>2.2 Module 2 — Identité visuelle & UI/UX</h3>
<ul>
    <li>Charte : Bleu Acier #0F2942, Orange Sécurité #F97316, contrastes élevés.</li>
    <li>Mobile-first : sidebar desktop, topbar + bottom nav mobile, PWA (manifest + service worker).</li>
    <li>Écrans clés : Tableau de bord (KPI), Réception/Inspection 360°, Ordres (stepper workflow), Facturation/Assurance, Stock & Laboratoire peinture, Paramètres.</li>
</ul>

<h3>2.3 Module 3 — Cahier des Charges Technique (MCD/MLD)</h3>
<p>16 entités : User, Role, Client, Vehicule, Assurance, Sinistre, OrdreReparation, LigneOrdre, PieceDetachee, MouvementStock, InspectionCarrosserie, FormulationPeinture, ComposantFormulation, Devise, Parametre, NotificationLog.</p>
<table>
    <tr><th>Entité</th><th>Table</th><th>Rôle</th></tr>
    <tr><td>OrdreReparation</td><td>ordres_reparation</td><td>Cœur métier : devis, statuts, montants, devise, assurance</td></tr>
    <tr><td>InspectionCarrosserie</td><td>inspections_carrosserie</td><td>4 photos + zones choquées + code teinte</td></tr>
    <tr><td>Sinistre</td><td>sinistres</td><td>Dossier assurance (double payeur)</td></tr>
    <tr><td>FormulationPeinture</td><td>formulations_peinture</td><td>Fiche de formulation de teinte</td></tr>
    <tr><td>ComposantFormulation</td><td>composants_formulation</td><td>Composants en g/ml</td></tr>
    <tr><td>Devise</td><td>devises</td><td>XOF/XAF/EUR/USD + taux</td></tr>
    <tr><td>NotificationLog</td><td>notifications_logs</td><td>Journal des envois WhatsApp/SMS/Email</td></tr>
</table>

<h3>RBAC (6 rôles)</h3>
<table>
    <tr><th>Rôle</th><th>Permissions clés</th></tr>
    <tr><td>Gérant</td><td>Accès total (*)</td></tr>
    <tr><td>Réceptionniste</td><td>Clients, véhicules, ordres, notifications, réception 360°</td></tr>
    <tr><td>Chef d'atelier</td><td>Ordres (édit/assign), stock, réception</td></tr>
    <tr><td>Peintre</td><td>Ordres assignés, stock sortie, formulations</td></tr>
    <tr><td>Mécanicien</td><td>Ordres assignés, mise à jour statut</td></tr>
    <tr><td>Comptable</td><td>Facturation, crédits, rapports, paiements</td></tr>
</table>

<h3>Services</h3>
<ul>
    <li><strong>CurrencyConverterService</strong> : conversion via pivot XOF, formatage selon décimales.</li>
    <li><strong>NotificationChannelInterface</strong> (Strategy) : WhatsApp (UltraMsg, Twilio), SMS (Orange, Africa's Talking), Email (fallback).</li>
    <li><strong>PdfService</strong> : génération devis/facture (DomPDF).</li>
</ul>

<h3>Workflow carrosserie & peinture</h3>
<p>Statuts : brouillon → devis → accepté → en cours → terminé → facturé → livré (+ annulé).<br>
Stepper : Expertise/Devis → Tôlerie/Débosselage → Préparation/Masticage → Cabine Peinture/Vernis → Mécanique rapide/Finition → Prêt pour livraison.</p>

<h3>2.4 Module 4 — Installation XAMPP</h3>
<ul>
    <li>VirtualHost `garage-app.local` → `C:/xampp/htdocs/garage/public` (voir `docs/xampp-vhost.conf`).</li>
    <li>php.ini : upload_max_filesize=10M, post_max_size=12M.</li>
    <li>Commandes : composer install, npm install && npm run build, php artisan migrate --seed, php artisan storage:link.</li>
    <li>Compte admin : admin@garage-moderne.local / password.</li>
</ul>

<h3>2.5 Module 5 — Tests & GitHub</h3>
<ul>
    <li>Pint (PSR-12) : clean.</li>
    <li>PHPStan niveau 5 (Larastan) : 0 erreur.</li>
    <li>PHPUnit : 19 tests, 32 assertions (conversion monétaire, notifications, PDF, persistance modules).</li>
    <li>Sécurité : CSRF, XSS, IDOR (mécanicien = ses OR), validation uploads (MIME, taille).</li>
    <li>CI GitHub Actions : MySQL + Pint + tests.</li>
    <li>Git : repo initialisé et commité.</li>
</ul>

<h3>2.6 Données de démonstration</h3>
<p>Seeder `FullDemoSeeder` : clients, véhicules, pièces, inspections 360°, ordres (devis + facture assurance), formulations peinture (g/ml), logs de notification. Toutes les tables de migration sont peuplées.</p>

<h3>2.7 Roadmap (optionnel)</h3>
<ul>
    <li>Tests UI Dusk (navigateur).</li>
    <li>Audit sécurité avancé (OWASP).</li>
    <li>Déploiement production (hébergement, sauvegardes).</li>
    <li>Intégration API WhatsApp Business réelle (Meta Cloud API).</li>
</ul>

<div class="footer">
    Garage Moderne — Présentation projet — Généré le {{ date('d/m/Y') }}
</div>
</body>
</html>