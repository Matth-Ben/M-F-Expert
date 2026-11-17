# M&F Expert — thème (base WP Gutenberg)

Site vitrine de M&F Expert (espace anti-poux, Viriat), maquette **« Flyer v2 »**. Thème hybride : **templates PHP natifs** pour la structure (header, footer, archives) et **blocs Gutenberg dynamiques** pour le contenu. Le client édite ses pages dans l'éditeur de blocs avec le **vrai rendu du site** (même CSS, mêmes classes).

## Commandes

```bash
pnpm install
pnpm dev                      # Vite (HMR front) + wp-scripts (blocs) en parallèle
pnpm build                    # build de prod : blocs puis Vite
pnpm block:new <slug>         # crée un bloc (options : --title="Titre" --inner --view)
pnpm i18n                     # build + .pot + fusion fr_FR.po + .mo + JSON (traduire les entrées vides du .po puis relancer)
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
│   ├── contact.php          Champs de Réglages > Coordonnées (téléphone, e-mail, adresse, horaires…)
│   └── socials.php          Réseaux sociaux proposés dans « Pied de page » (Facebook, Instagram)
├── includes/                Classes PHP (namespace WPGutenberg\)
│   ├── Theme.php            Liste des services
│   ├── Core/                Setup, Assets, Vite, Cleanup, Media, ContactLinks (#appeler / #chat)
│   ├── Editor/              Blocks (auto-registration), Editor (config éditeur)
│   ├── Admin/               OptionsPage (base des pages de réglages) : Coordonnées, Bandeau d'annonce, En-tête, Pied de page
│   ├── Forms/               ContactForm (traitement du formulaire du bloc Contact)
│   ├── Fragments/           API REST des panneaux (menu, filtres)
│   └── helpers.php          wpg_asset, wpg_render, wpg_icon, wpg_logo, wpg_contact, wpg_phone_url, wpg_chat_url…
├── public/                  sprite.svg (généré) + img/logo*.webp (emblème)
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

## Hero en tête de page

Sur les pages, le bloc **Hero** est toujours présent et toujours en premier :

- les nouvelles pages démarrent avec un hero (`config/editor.php` → `post_type_templates`, ajouter d'autres types de contenu au besoin) ;
- il est verrouillé : ni déplaçable, ni supprimable, ni déverrouillable (`lock` par défaut dans `blocks/hero/block.json`, `canLockBlocks: false`) ;
- un bloc inséré au-dessus est automatiquement replacé juste en dessous, et une page existante sans hero en reçoit un à l'ouverture (`src/editor/hero-first.js`) ;
- titre vide = titre de la page.

## Design system v2 (« Flyer v2 »)

Une seule source pour les couleurs et espacements : `theme.json`, mappé pour Tailwind dans `src/stylesheets/theme.css`.

- **Couleurs** : `bordeaux` (primaire), `beige` (fond de page), `cream` (surfaces), `ink` / `ink-soft` (texte), `accent`, `olive-dark`, `gold-dark`, `gold-light`, `gold-pale` (texte sur bordeaux) ; `rose`, `gold`, `olive` sont **décoratifs** (jamais du texte courant). → `bg-bordeaux`, `text-ink-soft`…
- **Espacements** : `section-md` = bandeaux (56 → 96 px), `section-lg` = bas des sections claires (64 → 110 px), valeurs exactes de la maquette à 390 et 1360 px. Conteneur : utilitaire `container` (1240 px, marges 20/40 px).
- **Enchaînement des sections** : une section claire (sans fond) n'a qu'un padding bas ; juste après un bandeau coloré (`bg-bordeaux`, `bg-cream`, bloc avec couleur de fond) elle reprend `section-lg` en haut. Règle unique pour tous les blocs clairs dans `components/content.css` (à compléter si un nouveau bloc clair est créé).
- **Typographie** (`components/typographies.css`, déclarées en `@utility`) : `display`, `h1`, `h2`, `h3`, `script`, `hook`, `quote`, `lead`, `eyebrow`, `rule`, `card-title` (titre de carte 19 px). Tailles nommées en plus de l'échelle : `text-note` (15,5 px, textes de cartes et listes) et `text-card` (19 px). Les titres libres (bloc Titre, `.wp-block-heading`) reçoivent `h2` / `h3` ; les titres des blocs gardent leur propre style. Polices auto-hébergées via `@fontsource` : DM Serif Display, Montserrat, Caveat (en attendant le fichier « Buffalo » de la charte).
- **Boutons** (`components/button.css`) : `.btn btn--primary|outline|light|chat`, `.link-arrow`, interlignage 1,7 comme la maquette (59 px, 52 px en `btn--sm`). Le bloc core Bouton a les styles *Par défaut*, *Contour* et *Clair (sur bordeaux)* ; la classe CSS additionnelle **`has-arrow`** ajoute la flèche après le texte (« Découvrir notre approche → »).
- **En-tête** : navigation complète et bouton à partir de 1200 px (point de rupture `nav` de `theme.css`, variantes `nav:` / `max-nav:`) ; en dessous, bouton de menu rond. La barre « Appeler / Chat » et le chat flottant restent sur `lg`.
- **Composants partagés** (`components/shared.css`) : `.check-list` (+ `--band`, `--cards`, `--on-dark`, applicable à un bloc Liste), `.photo-frame` (cadre doré décalé, `--text` : photo moins haute du bloc Texte + image), badge « NOUVEAU sur VIRIAT » (`parts/components/badge.php` / `blocks/_shared/badge.js`, texte en unités de conteneur : il suit la taille du badge).
- **Icônes** : `src/icons/*.svg` (trait 1,8 px, `currentColor`) → `wpg_icon( 'phone' )` en PHP, `<Icon name="phone" />` (`blocks/_shared/icon.js`) dans l'éditeur.

## Coordonnées et liens d'appel

**Réglages > Coordonnées** (`config/contact.php`) : téléphone, e-mail, adresse, horaires, accès, lien du chat. Lus partout via `wpg_contact()`.

Trois entrées du menu d'administration (visibles par le rôle Client), construites sur `includes/Admin/OptionsPage.php` (une option par page, champs déclarés dans `fields()`) :

| Menu | Classe | Contenu | Lecture |
| --- | --- | --- | --- |
| Bandeau d'annonce | `Admin\Announcement` | afficher / masquer, texte d'une ligne (gras, italique, lien) | `wpg_announcement()` |
| En-tête | `Admin\Header` | image du logo (remplace aussi le grand logo des heros), texte à côté du logo, bouton (afficher, texte, lien, icône) | `wpg_header()`, `wpg_logo()`, `wpg_logo_url()` |
| Pied de page | `Admin\Footer` | signature, bouton, affichage des coordonnées, réseaux sociaux, copyright | `wpg_footer()`, `wpg_socials()` |

Les liens des boutons acceptent `#appeler`, `#chat`, une adresse de page ou une URL (`wpg_link_url()`). Les liens du menu principal et des mentions légales restent dans Apparence > Menus.

Dans un bloc Bouton, l'URL **`#appeler`** devient le lien `tel:` du numéro saisi (ou la page Contact tant qu'il est vide) et **`#chat`** le lien du chat (`includes/Core/ContactLinks.php`), avec l'icône correspondante.

## Blocs v2

| Maquette | Bloc | Rôle |
| --- | --- | --- |
| B1 / B2 | `wpg/hero` | En-tête imposé : variante *Accueil* (logo-texte, signature, boutons, grand logo) ou *Page* (fil d'Ariane, H1, introduction) |
| B3 | `wpg/reassurance` | Trois arguments avec coche |
| B4 | `wpg/tagline` | Accroche sur trois lignes |
| B5 | `wpg/cta` | Panneau « Venez nous rencontrer ! » + boutons |
| C1 | `wpg/text-media` | Bienvenue : surtitre, titre, texte, signature (`wpg-signature`), photo encadrée |
| C2 | `wpg/difference` | Bandeau bordeaux : protocole + arguments |
| C3 | `wpg/family` | Toute la famille : photo, badge, liste à coches |
| C4 | `wpg/steps` › `wpg/step` | Méthode en étapes numérotées |
| C5 | `wpg/team` › `wpg/team-member` | Équipe |
| D1 / D2 | `wpg/services` › `wpg/service`, `wpg/service-family` | Cartes de prestations (grille accueil ou détaillées) |
| D3 / D4 | `wpg/prices` › `wpg/price` | Tarifs en cartes ou en liste |
| D5 | `wpg/included` › `wpg/feature` | Inclus dans chaque prestation |
| D6 | `wpg/info-box` | Encart d'information (réserver et régler) |
| E1 | `wpg/articles` | Derniers articles (conseils) |
| E2 | `wpg/quote-band` | Citation en bandeau |
| E3 | `wpg/faq` | Questions fréquentes (blocs Détails) + accès au chat |
| F1 | `wpg/contact` | Formulaire (traité par `includes/Forms/ContactForm.php`, `wp_mail`), coordonnées, question rapide, carte |

Non implémentés (« À venir » dans la maquette) : bandeau cookies, chiffres clés, galerie, vidéo, témoignages, labels, newsletter, page légale et 404 dédiées.

Blocs de la base d'origine hors design system v2 — `wpg/section`, `wpg/stats` › `wpg/stat` : retirés de l'inserteur (`supports.inserter: false`), toujours enregistrés et rendus là où ils sont déjà utilisés.

Chaque page v2 existe en **pattern** (`patterns/` : accueil, qui-sommes-nous, nos-prestations, tarifs, conseils, contact), proposé à la création d'une page. WordPress met en cache la liste des patterns du thème : après en avoir ajouté un, `wp eval 'wp_get_theme()->delete_pattern_cache();'`.

## Transitions et animations

Tout est coupé quand le visiteur demande moins d'animations (`prefers-reduced-motion`).

| Fonction | Fichiers |
| --- | --- |
| Transition de page : rideau beige sur le contenu et le pied de page, défilement remis en haut (ou restauré avec Précédent / Suivant), préchargement au survol | `src/scripts/utils/barba.js` |
| CSS / scripts des blocs de la page suivante (styles inline par page **ajoutés**, jamais remplacés) | `src/scripts/core/sync-assets.js` |
| En-tête collant qui se cache en descendant et revient en remontant (`--header-h` → `scroll-padding-top`) | `src/scripts/utils/headroom.js` |
| Soulignement du lien courant qui glisse d'une page à l'autre | `src/scripts/utils/nav-indicator.js` |
| Apparition du contenu au défilement (cartes en cascade) | `src/scripts/utils/reveal.js` |
| Styles de tout ce qui précède + survols (boutons, cartes d'articles, FAQ) | `src/stylesheets/components/motion.css` |

Pour des captures pleine page, activer le mouvement réduit (sinon les sections non atteintes restent masquées). L'impression affiche tout.

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
