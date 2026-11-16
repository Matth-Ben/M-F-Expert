<?php
/**
 * Fallback template + posts page (home.php), archives, search.
 *
 * WordPress resolves home.php / archive.php / search.php to this file
 * since they share the same layout. Create a dedicated file only when it differs.
 *
 * @package WPGutenberg
 */

get_header();
?>
<section class="container pt-32 pb-20">
	<header class="mb-12">
		<h1 class="h1">
			<?php
			if ( is_search() ) {
				/* translators: %s: search query. */
				printf( esc_html__( 'Search results for “%s”', 'wp-gutenberg' ), esc_html( get_search_query() ) );
			} elseif ( is_archive() ) {
				echo wp_kses_post( get_the_archive_title() );
			} else {
				echo esc_html( get_the_title( (int) get_option( 'page_for_posts' ) ) ?: __( 'News', 'wp-gutenberg' ) );
			}
			?>
		</h1>
		<?php the_archive_description( '<div class="mt-4 max-w-2xl">', '</div>' ); ?>
	</header>

	<?php if ( have_posts() ) : ?>
		<div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'parts/components/post-card' );
			endwhile;
			?>
		</div>

		<?php
		the_posts_pagination(
			array(
				'class'     => 'mt-12',
				'prev_text' => __( 'Previous', 'wp-gutenberg' ),
				'next_text' => __( 'Next', 'wp-gutenberg' ),
			)
		);
		?>
	<?php else : ?>
		<p><?php esc_html_e( 'No posts found.', 'wp-gutenberg' ); ?></p>
	<?php endif; ?>
</section>
<?php
get_footer();
