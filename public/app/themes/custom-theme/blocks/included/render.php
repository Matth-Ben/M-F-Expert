<?php
/**
 * Inclus dans chaque prestation — front-end markup (mirror of edit.js).
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks HTML (wpg/feature).
 *
 * @package WPGutenberg
 */

?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'wpg-included bg-bordeaux py-section-md text-cream' ) ); // phpcs:ignore ?>>
	<div class="container flex flex-col gap-6 lg:gap-9">
		<?php if ( ! empty( $attributes['title'] ) ) : ?>
			<h2 class="h2 !text-cream"><?php echo wp_kses_post( $attributes['title'] ); ?></h2>
		<?php endif; ?>
		<ul class="m-0 grid list-none gap-3.5 p-0 md:grid-cols-3 lg:gap-4.5" role="list">
			<?php echo $content; // phpcs:ignore -- inner blocks. ?>
		</ul>
	</div>
</section>
