<?php
/**
 * Single post: title/meta from the theme, body from blocks.
 *
 * @package WPGutenberg
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article <?php post_class( 'pt-32' ); ?>>
		<header class="container mb-12">
			<p class="text-sm text-ink-soft"><?php echo esc_html( get_the_date() ); ?></p>
			<h1 class="h1"><?php the_title(); ?></h1>
		</header>

		<?php get_template_part( 'parts/content-blocks' ); ?>
	</article>
	<?php
endwhile;

get_footer();
