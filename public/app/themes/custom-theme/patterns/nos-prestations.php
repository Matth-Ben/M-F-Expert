<?php
/**
 * Title: Nos prestations
 * Slug: wp-gutenberg/nos-prestations
 * Categories: featured
 * Block Types: core/post-content
 * Post Types: page
 * Description: Prestations v2 : réassurance, prestations détaillées, déroulé d'une visite, inclus, appel à l'action.
 *
 * Maquette « Flyer v2 ». Photos à choisir dans les blocs (mediaId non versionné).
 * `#appeler` / `#chat` : liens résolus depuis Réglages > Coordonnées (includes/Core/ContactLinks.php).
 *
 * @package WPGutenberg
 */

?>
<!-- wp:wpg/hero {"text":"Pas de produits à acheter, pas de longues heures à y consacrer : une prise en charge complète, de l’arrivée au protocole à la maison."} /-->

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

<!-- wp:wpg/services {"layout":"detailed","showHeader":false} -->
<!-- wp:wpg/service {"icon":"search","title":"Solution anti-poux","linkText":"Voir les tarifs","linkUrl":"/tarifs/"} -->
<!-- wp:paragraph -->
<p>Une méthode manuelle et mécanique pour le traitement des poux et lentes, sans produits chimiques. Une solution douce et respectueuse du cuir chevelu.</p>
<!-- /wp:paragraph -->
<!-- wp:list {"className":"check-list check-list--on-dark"} -->
<ul class="wp-block-list check-list check-list--on-dark"><!-- wp:list-item -->
<li>Diagnostic du cuir chevelu</li>
<!-- /wp:list-item --><!-- wp:list-item -->
<li>Traitement manuel et mécanique</li>
<!-- /wp:list-item --><!-- wp:list-item -->
<li>Suivi et contrôle</li>
<!-- /wp:list-item --><!-- wp:list-item -->
<li>Conseils personnalisés</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->
<!-- /wp:wpg/service -->
<!-- wp:wpg/service {"icon":"home","title":"Protéger votre maison","linkText":"Nous contacter","linkUrl":"/contact/"} -->
<!-- wp:paragraph -->
<p>Du matériel de nettoyage pour traiter notre environnement est disponible à la location chez M&amp;F Expert, pour un intérieur sain.</p>
<!-- /wp:paragraph -->
<!-- wp:list {"className":"check-list check-list--on-dark"} -->
<ul class="wp-block-list check-list check-list--on-dark"><!-- wp:list-item -->
<li>Location de matériel de nettoyage</li>
<!-- /wp:list-item --><!-- wp:list-item -->
<li>Traiter l’environnement de la famille</li>
<!-- /wp:list-item --><!-- wp:list-item -->
<li>Un intérieur sain après le traitement</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->
<!-- /wp:wpg/service -->
<!-- wp:wpg/service-family {"icon":"users","title":"Toute la famille","text":"Votre espace anti-poux accueille tous les membres de la famille, enfants et parents."} /-->
<!-- /wp:wpg/services -->

<!-- wp:wpg/steps {"title":"Le déroulé d’une visite"} -->
<!-- wp:wpg/step {"title":"Diagnostic","text":"Un examen attentif du cuir chevelu pour confirmer la situation."} /-->
<!-- wp:wpg/step {"title":"Traitement","text":"Manuel et mécanique, sans produits chimiques."} /-->
<!-- wp:wpg/step {"title":"Contrôle","text":"Une vérification pour un résultat durable."} /-->
<!-- wp:wpg/step {"title":"Protocole","text":"Des explications et des consignes à suivre à la maison."} /-->
<!-- /wp:wpg/steps -->

<!-- wp:wpg/included -->
<!-- wp:wpg/feature {"icon":"shield","title":"Sans produits chimiques","text":"Une méthode manuelle et mécanique, douce pour le cuir chevelu."} /-->
<!-- wp:wpg/feature {"icon":"users","title":"Pour toute la famille","text":"Enfants et parents sont accueillis dans le même espace."} /-->
<!-- wp:wpg/feature {"icon":"heart","title":"Un protocole à la maison","text":"Des explications détaillées pour prolonger le résultat."} /-->
<!-- /wp:wpg/included -->

<!-- wp:wpg/cta -->
<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#appeler">Appelez-nous</a></div>
<!-- /wp:button --><!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="#chat">Poser une question</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->
<!-- /wp:wpg/cta -->
