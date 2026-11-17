<?php
/**
 * Title: Page d'accueil
 * Slug: wp-gutenberg/home
 * Categories: featured
 * Block Types: core/post-content
 * Post Types: page
 * Description: Accueil v2 : hero, réassurance, accroche, prestations, méthode, bienvenue, tarifs, différence, FAQ.
 *
 * Maquette « Flyer v2 ». Photos à choisir dans les blocs (mediaId non versionné).
 * `#appeler` / `#chat` : liens résolus depuis Réglages > Coordonnées (includes/Core/ContactLinks.php).
 *
 * @package WPGutenberg
 */

?>
<!-- wp:wpg/hero {"variant":"home"} -->
<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#appeler">Appelez-nous</a></div>
<!-- /wp:button --><!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="/nos-prestations/">Voir nos prestations</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->
<!-- /wp:wpg/hero -->

<!-- wp:wpg/reassurance -->
<!-- wp:list {"className":"check-list check-list--band"} -->
<ul class="wp-block-list check-list check-list--band"><!-- wp:list-item -->
<li>Sans produits chimiques</li>
<!-- /wp:list-item --><!-- wp:list-item -->
<li>Douce pour le cuir chevelu</li>
<!-- /wp:list-item --><!-- wp:list-item -->
<li>Enfants et parents</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->
<!-- /wp:wpg/reassurance -->

<!-- wp:wpg/tagline /-->

<!-- wp:wpg/services {"linkUrl":"/nos-prestations/"} -->
<!-- wp:wpg/service {"icon":"search","title":"Solution anti-poux","linkText":"Voir les tarifs","linkUrl":"/tarifs/"} -->
<!-- wp:paragraph -->
<p>Une méthode manuelle et mécanique pour le traitement des poux et lentes, sans produits chimiques. Une solution douce et respectueuse du cuir chevelu.</p>
<!-- /wp:paragraph -->
<!-- /wp:wpg/service -->
<!-- wp:wpg/service {"icon":"home","title":"Protéger votre maison","linkText":"Nous contacter","linkUrl":"/contact/"} -->
<!-- wp:paragraph -->
<p>Du matériel de nettoyage pour traiter notre environnement est disponible à la location chez M&amp;F Expert, pour un intérieur sain.</p>
<!-- /wp:paragraph -->
<!-- /wp:wpg/service -->
<!-- wp:wpg/service-family {"icon":"users","title":"Toute la famille","text":"Votre espace anti-poux accueille tous les membres de la famille, enfants et parents."} /-->
<!-- /wp:wpg/services -->

<!-- wp:wpg/steps {"anchor":"methode"} -->
<!-- wp:wpg/step {"title":"Diagnostiquer","text":"Nous examinons le cuir chevelu pour confirmer la présence de poux et de lentes."} /-->
<!-- wp:wpg/step {"title":"Traiter","text":"Une méthode manuelle et mécanique, sans produits chimiques."} /-->
<!-- wp:wpg/step {"title":"Contrôler","text":"Un contrôle pour s’assurer d’un résultat durable."} /-->
<!-- wp:wpg/step {"title":"Accompagner","text":"Des explications détaillées et un protocole à suivre à la maison."} /-->
<!-- /wp:wpg/steps -->

<!-- wp:wpg/text-media {"mediaPosition":"right","eyebrow":"Bienvenue chez","title":"M\u0026amp;F Expert"} -->
<!-- wp:paragraph -->
<p>Notre salon est dédié à la prise en charge des poux et des lentes. Nous vous accueillons dans un espace chaleureux, avec une méthode professionnelle, pour une solution durable et en toute sérénité.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"className":"wpg-signature"} -->
<p class="wpg-signature">Une expertise douce et efficace pour toute la famille.</p>
<!-- /wp:paragraph -->
<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"has-arrow"} -->
<div class="wp-block-button has-arrow"><a class="wp-block-button__link wp-element-button" href="/qui-sommes-nous/">Découvrir notre approche</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->
<!-- /wp:wpg/text-media -->

<!-- wp:wpg/prices {"anchor":"tarifs"} -->
<!-- wp:wpg/price {"icon":"search","name":"Diagnostic & traitement","description":"Poux et lentes, méthode manuelle et mécanique"} /-->
<!-- wp:wpg/price {"icon":"check","name":"Suivi & contrôle","description":"Pour s’assurer d’un résultat durable"} /-->
<!-- wp:wpg/price {"icon":"heart","name":"Conseils personnalisés","description":"Prévenir une nouvelle infestation"} /-->
<!-- wp:wpg/price {"icon":"home","name":"Location de matériel de nettoyage","description":"Pour traiter votre intérieur"} /-->
<!-- /wp:wpg/prices -->

<!-- wp:wpg/difference -->
<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-light has-arrow"} -->
<div class="wp-block-button is-style-light has-arrow"><a class="wp-block-button__link wp-element-button" href="/qui-sommes-nous/">Qui sommes-nous ?</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->
<!-- wp:list {"className":"check-list check-list--cards"} -->
<ul class="wp-block-list check-list check-list--cards"><!-- wp:list-item -->
<li>Une expertise reconnue</li>
<!-- /wp:list-item --><!-- wp:list-item -->
<li>Une prise en charge personnalisée</li>
<!-- /wp:list-item --><!-- wp:list-item -->
<li>Un accompagnement complet</li>
<!-- /wp:list-item --><!-- wp:list-item -->
<li>Une pédagogie claire pour les familles</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->
<!-- /wp:wpg/difference -->

<!-- wp:wpg/faq -->
<!-- wp:details -->
<details class="wp-block-details"><summary>Comment se déroule une séance ?</summary><!-- wp:paragraph -->
<p>[Réponse à rédiger avec M&amp;F Expert]</p>
<!-- /wp:paragraph --></details>
<!-- /wp:details --><!-- wp:details -->
<details class="wp-block-details"><summary>Comment reconnaître les poux et les lentes ?</summary><!-- wp:paragraph -->
<p>[Réponse à rédiger avec M&amp;F Expert]</p>
<!-- /wp:paragraph --></details>
<!-- /wp:details --><!-- wp:details {"showContent":true} -->
<details class="wp-block-details" open><summary>Que faire après le traitement ?</summary><!-- wp:paragraph -->
<p>Chaque famille reçoit des explications détaillées et un protocole à suivre à la maison après le traitement.</p>
<!-- /wp:paragraph --></details>
<!-- /wp:details --><!-- wp:details -->
<details class="wp-block-details"><summary>Quel protocole suivre à la maison ?</summary><!-- wp:paragraph -->
<p>[Réponse à rédiger avec M&amp;F Expert]</p>
<!-- /wp:paragraph --></details>
<!-- /wp:details --><!-- wp:details -->
<details class="wp-block-details"><summary>Comment prévenir une réinfestation ?</summary><!-- wp:paragraph -->
<p>[Réponse à rédiger avec M&amp;F Expert]</p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->
<!-- /wp:wpg/faq -->
