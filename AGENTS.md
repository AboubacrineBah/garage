@"
# Contexte du Projet — Gestion d'Atelier Tôlerie, Peinture & Mécanique Rapide

- **Environnement local :** XAMPP (``C:\xampp\htdocs\mecaflow-garage``), PHP 8.2+, Apache, MySQL/MariaDB.
- **Domaine local :** ``http://garage-app.local`` (ou ``http://localhost/mecaflow-garage/public``).
- **Stack technique :** Laravel (dernière version stable), Blade, Bootstrap 5, Vite, NPM, Composer.
- **Devise par défaut :** XOF (Franc CFA BCEAO, 0 décimale), support multi-devises (XOF, XAF, EUR, USD).
- **Paiements locaux :** Espèces, Orange Money, Wave, Moov Money, Virements, Prises en charge Assurances.
- **Skill métier principal :** Utilise systématiquement le skill ``garage-carrosserie-uemoa`` pour toute tâche d'architecture, de génération de code, de migration, de test ou de configuration.
- **Commandes de vérification :**
  - Qualité code : ``./vendor/bin/pint``
  - Analyse statique : ``./vendor/bin/phpstan analyse``
  - Tests : ``php artisan test``
"@ | Out-File -FilePath "AGENTS.md" -Encoding utf8

<laravel-boost-guidelines>
# Laravel Application

This repository contains a Laravel application. Complete the following setup before working on the user's request.

## Prerequisites

Verify that PHP and Composer are available:

```sh
php -v
composer -V
```

If either command is unavailable, detect the user's operating system and install the prerequisites with the appropriate command:

macOS:

```sh
/bin/bash -c "$(curl -fsSL https://php.new/install/mac/8.5)"
```

Windows PowerShell:

```powershell
Set-ExecutionPolicy Bypass -Scope Process -Force; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://php.new/install/windows/8.5'))
```

Linux:

```sh
/bin/bash -c "$(curl -fsSL https://php.new/install/linux/8.5)"
```

After installation, ask the user to restart their terminal. If the agent needs the restarted shell to continue, ask the user to reopen their terminal and rerun their original prompt.

## Agent Setup

Install Laravel Boost from the application root before making application changes:

```sh
composer require laravel/boost --dev
php artisan boost:install
```

Boost replaces these bootstrap instructions with guidelines tailored to the application. After installation, read `AGENTS.md` again and continue with the user's original request using the generated guidelines.
</laravel-boost-guidelines>

