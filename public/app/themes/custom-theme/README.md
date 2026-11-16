# WP Gutenberg

Thème hybride : **templates PHP natifs** pour la structure (header, footer, archives) et **blocs Gutenberg dynamiques** pour le contenu. Le client édite ses pages dans l'éditeur de blocs avec le **vrai rendu du site** (même CSS, mêmes classes).

## Commandes

```bash
pnpm install
pnpm dev                      # Vite (HMR front) + wp-scripts (blocs) en parallèle
pnpm build                    # build de prod : blocs puis Vite
pnpm block:new <slug>         # crée un bloc (options : --title="Titre" --inner --view)
pnpm i18n                     # régénère languages/wp-gutenberg.pot
```

## Structure

```
custom-theme/
├── functions.php            Autoloader PSR-4 + boot des services
├── theme.json               Design tokens (couleurs, espacements, largeurs) + verrouillage de l'éditeur
├── header.php / footer.php  Layout (Barba wrapper)
├── page.php                 Pages = 100 % blocs
├── single.php / index.php / 404.php
├── parts/                   Template parts (get_template_part / wpg_render)
├── patterns/                Compositions de blocs prêtes à l'emploi (auto-enregistrées)
├── blocks/                  ★ Sources des blocs (1 dossier = 1 bloc)
│   └── _shared/             Composants éditeur partagés (sélecteur d'image…)
├── build/                   Blocs compilés par wp-scripts (git-ignoré)
├── config/
│   ├── editor.php           Blocs core / plugins autorisés dans l'éditeur
│   └── socials.php          Réseaux sociaux
├── includes/                Classes PHP (namespace WPGutenberg\)
│   ├── Theme.php            Liste des services
│   ├── Core/                Setup, Assets, Vite, Cleanup, Media
│   ├── Editor/              Blocks (auto-registration), Editor (config éditeur)
│   ├── Admin/               Réglages > Socials
│   ├── Fragments/           API REST des panneaux (menu, filtres)
│   └── helpers.php          wpg_asset, wpg_render, wpg_icon, wpg_menu_items, wpg_attrs…
└── src/
    ├── stylesheets/         Tailwind v4 (styles.css = entrée)
    ├── scripts/             JS global (Barba, Lenis, GSAP) + runtime des blocs
    └── editor/              CSS du canvas éditeur + JS éditeur (styles/variations de blocs)
```

## Ajouter un bloc

```bash
pnpm block:new testimonial --title="Témoignage" --inner --view
```

Crée `blocks/testimonial/` :

| Fichier       | Rôle                                                                                   |
| ------------- | -------------------------------------------------------------------------------------- |
| `block.json`  | Déclaration : nom, attributs, supports (couleurs, padding, align…), assets              |
| `edit.js`     | **Back** : rendu dans l'éditeur (React). Mêmes classes que `render.php`                 |
| `render.php`  | **Front** : HTML final, rendu côté serveur                                              |
| `index.js`    | Enregistrement JS (`edit` + `save`)                                                     |
| `style.css`   | CSS propre au bloc — chargée **seulement** sur les pages qui l'utilisent (front + éditeur) |
| `view.js`     | (option `--view`) JS front — chargé **seulement** sur les pages qui l'utilisent          |

Aucune déclaration PHP : `pnpm dev` / `pnpm build` compile le bloc et génère `build/blocks-manifest.php`, lu par `includes/Editor/Blocks.php`. Le bloc apparaît dans la catégorie **Blocs du site** de l'éditeur et est autorisé automatiquement.

### Règles de construction

1. **Blocs dynamiques uniquement** (`render.php`) : on peut modifier le HTML d'un bloc sans casser les contenus existants (pas d'erreur « Ce bloc contient du contenu invalide »). `save` ne renvoie que `<InnerBlocks.Content />` ou `null`.
2. **`edit.js` et `render.php` partagent les mêmes classes Tailwind** : c'est ce qui garantit le WYSIWYG. Les comportements front (parallaxe, split text, compteurs) ne sont pas rejoués dans l'éditeur.
3. **Images = ID d'attachement** (`mediaId`), jamais d'URL. Front : `wp_get_attachment_image()` (srcset, sizes, lazy, WebP). Éditeur : `useMedia()` de `_shared/media.js`.
4. **Couleurs / espacements** : préférer les `supports` natifs (`color`, `spacing`) dans `block.json` — WordPress génère l'UI à partir de `theme.json` et ajoute les classes via `useBlockProps()` / `get_block_wrapper_attributes()`.
5. **Contenu riche** : utiliser `InnerBlocks` avec des blocs core (titres, paragraphes, boutons) plutôt que des champs texte.
6. **JS front** d'un bloc interactif :
   ```js
   // blocks/<slug>/view.js
   (window.wpgBlocks ??= []).push(['<slug>', (el) => { /* init */ return () => { /* cleanup */ }; }]);
   ```
   ```php
   // render.php
   get_block_wrapper_attributes( array( 'data-wpg-block' => '<slug>' ) )
   ```
   `setup(el)` est appelé pour chaque instance, y compris après une navigation Barba ; le cleanup est appelé quand on quitte la page.

## Design tokens

Une seule source : `theme.json`.

1. Ajouter la couleur dans `settings.color.palette` → elle apparaît dans l'éditeur.
2. La mapper dans `src/stylesheets/theme.css` (`--color-xxx: var(--wp--preset--color--xxx)`) → classes `bg-xxx`, `text-xxx` côté Tailwind.

Les espacements `sm / md / lg` sont exposés en `py-section-sm|md|lg`.

## Performance

- Pas de moteur de template : PHP natif, aucun cache Twig à compiler.
- CSS/JS des blocs (core et custom) chargés **uniquement** s'ils sont présents dans la page (`should_load_separate_core_block_assets`, `style`/`viewScriptModule` dans `block.json`). Barba synchronise ces assets entre les pages (`src/scripts/core/sync-assets.js`).
- Enregistrement des blocs via manifeste PHP compilé (pas de lecture de `block.json` à chaque requête).
- Utilitaires Tailwind hors cascade layer pour primer sur les styles WordPress sans `!important`.
- Images : qualité 82 + sous-tailles WebP générées à l'upload.
- jQuery n'est plus désenregistré globalement : WordPress ne le charge que si un plugin en dépend .

## Éditeur (verrouillage client)

- `config/editor.php` : liste blanche des blocs core et plugins proposés.
- `theme.json` : pas de couleurs/tailles/espacements libres, uniquement les presets.
- `src/editor/index.js` : suppression des styles de blocs core inutiles, ajout de styles projet (`registerBlockStyle`).
- Patterns distants, Openverse et Block Directory désactivés.
