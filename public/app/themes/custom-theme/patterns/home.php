<?php
/**
 * Title: Page d'accueil
 * Slug: wp-gutenberg/home
 * Categories: featured
 * Block Types: core/post-content
 * Post Types: page
 * Description: Hero + texte/image + chiffres clés.
 *
 * Patterns in /patterns are registered automatically by WordPress.
 * "Block Types: core/post-content" makes it appear in the "Choose a pattern" modal for new pages.
 *
 * @package WPGutenberg
 */

?>
<!-- wp:wpg/hero {"title":"Bienvenue sur le site","text":"Une introduction courte et percutante."} -->
<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button">Découvrir</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->
<!-- /wp:wpg/hero -->

<!-- wp:wpg/text-media -->
<!-- wp:heading -->
<h2 class="wp-block-heading">Qui sommes-nous ?</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Présentez votre activité en quelques lignes.</p>
<!-- /wp:paragraph -->
<!-- /wp:wpg/text-media -->

<!-- wp:wpg/stats {"backgroundColor":"light"} -->
<!-- wp:wpg/stat {"value":25,"label":"ans d’expérience"} /-->
<!-- wp:wpg/stat {"value":300,"suffix":"+","label":"projets livrés"} /-->
<!-- wp:wpg/stat {"value":98,"suffix":"%","label":"clients satisfaits"} /-->
<!-- /wp:wpg/stats -->
