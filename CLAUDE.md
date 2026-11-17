# CLAUDE.md — M&F Expert (base WP Gutenberg)

> **Rôle de ce fichier** : contexte permanent de toute session Claude Code sur ce dépôt. À lire en premier, avant d'explorer ou de coder.
>
> **Document vivant** : dès qu'une session constate un écart avec le code réel (fichier déplacé, convention changée), elle **met ce fichier à jour** dans le même changement.

---

## 0. Objectif

Claude absorbe le répétitif déjà connu (structure, plomberie des blocs, accessibilité, performance) pour que le temps humain aille aux décisions techniques et créatives. Ce fichier doit permettre de :

- ne jamais recréer une fonctionnalité qui existe déjà ;
- produire des blocs Gutenberg cohérents avec l'existant sans réexplication ;
- appliquer par défaut les bonnes pratiques Gutenberg / Tailwind / accessibilité / performance ;
- savoir quand passer la main à un agent spécialisé (§6).

---

## 1. Vue d'ensemble

Site vitrine de **M&F Expert** (espace anti-poux à Viriat), d'après la maquette Claude Design « Flyer v2 » (seule la v2 est implémentée ; les blocs « À venir » de la maquette ne le sont pas). Site WordPress dont le back-office repose sur **l'éditeur de blocs** : le client compose ses pages avec des blocs qui ont **le rendu réel du site** dans l'éditeur. Pas de Timber/Twig, pas d'ACF.

**Stack**

- **Backend** : PHP 8.2+, WordPress 7.1 (`johnpbloch/wordpress-core`), tout via Composer (core et plugins non versionnés). `public/wp-config.php` lit le `.env` racine.
- **Templates** : PHP natif (hiérarchie WordPress + `parts/`), helpers `wpg_*`.
- **Blocs** : blocs Gutenberg **dynamiques** (`block.json` + `edit.js` + `render.php`), compilés par `@wordpress/scripts` v34 (Node 22.0 local ; les versions ≥ 35 exigent Node ≥ 22.12).
- **Front** : Vite 7 + Tailwind CSS v4, GSAP (+ SplitText), Barba.js, Lenis. JS en `.js`/`.ts` dans `src/scripts/`. Polices auto-hébergées via `@fontsource` (DM Serif Display, Montserrat variable, Caveat — remplace « Buffalo » de la charte en attendant le fichier).
- **Plugins** : uniquement des plugins **gratuits** de wordpress.org via wpackagist (SEOPress, Matomo, Relevanssi, Simple History, Wordfence, WP Health). Aucun plugin payant ni privé.
- **Langue** : `fr_FR`, text domain `wp-gutenberg`.

**Arborescence**

```
wp-gutenberg/
├── CLAUDE.md, .claude/ (agents, skills)
├── composer.json, .env(.example)
└── public/
    ├── wp-config.php, index.php
    ├── wordpress/                      core (Composer, non versionné)
    └── app/
        ├── mu-plugins/                 toujours actifs (voir tableau)
        ├── plugins/                    Composer, non versionnés
        └── themes/custom-theme/        le thème
```

**Le thème (`public/app/themes/custom-theme/`)**

```
functions.php        autoloader PSR-4 (WPGutenberg\ → includes/) + Theme::boot()
theme.json           design tokens + verrouillage de l'éditeur (source unique des couleurs/espacements/largeurs)
header.php, footer.php, page.php, single.php, index.php, 404.php
parts/               site-header, site-footer, content-blocks, components/ (menu, post-card), fragments/panels/
patterns/            compositions de blocs (auto-enregistrées par WordPress)
blocks/<slug>/       SOURCES des blocs : block.json, index.js, edit.js, render.php, [style.css], [view.js]
blocks/_shared/      composants éditeur partagés : media.js (useMedia, MediaPanel, MediaCanvasPlaceholder), icon.js (<Icon>),
                     badge.js (badge NOUVEAU sur VIRIAT), controls.js (IconControl, LinkPanel)
build/               blocs compilés + blocks-manifest.php + _editor/ (git-ignoré)
config/              editor.php (blocs autorisés), contact.php (champs de Réglages > Coordonnées), socials.php
includes/            Theme.php (liste des services), helpers.php,
                     Core/ (Setup, Assets, Vite, Cleanup, Media, ContactLinks), Editor/ (Blocks, Editor),
                     Admin/ (OptionsPage, Contact, Announcement, Header, Footer), Forms/ (ContactForm), Fragments/ (Rest, FragmentLoader)
public/              sprite.svg (généré depuis src/icons), img/logo.webp + logo-sm.webp (emblème du logo)
src/                 stylesheets/ (Tailwind), scripts/ (app.js, core/, fragments/, utils/), editor/ (editor.css, index.js),
                     icons/ (sprite : pictogrammes filaires 24 px, trait 1,8, currentColor)
languages/           wp-gutenberg.pot, fr_FR.po/.mo, JSON des traductions JS
bin/create-block.mjs générateur de blocs
```

