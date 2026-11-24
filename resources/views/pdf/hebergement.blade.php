<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Hébergement VPS Hetzner — Garage Moderne</title>
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
    .danger { background: #dc3545; }
    .kpi { display: inline-block; margin: 4px; padding: 8px 12px; background: #F0F4F8; border-radius: 8px; text-align: center; }
    .kpi .v { font-size: 18px; font-weight: bold; color: #0F2942; }
    .kpi .l { font-size: 9px; color: #6C757D; }
    .page-break { page-break-after: always; }
    .footer { text-align: center; font-size: 9px; color: #6C757D; margin-top: 20px; }
    .highlight { background: #FFF3CD; padding: 8px; border-radius: 6px; border-left: 4px solid #F97316; margin: 8px 0; }
</style>
</head>
<body>

<h1>Proposition d'Hébergement VPS Hetzner</h1>
<p><strong>Application Garage Moderne — Tôlerie, Peinture & Mécanique Rapide</strong><br>
Hébergement sécurisé, haute disponibilité, tarification en Francs CFA (XOF)</p>

<div class="kpi"><div class="v">3</div><div class="l">Offres</div></div>
<div class="kpi"><div class="v">100%</div><div class="l">Sécurité</div></div>
<div class="kpi"><div class="v">99.9%</div><div class="l">Uptime</div></div>
<div class="kpi"><div class="v">24/7</div><div class="l">Monitoring</div></div>

<h2>1. Contexte & Objectifs</h2>
<p>L'application Garage Moderne nécessite un hébergement fiable, sécurisé et évolutif pour gérer les données sensibles des clients (informations personnelles, véhicules, facturation, assurances). Le choix se porte sur <strong>Hetzner Cloud</strong>, réputé pour son excellent rapport qualité/prix et ses datacenters européens (Allemagne, Finlande).</p>

<h3>Exigences métier</h3>
<ul>
    <li>Disponibilité 24/7 pour les ateliers (pas d'interruption en heures de travail).</li>
    <li>Sécurité des données clients et financières (RGPD-friendly, chiffrement).</li>
    <li>Sauvegardes automatiques quotidiennes.</li>
    <li>Évolutivité : pouvoir augmenter les ressources sans migration.</li>
    <li>Coût maîtrisé en FCFA (zone UEMOA).</li>
</ul>

<h2>2. Architecture proposée</h2>
<table>
    <tr><th>Composant</th><th>Technologie</th><th>Rôle</th></tr>
    <tr><td>Serveur web</td><td>Nginx + PHP-FPM 8.3</td><td>Servir l'application Laravel</td></tr>
    <tr><td>Base de données</td><td>MySQL 8.0 (ou MariaDB)</td><td>Données clients, ordres, stock</td></tr>
    <tr><td>Cache / Sessions</td><td>Redis</td><td>Cache, sessions, files d'attente</td></tr>
    <tr><td>Files d'attente</td><td>Laravel Queue (Redis)</td><td>Notifications WhatsApp/SMS asynchrones</td></tr>
    <tr><td>SSL/TLS</td><td>Let's Encrypt (Certbot)</td><td>HTTPS gratuit, renouvellement auto</td></tr>
    <tr><td>Firewall</td><td>UFW + Hetzner Cloud Firewall</td><td>Filtrage réseau</td></tr>
    <tr><td>Monitoring</td><td>Uptime Kuma / Hetzner Monitoring</td><td>Alertes disponibilité</td></tr>
    <tr><td>Sauvegardes</td><td>Hetzner Snapshots + mysqldump</td><td>Quotidiennes, rétention 7-30j</td></tr>
    <tr><td>Stockage fichiers</td><td>Volume Hetzner (optionnel)</td><td>Photos véhicules, documents</td></tr>
</table>

<div class="page-break"></div>

<h2>3. Offres VPS Hetzner</h2>
<p>Conversion : 1 EUR = 655,957 FCFA (taux BCEAO indicatif). Prix mensuels Hetzner Cloud (HT, hors taxes).</p>

<h3>Offre Starter — Petits ateliers (1-3 mécaniciens)</h3>
<table>
    <tr><th>Élément</th><th>Détail</th><th>Prix EUR/mois</th><th>Prix FCFA/mois</th></tr>
    <tr><td>VPS CX11</td><td>2 vCPU, 4 Go RAM, 40 Go SSD</td><td>4,51 €</td><td>2 958 FCFA</td></tr>
    <tr><td>Sauvegardes auto (20%)</td><td>Snapshots quotidiens</td><td>0,90 €</td><td>590 FCFA</td></tr>
    <tr><td>IPv4 dédiée</td><td>1 adresse</td><td>1,00 €</td><td>656 FCFA</td></tr>
    <tr><td>Let's Encrypt SSL</td><td>Certificat HTTPS</td><td>Gratuit</td><td>0 FCFA</td></tr>
    <tr><td><strong>Total mensuel</strong></td><td></td><td><strong>6,41 €</strong></td><td><strong>4 204 FCFA</strong></td></tr>
    <tr><td><strong>Total annuel</strong></td><td></td><td><strong>76,92 €</strong></td><td><strong>50 448 FCFA</strong></td></tr>
</table>
<p><em>Capacité : jusqu'à 5 utilisateurs simultanés, ~50 ordres/mois, stockage photos basique.</em></p>

<h3>Offre Business — Ateliers moyens (3-10 mécaniciens) <span class="badge ok">Recommandé</span></h3>
<table>
    <tr><th>Élément</th><th>Détail</th><th>Prix EUR/mois</th><th>Prix FCFA/mois</th></tr>
    <tr><td>VPS CX32</td><td>4 vCPU, 8 Go RAM, 80 Go SSD</td><td>10,50 €</td><td>6 888 FCFA</td></tr>
    <tr><td>Sauvegardes auto (20%)</td><td>Snapshots quotidiens</td><td>2,10 €</td><td>1 378 FCFA</td></tr>
    <tr><td>IPv4 dédiée</td><td>1 adresse</td><td>1,00 €</td><td>656 FCFA</td></tr>
    <tr><td>Volume stockage 50 Go</td><td>Photos & documents</td><td>2,50 €</td><td>1 640 FCFA</td></tr>
    <tr><td>Let's Encrypt SSL</td><td>Certificat HTTPS</td><td>Gratuit</td><td>0 FCFA</td></tr>
    <tr><td>Monitoring Hetzner</td><td>Alertes disponibilité</td><td>Gratuit</td><td>0 FCFA</td></tr>
    <tr><td><strong>Total mensuel</strong></td><td></td><td><strong>16,10 €</strong></td><td><strong>10 560 FCFA</strong></td></tr>
    <tr><td><strong>Total annuel</strong></td><td></td><td><strong>193,20 €</strong></td><td><strong>126 720 FCFA</strong></td></tr>
</table>
<p><em>Capacité : jusqu'à 20 utilisateurs simultanés, ~200 ordres/mois, stockage photos complet.</em></p>

<h3>Offre Enterprise — Grands ateliers / multi-sites (10+ mécaniciens)</h3>
<table>
    <tr><th>Élément</th><th>Détail</th><th>Prix EUR/mois</th><th>Prix FCFA/mois</th></tr>
    <tr><td>VPS CX42</td><td>8 vCPU, 16 Go RAM, 160 Go SSD</td><td>18,50 €</td><td>12 135 FCFA</td></tr>
    <tr><td>Sauvegardes auto (20%)</td><td>Snapshots quotidiens</td><td>3,70 €</td><td>2 427 FCFA</td></tr>
    <tr><td>IPv4 dédiée</td><td>1 adresse</td><td>1,00 €</td><td>656 FCFA</td></tr>
    <tr><td>Volume stockage 100 Go</td><td>Photos & documents</td><td>5,00 €</td><td>3 280 FCFA</td></tr>
    <tr><td>Load Balancer</td><td>Haute disponibilité</td><td>5,39 €</td><td>3 526 FCFA</td></tr>
    <tr><td>Let's Encrypt SSL</td><td>Certificat HTTPS</td><td>Gratuit</td><td>0 FCFA</td></tr>
    <tr><td>Monitoring Hetzner</td><td>Alertes disponibilité</td><td>Gratuit</td><td>0 FCFA</td></tr>
    <tr><td><strong>Total mensuel</strong></td><td></td><td><strong>33,59 €</strong></td><td><strong>21 996 FCFA</strong></td></tr>
    <tr><td><strong>Total annuel</strong></td><td></td><td><strong>403,08 €</strong></td><td><strong>264 384 FCFA</strong></td></tr>
</table>
<p><em>Capacité : 50+ utilisateurs simultanés, ~1000 ordres/mois, multi-sites, haute disponibilité.</em></p>

<div class="page-break"></div>

<h2>4. Sécurité — Priorité #1</h2>

<div class="highlight">
    <strong>La sécurité est la priorité absolue.</strong> Toutes les mesures ci-dessous sont incluses dans chaque offre. Aucune compromission sur la protection des données clients et financières.
</div>

<h3>4.1 Sécurité réseau</h3>
<table>
    <tr><th>Mesure</th><th>Description</th><th>Statut</th></tr>
    <tr><td>Hetzner Cloud Firewall</td><td>Filtrage au niveau hyperviseur (avant l'OS). Règles : ports 80/443 ouverts, 22 (SSH) restreint par IP, tout le reste bloqué.</td><td><span class="badge ok">Inclus</span></td></tr>
    <tr><td>UFW (Uncomplicated Firewall)</td><td>Firewall OS-level en complément. Double protection.</td><td><span class="badge ok">Inclus</span></td></tr>
    <tr><td>Fail2Ban</td><td>Blocage automatique des IPs après tentatives SSH/HTTP échouées.</td><td><span class="badge ok">Inclus</span></td></tr>
    <tr><td>SSH par clé uniquement</td><td>Désactivation de l'authentification par mot de passe SSH. Port SSH personnalisé (non 22).</td><td><span class="badge ok">Inclus</span></td></tr>
    <tr><td>DDoS Protection</td><td>Hetzner inclut une protection DDoS de base sur tous les VPS.</td><td><span class="badge ok">Inclus</span></td></tr>
</table>

<h3>4.2 Sécurité applicative (Laravel)</h3>
<table>
    <tr><th>Mesure</th><th>Description</th><th>Statut</th></tr>
    <tr><td>HTTPS obligatoire</td><td>Redirection HTTP → HTTPS. Certificat Let's Encrypt auto-renouvelé.</td><td><span class="badge ok">Inclus</span></td></tr>
    <tr><td>CSRF Protection</td><td>Token CSRF sur tous les formulaires (Blade @csrf).</td><td><span class="badge ok">Inclus</span></td></tr>
    <tr><td>XSS Prevention</td><td>Échappement automatique Blade @{{ }}. Headers X-XSS-Protection.</td><td><span class="badge ok">Inclus</span></td></tr>
    <tr><td>SQL Injection</td><td>Eloquent ORM avec requêtes préparées. Validation Form Requests.</td><td><span class="badge ok">Inclus</span></td></tr>
    <tr><td>RBAC</td><td>6 rôles avec Policies. Mécanicien ne voit que ses ordres (IDOR protection).</td><td><span class="badge ok">Inclus</span></td></tr>
    <tr><td>Rate Limiting</td><td>Throttle sur les routes sensibles (login, API).</td><td><span class="badge ok">Inclus</span></td></tr>
    <tr><td>Headers sécurité</td><td>X-Frame-Options, X-Content-Type-Options, Referrer-Policy, X-XSS-Protection.</td><td><span class="badge ok">Inclus</span></td></tr>
    <tr><td>Upload sécurisé</td><td>Validation MIME (image), taille max 5 Mo, stockage hors webroot.</td><td><span class="badge ok">Inclus</span></td></tr>
</table>

<h3>4.3 Sécurité données</h3>
<table>
    <tr><th>Mesure</th><th>Description</th><th>Statut</th></tr>
    <tr><td>Chiffrement DB</td><td>MySQL avec innodb_file_per_table. Option : chiffrement au repos (LUKS).</td><td><span class="badge ok">Inclus</span></td></tr>
    <tr><td>Sauvegardes chiffrées</td><td>Snapshots Hetzner chiffrés. mysqldump quotidien chiffré (GPG).</td><td><span class="badge ok">Inclus</span></td></tr>
    <tr><td>Isolation utilisateurs</td><td>Chaque atelier a sa propre base de données et son propre VPS (pas de multi-tenant).</td><td><span class="badge ok">Inclus</span></td></tr>
    <tr><td>Logs d'audit</td><td>Journalisation des connexions, modifications sensibles, envois de notifications.</td><td><span class="badge ok">Inclus</span></td></tr>
    <tr><td>Mots de passe</td><td>Bcrypt (12 rounds). Changement obligatoire au premier login admin.</td><td><span class="badge ok">Inclus</span></td></tr>
</table>

<h3>4.4 Maintenance & mises à jour</h3>
<ul>
    <li>Mises à jour automatiques de sécurité OS (unattended-upgrades).</li>
    <li>Mises à jour Laravel via Composer (manuel, testé en staging).</li>
    <li>Rotation des logs (logrotate).</li>
    <li>Renouvellement SSL automatique (Certbot cron).</li>
    <li>Alertes monitoring (email + SMS si disponible).</li>
</ul>

<div class="page-break"></div>

<h2>5. Procédure de déploiement</h2>
<ol>
    <li><strong>Création VPS</strong> : Hetzner Cloud Console → choisir l'offre → datacenter Falkenstein (Allemagne) ou Helsinki.</li>
    <li><strong>Configuration initiale</strong> : SSH par clé, création utilisateur non-root, UFW, Fail2Ban.</li>
    <li><strong>Installation stack</strong> : Nginx, PHP 8.3, MySQL 8, Redis, Composer, Node.js.</li>
    <li><strong>Déploiement app</strong> : git clone, composer install, npm run build, php artisan migrate --seed, php artisan storage:link.</li>
    <li><strong>SSL</strong> : Certbot → certificat Let's Encrypt.</li>
    <li><strong>Configuration queue</strong> : Supervisor pour les workers Laravel (notifications).</li>
    <li><strong>Monitoring</strong> : Uptime Kuma ou Hetzner Monitoring.</li>
    <li><strong>Sauvegardes</strong> : cron mysqldump + Hetzner Snapshots.</li>
    <li><strong>Tests</strong> : vérification fonctionnelle, performance, sécurité.</li>
</ol>

<h2>6. Comparatif des offres</h2>
<table>
    <tr><th>Critère</th><th>Starter</th><th>Business <span class="badge ok">Rec.</span></th><th>Enterprise</th></tr>
    <tr><td>vCPU</td><td>2</td><td>4</td><td>8</td></tr>
    <tr><td>RAM</td><td>4 Go</td><td>8 Go</td><td>16 Go</td></tr>
    <tr><td>Stockage</td><td>40 Go</td><td>80 Go + 50 Go volume</td><td>160 Go + 100 Go volume</td></tr>
    <tr><td>Utilisateurs simultanés</td><td>~5</td><td>~20</td><td>50+</td></tr>
    <tr><td>Ordres/mois</td><td>~50</td><td>~200</td><td>1000+</td></tr>
    <tr><td>Haute dispo</td><td>Non</td><td>Non</td><td>Oui (Load Balancer)</td></tr>
    <tr><td>Prix mensuel (FCFA)</td><td>4 204</td><td>10 560</td><td>21 996</td></tr>
    <tr><td>Prix annuel (FCFA)</td><td>50 448</td><td>126 720</td><td>264 384</td></tr>
</table>

<h2>7. Recommandation</h2>
<div class="highlight">
    <strong>Offre Business (10 560 FCFA/mois)</strong> recommandée pour la majorité des ateliers de carrosserie/peinture en Afrique de l'Ouest. Elle offre le meilleur compromis entre performance, stockage (photos véhicules) et coût. La sécurité est maximale sur les trois offres (firewall, SSL, sauvegardes, RBAC, headers).
</div>

<p><strong>Engagement :</strong> paiement mensuel ou annuel (économie de 2 mois en annuel). Support technique inclus (installation, configuration, mise à jour). Formation à l'utilisation de l'application incluse.</p>

<div class="footer">
    Garage Moderne — Proposition d'hébergement VPS Hetzner — Sécurité prioritaire — Généré le {{ date('d/m/Y') }}
</div>
</body>
</html>