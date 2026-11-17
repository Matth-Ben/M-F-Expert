<?php
/**
 * Title: Tarifs
 * Slug: wp-gutenberg/tarifs
 * Categories: featured
 * Block Types: core/post-content
 * Post Types: page
 * Description: Tarifs v2 : liste de tarifs, inclus, réserver et régler, appel à l'action.
 *
 * Maquette « Flyer v2 ». Photos à choisir dans les blocs (mediaId non versionné).
 * `#appeler` / `#chat` : liens résolus depuis Réglages > Coordonnées (includes/Core/ContactLinks.php).
 *
 * @package WPGutenberg
 */

?>
<!-- wp:wpg/hero {"title":"Nos tarifs","text":"Des prestations claires, sans produits à acheter. Retrouvez ici le détail de chaque prise en charge."} /-->

<!-- wp:wpg/prices {"layout":"list","showHeader":false} -->
<!-- wp:wpg/price {"icon":"search","name":"Diagnostic & traitement","description":"Poux et lentes, méthode manuelle et mécanique"} /-->
<!-- wp:wpg/price {"icon":"check","name":"Suivi & contrôle","description":"Pour s’assurer d’un résultat durable"} /-->
<!-- wp:wpg/price {"icon":"heart","name":"Conseils personnalisés","description":"Prévenir une nouvelle infestation"} /-->
<!-- wp:wpg/price {"icon":"home","name":"Location de matériel de nettoyage","description":"Pour traiter votre intérieur"} /-->
<!-- /wp:wpg/prices -->

<!-- wp:wpg/included -->
<!-- wp:wpg/feature {"icon":"shield","title":"Sans produits chimiques","text":"Une méthode manuelle et mécanique, douce pour le cuir chevelu."} /-->
<!-- wp:wpg/feature {"icon":"users","title":"Pour toute la famille","text":"Enfants et parents sont accueillis dans le même espace."} /-->
<!-- wp:wpg/feature {"icon":"heart","title":"Un protocole à la maison","text":"Des explications détaillées pour prolonger le résultat."} /-->
<!-- /wp:wpg/included -->

<!-- wp:wpg/info-box -->
<!-- wp:paragraph -->
<p>[Modalités de réservation et moyens de paiement à compléter avec M&amp;F Expert.]</p>
<!-- /wp:paragraph -->
<!-- /wp:wpg/info-box -->

<!-- wp:wpg/cta -->
<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#appeler">Appelez-nous</a></div>
<!-- /wp:button --><!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="#chat">Poser une question</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->
<!-- /wp:wpg/cta -->