**Fonctionnalités en place (à connaître avant de développer)**

- **Enregistrement automatique des blocs** : `Editor\Blocks` lit `build/blocks-manifest.php` (`wp_register_block_types_from_metadata_collection`). Rien à déclarer en PHP. Catégorie d'inserteur `wpg` (« Blocs du site »), namespace de blocs `wpg/`.
- **Éditeur WYSIWYG** : `Editor\Editor` charge la CSS du site dans l'iframe de l'éditeur (`enqueue_block_assets`, admin uniquement) + `src/editor/editor.css`. Liste blanche des blocs : `config/editor.php` (core + plugins) ; tous les `wpg/*` sont autorisés automatiquement. Patterns distants, Openverse et Block Directory désactivés.
- **Assets à la demande** : CSS/JS de chaque bloc (core et custom) chargés seulement si le bloc est présent (`should_load_separate_core_block_assets`, `style` / `viewScriptModule` dans `block.json`). `src/scripts/core/sync-assets.js` recopie ces assets lors des transitions Barba.
- **Runtime JS des blocs** : `src/scripts/core/blocks.js`. Un `view.js` fait `(window.wpgBlocks ??= []).push(['<slug>', setup])` ; `setup(el)` est appelé pour chaque `[data-wpg-block="<slug>"]`, y compris après Barba, et peut renvoyer un cleanup appelé à la sortie de page.
- **Hero imposé en tête des pages** : modèle de contenu des pages dans `config/editor.php` (`post_type_templates`, appliqué par `Editor\Editor::post_type_template()`) ; le hero est verrouillé par défaut (`lock: { move, remove }`, `multiple: false`, `inserter: false` dans son `block.json`) ; `src/editor/hero-first.js` redescend sous le hero tout bloc inséré au-dessus et ajoute un hero aux pages existantes qui n'en ont pas ; `canLockBlocks: false` empêche de déverrouiller. Titre vide = titre de la page (front et placeholder éditeur). Une page enregistrée sans hero en base (ex. pages légales vides) reçoit quand même le hero par défaut sur le front (`parts/content-blocks.php`).
- **Panneau modal** (`src/scripts/fragments/PanelManager.ts`) : `<div role="dialog" aria-modal>` nommé par le titre du fragment, reste de la page en `inert`, Échap / bouton / voile pour fermer, focus rendu au déclencheur (`aria-expanded` synchronisé), Lenis stoppé, fermé sans rendre le focus au début d'une navigation Barba (`closeForNavigation()`). Libellés traduits via `window.wpg.i18n` (`Core\Assets`).
- **Fragments REST** (panneaux menu/filtres) : `POST /wp-json/wpg/v1/fragment`, whitelist + sanitisation dans `includes/Fragments/FragmentLoader.php`, templates dans `parts/fragments/panels/`, client `src/scripts/fragments/` (config `window.WPG_FRAGMENTS`).
- **Design system v2** : `theme.json` (palette bordeaux / beige / cream / ink / ink-soft / accent / gold* / olive* / rose / field, `spacingSizes` sm/md/lg = respiration / bandeaux 56→96 px / bas de section claire 64→110 px, `contentSize` 47.5rem / `wideSize` 77.5rem) → mappés pour Tailwind dans `src/stylesheets/theme.css` (`bg-bordeaux`, `py-section-md`, `rounded-panel`, `shadow-card`, `max-w-site`…). `rose`, `gold`, `olive` sont décoratifs (jamais du texte courant). Typo en `@utility` (`components/typographies.css` : `display`, `h1`, `h2`, `h3`, `script`, `hook`, `quote`, `lead`, `eyebrow`, `rule`, `card-title`), tailles nommées `text-note` (15,5 px) et `text-card` (19 px) dans `theme.css` ; `h2`/`h3` ne s'appliquent d'office qu'aux titres libres (`.wp-block-heading`). Boutons `.btn btn--primary|outline|light|chat` + `.link-arrow` (`components/button.css`, interlignage 1,7, core/button aux styles Par défaut / Contour / Clair, option « Flèche » dans la barre latérale du bloc Bouton = classe `has-arrow`, ajoutée par `src/editor/index.js`), composants partagés `.check-list` (+ `--band|--cards|--on-dark`, posable sur un bloc Liste), `.photo-frame` (+ `--text`), badge (`parts/components/badge.php`, texte en unités de conteneur) dans `components/shared.css`.
- **Pages de réglages du back-office** (base `Admin\OptionsPage` : une option par page, champs typés déclarés dans `fields()` — text, textarea, email, tel, url, link, checkbox, select, image, editor —, valeurs par défaut tant que la page n'est pas enregistrée, `::values()`) : Réglages > Coordonnées (`Admin\Contact`, champs dans `config/contact.php`), menus « Bandeau d'annonce » (`Admin\Announcement` → `wpg_announcement()`), « En-tête » (`Admin\Header` : image et texte du logo, bouton → `wpg_header()`, `wpg_logo()`, `wpg_logo_url()`, `wpg_logo_alt()`) et « Pied de page » (`Admin\Footer` : signature, bouton, coordonnées oui/non, réseaux de `config/socials.php`, copyright → `wpg_footer()`, `wpg_socials()`). Liens des boutons : `wpg_link_url()` (jetons `#appeler` / `#chat`). Il n'y a plus de page Réglages > Socials ni de logo personnalisé du Customizer.
- **Transitions et animations** (front, toutes coupées par `prefers-reduced-motion`) :
  - **Transition de page** (`src/scripts/utils/barba.js`) : un rideau beige (`.page-curtain`, sous l'en-tête et la barre mobile) couvre contenu et pied de page ; l'ancienne page est masquée (`display:none`) dès que la nouvelle est insérée, les assets de la nouvelle page sont synchronisés dans `beforeEnter` (seul moment où `data.next.html` existe), le défilement est remis en haut (ou restauré pour Précédent / Suivant) puis le rideau se lève. Préchargement des pages au survol / focus / toucher (`barba.prefetch`) ; le chargement spéculatif natif de WordPress est désactivé (`Core\Cleanup`) pour ne pas tout télécharger deux fois. Un petit loader n'apparaît que si la page tarde (> 0,6 s). Lien vers une section d'une autre page (`/contact/#contact-form`) : Barba retire le `#hash` de l'URL, il est lu sur le lien cliqué ; la page arrive sur la section (sous l'en-tête), le focus va dessus et le `#hash` est remis dans l'adresse. GSAP ScrollTrigger réécrit `history.scrollRestoration` : il est forcé à `manual` via `ScrollTrigger.clearScrollMemory('manual')`, sinon le navigateur fait sauter l'ancienne page dès qu'on appuie sur Précédent.
  - **Assets par page** (`src/scripts/core/sync-assets.js`) : feuilles et styles inline manquants copiés ; un style inline qui existe déjà avec un autre contenu (styles globaux par bloc, conteneurs `wp-container-*`) reçoit les règles de la nouvelle page **à côté** (jamais remplacé).
  - **En-tête headroom** (`utils/headroom.js` + `components/motion.css`) : `position: sticky` avec `top: -hauteur du bandeau d'annonce`, masqué en descendant, réaffiché en remontant / au focus / à la navigation ; `--header-h` alimente `scroll-padding-top` (2.4.11), annulé pour les contrôles de l'en-tête lui-même (`scroll-margin-top` négatif) ; un focus qui entre dans l'en-tête caché le réaffiche et remet la page à sa place dans la même image (pas de saut) ; un focus clavier sous la barre mobile fait défiler juste ce qu'il faut (`.quick-bar`).
  - **Soulignement du lien courant** (`utils/nav-indicator.js`) : barre qui glisse vers le lien de la nouvelle page dès le clic ; `aria-current` reste la source.
  - **Apparition au défilement** (`utils/reveal.js`) : sections découpées en éléments (cartes d'une grille / liste une à une, en cascade par lot). Au premier chargement, seul ce qui est sous la ligne de flottaison est préparé (aucun clignotement, LCP intact) ; après navigation tout est préparé derrière le rideau. Utilise la propriété `translate` et retire ses classes une fois jouée.
  - **Micro-interactions** (`components/motion.css`, hors layer, propriétés `translate` / `scale` / `rotate` pour ne pas écraser un `transform`) : boutons qui se soulèvent, icône qui bouge, contour qui se remplit ; cartes d'articles (seules cartes cliquables) qui se soulèvent avec zoom de l'image ; FAQ qui se déplie en douceur (`::details-content`) ; survols limités à `(hover: hover)`.
- **Chrome du site** (`parts/site-header.php`, `parts/site-footer.php`) : bandeau d'annonce, en-tête (logo + menu `main` + bouton d'appel à partir de 1200 px, point de rupture `nav` de `theme.css` ; en dessous, bouton de menu rond → panneau), pied de page à vague (signature, coordonnées, menu `legals`, réseaux), bouton chat flottant (bureau) et barre fixe « Appeler / Chat » (mobile, le pied de page réserve sa hauteur). `aria-current` des menus resynchronisé après Barba (`src/scripts/utils/barba.js`).
- **Coordonnées** : Réglages > Coordonnées (`Admin\Contact`, champs dans `config/contact.php`, valeurs par défaut tant que la page n'est pas enregistrée) → `wpg_contact()`, `wpg_phone_url()` (tel: ou page Contact si vide), `wpg_chat_url()`, `wpg_contact_page_url()` ; côté éditeur `window.wpgEditor.contact` (+ `sprite`, `logo`, imprimés par `Editor\Editor`).
- **Liens jetons** : dans un bloc Bouton, l'URL `#appeler` / `#chat` est résolue au rendu depuis les Coordonnées, avec l'icône (`Core\ContactLinks`, `data-icon`). Les gabarits des blocs et les patterns les utilisent. Dans l'éditeur (bouton sans `href`), `src/editor/index.js` pose `data-icon` sur le bloc Bouton pour afficher la même icône.
- **Formulaire de contact** : bloc `wpg/contact` + `Forms\ContactForm` (`admin-post.php`, nonce, champ piège, validation serveur, `wp_mail` vers l'e-mail des Coordonnées, redirection 303 `?contact=sent|error#contact-form`, erreurs/valeurs gardées en transient). POST natif : Barba n'est pas concerné.
- **Blocs v2** (code maquette → bloc) : B1/B2 `hero` (variantes `home` / `page` avec fil d'Ariane), B3 `reassurance`, B4 `tagline`, B5 `cta`, C1 `text-media`, C2 `difference`, C3 `family`, C4 `steps`›`step`, C5 `team`›`team-member`, D1/D2 `services`›`service`/`service-family`, D3/D4 `prices`›`price`, D5 `included`›`feature`, D6 `info-box`, E1 `articles` (derniers articles), E2 `quote-band`, E3 `faq` (InnerBlocks `core/details`), F1 `contact`. Les sections claires n'ont qu'un padding bas et reprennent `section-lg` en haut quand elles suivent un bandeau : règle unique dans `components/content.css` (liste des blocs clairs à compléter pour tout nouveau bloc clair). `wpg/section` et `wpg/stats` › `wpg/stat` (base d'origine, hors DS v2) sont masqués de l'inserteur.
- **Pages v2 en patterns** : `patterns/home.php`, `qui-sommes-nous.php`, `nos-prestations.php`, `tarifs.php`, `conseils.php`, `contact.php` (proposés à la création d'une page). WordPress met en cache la liste des patterns du thème : après ajout d'un fichier, `wp eval 'wp_get_theme()->delete_pattern_cache();'`.
- **Tailwind hors cascade layer** : `styles.css` importe les utilitaires sans `@layer` pour primer sur la CSS WordPress (elle aussi hors layer). Piège : les styles d'éléments de `theme.json` sortent hors layer (`a:where(:not(.wp-element-button))`) et battent toute règle en `@layer components` → ne jamais mettre de couleur de lien dans `theme.json` ; un soulignement de lien défini par une classe de composant doit être écrit hors layer (bloc final de `components/site.css`). Les sections pleine largeur s'enchaînent sans `blockGap` (`components/content.css`).
- **Favicon** (`Core\Favicon`) : icônes générées depuis l'emblème dans `public/img/favicon/` (favicon.ico 16/32/48 et icônes 192/512 sur tuile crème arrondie, apple-touch-icon 180 carré plein), servies via le filtre `get_site_icon_url` : WordPress imprime les balises (site, admin, connexion) et `/favicon.ico` y redirige. Une icône choisie dans Personnaliser > Identité du site reste prioritaire.
- **Logo** : image choisie dans « En-tête », sinon l'emblème `public/img/logo(-sm).webp` ; texte à côté du logo (défaut : nom du site) — `wpg_logo()`. Grand logo des heros : `wpg_logo_url( 'lg' )` + `wpg_logo_alt()`.
- **Images** : qualité 82, sous-tailles WebP générées à l'upload (`Core\Media`).
- **Traductions JS** : `Core\Setup::script_translation_file()` redirige WordPress vers `languages/` du thème pour les JSON des scripts de blocs.

**mu-plugins**

| Fichier | Rôle |
|---|---|
| `client-menu-restrictions.php` | Rôle back-office `client_role`, masquage sélectif des menus wp-admin. |
| `disable-features.php` | Désactive les commentaires (Gutenberg reste actif). |
| `duplicate-post.php` | Lien « Dupliquer » sur posts/pages/CPT. |
| `homepage-delete-protection.php` | Double confirmation avant suppression de la page d'accueil. |

**N'existe PAS dans ce projet** (ne pas supposer leur présence) : ACF, Timber/Twig, piecesjs, endpoint newsletter, reCAPTCHA, Gravity Forms ou autre plugin de formulaire (le seul formulaire est le bloc `wpg/contact` natif), bandeau cookies, rechargement Barba des pages à formulaire. Le fil d'Ariane est celui du hero (variante `page`, parent direct seulement), pas un bloc SEOPress (version Pro uniquement).

---

## 2. Règles d'or

1. **Chercher avant de créer.** Parcourir `blocks/`, `blocks/_shared/`, `parts/`, `includes/`, `includes/helpers.php` et `patterns/`. Préférer étendre (attribut, variante, style de bloc) plutôt que dupliquer.
2. **Blocs dynamiques uniquement.** Le HTML front vient de `render.php` ; `save` ne renvoie que `<InnerBlocks.Content />` ou `null`. On peut ainsi modifier le markup sans « contenu invalide » sur les pages existantes. **Ne jamais renommer un bloc** (`wpg/<slug>`) sans migrer le contenu en base (`wp search-replace 'wp:ancien/' 'wp:nouveau/'`).
3. **`edit.js` ≡ `render.php`.** Mêmes balises, mêmes classes Tailwind : c'est la condition du WYSIWYG. Les comportements front (parallaxe, split text, compteurs) ne sont pas rejoués dans l'éditeur ; tout attribut qui cache du contenu avant JS (`data-text-split`) est omis côté éditeur. Jamais de `>` dans un attribut d'un `render.php` (ex. variante Tailwind `[&>p]:…`) : `wptexturize` le prend pour la fin de la balise et casse le HTML du bloc ; utiliser `[&_p]:…` ou une classe dans le `style.css` du bloc.
4. **Une seule source pour les tokens : `theme.json`.** Nouvelle couleur = palette `theme.json` + mapping dans `theme.css`. Pas de valeurs arbitraires (`text-[17px]`, hex en dur). Couleurs/padding d'un bloc : `supports` natifs dans `block.json`, pas d'attributs maison.
5. **Images = ID d'attachement** (`mediaId`), jamais d'URL. Front : `wp_get_attachment_image()` (srcset/sizes/lazy ; `loading: eager` + `fetchpriority: high` au-dessus de la ligne de flottaison). Éditeur : `useMedia()`.
6. **Contenu riche = InnerBlocks** avec des blocs core (titres, paragraphes, boutons) plutôt que des champs texte multiples.
7. **Tailwind utility-first.** CSS propre au bloc dans `blocks/<slug>/style.css` seulement si un utilitaire ne suffit pas. Toute surcharge d'une CSS tierce ou WordPress s'écrit **hors `@layer`** (une règle hors layer gagne toujours contre une règle en layer).
8. **Performance.** Pas de JS global pour un bloc : `view.js` (`viewScriptModule`), chargé seulement là où le bloc est présent. Bibliothèque lourde = `import()` dynamique dans `view.js`.
9. **Accessibilité par défaut (WCAG AA)**, critère d'acceptation de chaque bloc. Pièges déjà rencontrés sur de vrais projets (Lighthouse 100 et axe à 0 ne suffisent pas) :
   - **Lien étiré** (carte cliquable) : ordre des variantes `focus-visible:after:outline-…` ; `after:focus-visible:` ne produit aucun focus visible (2.4.7).
   - **Texte blanc sur photo** : voile ≥ 40 % ou dégradé sombre derrière le texte, contraste mesuré sur la vraie photo (1.4.3). Le hero utilise `bg-black/40`.
   - **Header collant** (si un projet en ajoute un) : `scroll-padding-top` = hauteur du header pour que le focus ne passe pas dessous (2.4.11).
   - **Panneau / modale** : nom accessible, reste de la page en `inert`, focus rendu au déclencheur ; `role="dialog"` sur un `<div>`, jamais un `<aside>`. Fermer le panneau au début d'une transition Barba.
   - **Ancres / lien d'évitement** : déplacer le focus sur la cible, pas seulement défiler.
   - **`prefers-reduced-motion`** : couper Lenis, fondus, compteurs (déjà fait dans `stats/view.js`), zooms.
   - **Contrôles non textuels** : contraste ≥ 3:1 (1.4.11). **Libellés ARIA en français.**
   - **Anneau de focus** : l'or du DS (#C38B47) est trop clair. Tous les focus utilisent `--focus-ring` (`base/base.css`, hors layer) : or foncé sur fonds clairs, or clair sur bordeaux, posé par les conteneurs `.bg-*` et jamais par l'élément focalisé. Le bord des champs utilise le jeton `field` (#967D5F, ≥ 3:1).
   - **Éléments fixes** (barre mobile Appeler/Chat, chat flottant) : `html { scroll-padding-bottom }` les empêche de masquer le focus (2.4.11). Tout nouvel élément fixe doit en tenir compte.
   - **Navigation Barba** : le focus va sur le `<h1>` de la nouvelle page (sinon `<main tabindex="-1">`) ; `aria-current` est resynchronisé dans le header, le footer et le panneau (`src/scripts/utils/current-links.ts`). Le formulaire de contact place le focus sur son message de retour (`autofocus`, sans JS).
10. **i18n systématique.** Toute chaîne : `__( '…', 'wp-gutenberg' )` (PHP et JS). Après ajout : `pnpm i18n`, traduire les entrées vides de `languages/fr_FR.po`, relancer `pnpm i18n`.
11. **Lecteur vidéo = Video.js** (jamais Plyr ni un lecteur maison) : importé dynamiquement dans le `view.js` du bloc (au clic sur l'aperçu ou à l'entrée dans le viewport), `player.dispose()` dans le cleanup. `<video preload="none" poster="…">` + `<track kind="captions">` si sous-titres (1.2.2). YouTube : `videojs-youtube` importé au clic ; Vimeo : iframe insérée au clic ; toujours un lien de repli.
12. **Ce qu'un plugin produit, on le style, on ne le recode pas.** Et on ne **désenregistre jamais** un script du cœur (`jquery`, `wp-i18n`…) : WordPress ne les charge que si nécessaire.
13. **Aucune licence ni clé dans un fichier versionné** (`.env` et `auth.json` sont ignorés). Pas de lien de téléchargement signé dans `composer.json`. Plugins : gratuits, via `composer require wpackagist-plugin/<slug>`. Pas de dump SQL ni de log dans le dépôt.
14. **Icônes** (`src/icons/*.svg` → sprite `public/sprite.svg`) : `wpg_icon( 'id', 'classes' )`, couleur via `currentColor`. Vérifier toute nouvelle icône à l'écran (un `fill` exporté de Figma peut la rendre invisible).
15. **Ne pas halluciner l'environnement.** Vérifier dans le repo avant de supposer un fichier, une classe ou une convention.

---

## 3. Créer un bloc (maquette → bloc Gutenberg)

### Étape 1 — Réception
Le mainteneur fournit une maquette (capture, export, ou lien Figma si le connecteur est disponible).

### Étape 2 — Dialogue (avant tout code)
Questions à trancher, groupées quand c'est évident :
- **Identité** : slug kebab-case, rôle dans la page (hero, liste, CTA…), titre et description en français pour l'inserteur.
- **Contenu** : quels textes sont des attributs `RichText` vs des InnerBlocks (préférer InnerBlocks) ; images (`mediaId`) ; liens (bloc `core/buttons` en InnerBlocks plutôt qu'un attribut URL).
- **Structure** : bloc simple, ou parent/enfant (`parent` / `allowedBlocks` comme `wpg/stats` + `wpg/stat`) ?
- **Variantes** : `supports` natifs (couleur, padding), attribut `enum` + contrôle dans l'inspecteur, ou style de bloc (`registerBlockStyle`) ?
- **Responsive** : comportement par breakpoint si la maquette n'en couvre qu'un.
- **Interaction** : bloc statique, ou `view.js` ? Animation GSAP ? Vidéo (règle n°11) ?
- **Accessibilité** : pattern APG particulier (accordéon, onglets, carrousel, dialog) ?

### Étape 3 — Anti-duplication
Vérifier `blocks/` et `patterns/` : si un bloc proche existe, proposer une variante.

### Étape 4 — Génération
1. `pnpm block:new <slug> --title="Titre" [--inner] [--view]` (crée block.json, index.js, edit.js, render.php, style.css, [view.js]).
2. `block.json` : attributs, `supports`, description en français.
3. `render.php` (front) puis `edit.js` (éditeur) avec **les mêmes classes**. Réutiliser `blocks/_shared/media.js`.
4. `view.js` seulement si nécessaire (runtime `wpgBlocks`, `data-wpg-block` dans `render.php`).
5. Chaînes traduisibles → `pnpm i18n` + `fr_FR.po`.
6. `pnpm build` (ou `pnpm dev`) ; ajouter le bloc à un pattern si c'est une brique de page courante.

### Étape 5 — Revue
Agents DA et Dev Accessibilité, puis Test (§6).

---

## 4. Conventions de code

- **Services PHP** : une classe dans `includes/<Domaine>/` avec une méthode `register()` qui ne fait qu'accrocher des hooks, ajoutée à `Theme::SERVICES`. Namespace `WPGutenberg\`, classes `final`.
- **Helpers** (`includes/helpers.php`, préfixe `wpg_`) : `wpg_asset()`, `wpg_logo()`, `wpg_logo_url()`, `wpg_render()` (part → string), `wpg_attrs()` (attributs HTML échappés), `wpg_icon()`, `wpg_menu_items()`, `wpg_socials()`, `wpg_contact()`, `wpg_phone_url()`, `wpg_chat_url()`, `wpg_contact_page_url()`, `wpg_announcement()`, `wpg_header()`, `wpg_footer()`, `wpg_link_url()`, `wpg_logo_alt()`, `wpg_html_class()`.
- **Templates** : structure dans les fichiers racine + `parts/` (via `get_template_part( $slug, null, $args )`). Logique dans les classes/helpers, pas dans les templates.
- **Config projet** : `config/*.php` (retourne un tableau) plutôt que des valeurs codées en dur dans les classes.
- **Échappement** : `esc_html`, `esc_attr`, `esc_url`, `wp_kses_post` pour le contenu riche ; `get_block_wrapper_attributes()` sur la racine de chaque bloc.
- **Pages** (`page.php`) : 100 % blocs, aucun titre ajouté par le thème (le hero porte le `<h1>`). Un seul `<h1>` par page.

---

## 5. Commandes et vérifications

```bash
# thème
cd public/app/themes/custom-theme
pnpm dev            # Vite (HMR) + wp-scripts --watch
pnpm build          # blocs puis Vite
pnpm block:new <slug> [--title=…] [--inner] [--view]
pnpm i18n           # build + .pot + fusion fr_FR.po + .mo + JSON

# projet
composer require wpackagist-plugin/<slug>
cd public && wp --path=wordpress <commande>
```

Vérifications utiles (Composer affiche des avertissements de dépréciation PHP 8.4 : `php -d error_reporting=0 /usr/local/bin/composer …`) :
- PHP : `php -l` sur les fichiers modifiés ; `public/app/debug.log` (vider avant un test pour ne lire que les nouvelles entrées).
- Blocs enregistrés : `wp eval` sur `WP_Block_Type_Registry`, ou `GET /wp/v2/block-types/wpg`.
- Front : `curl -sk https://mfexpert.local/<slug>/` (permaliens `/%postname%/` en local). Pages : `/`, `/qui-sommes-nous/`, `/nos-prestations/`, `/tarifs/`, `/conseils/`, `/contact/` ; menus `main` (Principal) et `legals` (Mentions).
- Navigateur : le navigateur intégré refuse les domaines `.local` ; utiliser Chrome headless (`google-chrome --headless=new --ignore-certificate-errors`) ou Playwright (`playwright-core` disponible dans `~/.npm/_npx/*/node_modules/`, avec `executablePath: '/usr/bin/google-chrome'`).

---

## 6. Agents spécialisés (`.claude/agents/`)

| Agent | Quand l'invoquer |
|---|---|
| **dev** | Implémentation (block.json, edit.js, render.php, PHP, CSS, JS) — agent par défaut pour le code métier. |
| **da** | Après implémentation : fidélité à la maquette, cohérence avec les tokens et les autres blocs, parité éditeur/front. |
| **dev-accessibilite** | Après Dev : audit **et correction** WCAG AA. |
| **test** | Vérification fonctionnelle : build, rendu, éditeur, Barba, non-régression. |
| **test-accessibilite** | Audit a11y indépendant en fin de chaîne (ne corrige pas). |

Skills génériques disponibles : `ponytail` (+ `-audit`, `-debt`, `-gain`, `-help`, `-review`) pour la solution la plus simple, `caveman` / `caveman-review` pour des réponses compressées.

---

## 7. Démarrer une session

1. Lire ce fichier.
2. Explorer rapidement le repo réel et corriger ce fichier en cas d'écart confirmé.
3. Pour un nouveau bloc : suivre §3 (dialogue avant génération).
4. Invoquer les agents (§6) aux bons moments.
5. En fin de session, mettre à jour ce fichier et `custom-theme/README.md` si quelque chose a changé.

---

## 8. Écarts connus (dette à traiter, ne pas prendre pour des conventions)

- **Barba et formulaires** : aucun rechargement complet n'est prévu pour une page dont un plugin a besoin de CSS dans le `<head>` ou de scripts inline. À ajouter (`data-barba-prevent` ou rechargement) le jour où un plugin de formulaire est installé.
- **Chat** : il n'existe pas encore de chat ; les boutons « Une question ? » / « Chat » pointent vers le lien du chat des Coordonnées, sinon la page Contact.
- **Placeholders de contenu** : téléphone, horaires, accès, prix, réponses FAQ, équipe et articles sont à compléter avec M&F Expert (textes « [À compléter] » de la maquette).

---

## 9. Décisions passées (pour ne pas les refaire)

- **Twig/Timber → PHP natif** et **ACF → blocs Gutenberg dynamiques** : rendu réel dans l'éditeur, pas de moteur de template à compiler, pas de dépendance payante.
- **Hybride plutôt que Full Site Editing** : header/footer/archives restent dans le thème (le client ne les modifie pas).
- **Supprimé** à la création du projet : `views/` Twig, `acf-json/`, modèles Timber, auth front et fil d'Ariane maison (dépendaient de Timber, l'auth était déjà cassée), ancienne page de réglages d'agence (son seul réglage faisait doublon avec `disable-features.php`), plugins privés et payants.
- **Le namespace de blocs `wpg/`** est figé : un renommage impose une migration du contenu en base (règle n°2).
- **Pas de `make-php` i18n** : la version locale de WP-CLI ne l'a pas ; le `.mo` suffit.
