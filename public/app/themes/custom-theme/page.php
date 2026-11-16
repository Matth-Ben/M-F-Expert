<?php
/**
 * Pages (and the static front page): 100% built with blocks.
 *
 * No title here on purpose — the editor shows exactly what the visitor sees,
 * so the page title is rendered by a block (e.g. Hero) when needed.
 *
 * @package WPGutenberg
 */

get_header();

while ( have_posts() ) :
	the_post();
	get_template_part( 'parts/content-blocks' );
endwhile;

get_footer();
