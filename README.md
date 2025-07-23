<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Logo Laravel"></a></p>

# Base Project Laravel

Ce projet est un squelette d’application web basé sur le framework **Laravel 12** (PHP ^8.2), enrichi de nombreux packages pour accélérer le développement d’applications modernes, multilingues et robustes.

## Fonctionnalités principales

- Authentification complète (Livewire, gestion des emails, vérification, réinitialisation, etc.)
- Gestion des rôles et permissions (spatie/laravel-permission)
- Upload et gestion de médias (spatie/laravel-medialibrary)
- SEO (artesaos/seotools)
- Génération de PDF (barryvdh/laravel-dompdf)
- Notifications toast (devrabiul/laravel-toaster-magic)
- Consentement cookies, honeypot anti-spam, backup, sitemap, recherche, etc. (Spatie)
- Actions Laravel (lorisleiva/laravel-actions)
- Prise en charge du français (laravel-lang/common)
- Frontend moderne avec **Vite** et **Tailwind CSS 4**
- Composants Livewire 3
- Outils de développement : Debugbar, Pest, Pint, Sail, etc.

## Stack technique

### Backend (PHP)

- **Laravel 12**
- PHP ^8.2
- Livewire ^3.6
- spatie/laravel-permission, spatie/laravel-medialibrary, spatie/laravel-backup, spatie/laravel-cookie-consent, spatie/laravel-honeypot, spatie/laravel-searchable, spatie/laravel-sitemap
- barryvdh/laravel-dompdf
- artesaos/seotools
- lorisleiva/laravel-actions
- devrabiul/laravel-toaster-magic
- laravel-lang/common

### Frontend (JS)

- **Vite** ^6.2.4
- **Tailwind CSS** ^4.0.0 (+ plugins forms, typography)
- laravel-vite-plugin
- @tailwindcss/vite
- concurrently (pour le dev multi-processus)

### Outils de développement

- **Pest** (tests)
- **Laravel Debugbar**
- **Laravel Pint** (formatage)
- **Laravel Sail** (environnement Docker)
- **Wulfheart Laravel Actions IDE Helper**
- **Faker** (génération de données de test)
- **Mockery** (mocks pour tests)

## Installation

1. **Cloner le dépôt**
2. Installer les dépendances PHP :
   ```bash
   composer install
   ```
3. Installer les dépendances JS :
   ```bash
   npm install
   ```
4. Copier le fichier d’environnement :
   ```bash
   cp .env.example .env
   ```
5. Générer la clé d’application :
   ```bash
   php artisan key:generate
   ```
6. Lancer les migrations :
   ```bash
   php artisan migrate
   ```
7. Lancer le serveur de développement :
   ```bash
   npm run dev
   # ou
   composer dev
   ```

## Scripts utiles

- `npm run dev` : Lance Vite en mode développement
- `npm run build` : Build de production
- `composer dev` : Lance serveur PHP, queue, logs et Vite en parallèle (voir scripts composer)
- `composer test` : Lance les tests

## Structure du projet

- `app/` : Code applicatif Laravel (contrôleurs, modèles, Livewire, etc.)
- `resources/` : Vues Blade, assets CSS/JS
- `routes/` : Fichiers de routes
- `config/` : Fichiers de configuration
- `database/` : Migrations, seeders, factories
- `public/` : Fichiers accessibles publiquement

## Localisation

Le projet est prêt pour le français (`lang/fr/` et `fr.json`).

## Contribution

Merci de consulter la documentation Laravel et les conventions du projet avant toute contribution.

## Licence

Ce projet est sous licence MIT.
