<?php
/**
 * Site footer.
 *
 * @package WPGutenberg
 */

$socials = wpg_socials();
?>
<footer class="container flex flex-wrap items-center justify-between gap-8 py-12">
	<?php echo wpg_logo(); // phpcs:ignore -- escaped in helper. ?>

	<?php if ( $socials ) : ?>
		<ul class="flex gap-4">
			<?php foreach ( $socials as $id => $social ) : ?>
				<li>
					<a href="<?php echo esc_url( $social['url'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $social['title'] ); ?>">
						<?php echo wpg_icon( $id, 'size-5' ); // phpcs:ignore -- escaped in helper. ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<?php get_template_part( 'parts/components/menu', null, array( 'location' => 'footer', 'class' => 'inline-flex gap-4 lg:gap-8' ) ); ?>
	<?php get_template_part( 'parts/components/menu', null, array( 'location' => 'legals', 'class' => 'inline-flex gap-4 lg:gap-8 text-sm' ) ); ?>
</footer>
