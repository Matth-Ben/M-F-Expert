<?php
/**
 * Site header.
 *
 * @package WPGutenberg
 */

?>
<header class="headroom site-header absolute top-0 z-50 w-full bg-white/90 backdrop-blur-sm transition-colors duration-300">
	<div class="container flex items-center justify-between py-4">
		<?php echo wpg_logo(); // phpcs:ignore -- escaped in helper. ?>

		<div class="flex items-center gap-4">
			<?php get_template_part( 'parts/components/menu', null, array( 'location' => 'main', 'class' => 'text-cta inline-flex gap-4' ) ); ?>

			<button type="button" data-panel-open="menu" aria-haspopup="dialog" aria-expanded="false" aria-controls="panel" aria-label="<?php esc_attr_e( 'Open menu', 'wp-gutenberg' ); ?>">
				<?php esc_html_e( 'Menu', 'wp-gutenberg' ); ?>
			</button>
		</div>
	</div>
</header>
