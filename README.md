# WP Gutenberg

Projet WordPress basé sur l'éditeur de blocs (Gutenberg) : Composer pour WordPress et les plugins, thème PHP natif + blocs Gutenberg dynamiques, Vite + Tailwind v4.

Documentation du thème (ajout de blocs, conventions) : [`public/app/themes/custom-theme/README.md`](public/app/themes/custom-theme/README.md).

## Prérequis

- PHP >= 8.2, Composer
- Node.js >= 22, pnpm
- MariaDB / MySQL, Nginx ou Apache
- WordPress >= 6.8 (installé via Composer)

## Installation

```bash
cp .env.example .env            # puis renseigner DB_*, WP_HOME, salts
composer install                # WordPress (public/wordpress) + plugins (public/app/plugins)
cd public/app/themes/custom-theme
pnpm install
pnpm build                      # ou `pnpm dev` en développement
```

Base de données locale (voir `/var/www/sites/README-local-wp.md`) :

```bash
sudo mysql -e "CREATE DATABASE wp_gutenberg DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'wp_gutenberg'@'localhost' IDENTIFIED BY 'MOT_DE_PASSE';
GRANT ALL PRIVILEGES ON wp_gutenberg.* TO 'wp_gutenberg'@'localhost'; FLUSH PRIVILEGES;"
```

Puis `https://wp-gutenberg.local/wordpress/wp-admin/install.php` et activer le thème **WP Gutenberg**.

## Structure

```
wp-gutenberg/
├── public/
│   ├── index.php / wp-config.php   (lit .env)
│   ├── wordpress/                  Core (Composer, non versionné)
│   └── app/
│       ├── mu-plugins/             Restrictions client, protection home, désactivation commentaires…
│       ├── plugins/                Plugins (Composer, non versionnés)
│       └── themes/custom-theme/    Thème
├── composer.json
└── .env
```

## Plugins

| Plugin | Statut |
| --- | --- |
Uniquement des plugins gratuits, installés depuis wordpress.org via [wpackagist](https://wpackagist.org) (aucun identifiant requis) :

| Plugin | Rôle |
| --- | --- |
| SEOPress (gratuit) | SEO — blocs `wpseopress/faq-block` et `wpseopress/sitemap` autorisés dans l'éditeur |
| Matomo | Statistiques |
| Relevanssi | Recherche |
| Simple History | Journal d'activité |
| Wordfence | Sécurité |
| WP Health | Santé du site |

Ajouter un plugin : `composer require wpackagist-plugin/<slug>`.

## Production

```bash
composer install --no-dev --optimize-autoloader
cd public/app/themes/custom-theme && pnpm install --frozen-lockfile && pnpm build
```
