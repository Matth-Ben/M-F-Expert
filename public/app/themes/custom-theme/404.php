<?php
/**
 * 404.
 *
 * @package WPGutenberg
 */

get_header();
?>
<section class="container flex min-h-[70svh] flex-col items-start justify-center gap-6 pt-32">
	<h1 class="h1"><?php esc_html_e( '404 Error', 'wp-gutenberg' ); ?></h1>
	<p><?php esc_html_e( 'That page can’t be found.', 'wp-gutenberg' ); ?></p>
	<a class="underline" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to home page', 'wp-gutenberg' ); ?></a>
</section>
<?php
get_footer();
