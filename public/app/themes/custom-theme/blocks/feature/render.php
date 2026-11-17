<?php
/**
 * Argument inclus — front-end markup (mirror of edit.js).
 *
 * @var array<string, mixed> $attributes Block attributes.
 *
 * @package WPGutenberg
 */

?>
<li <?php echo get_block_wrapper_attributes( array( 'class' => 'flex flex-col gap-2.5 rounded-card bg-cream p-5.5 text-ink lg:p-7' ) ); // phpcs:ignore ?>>
	<span class="icon-dot size-14 bg-olive-dark text-cream">
		<?php echo wpg_icon( $attributes['icon'] ?? 'check', 'size-7' ); // phpcs:ignore -- escaped by the helper. ?>
	</span>
	<?php if ( ! empty( $attributes['title'] ) ) : ?>
		<h3 class="mt-1 card-title"><?php echo wp_kses_post( $attributes['title'] ); ?></h3>
	<?php endif; ?>
	<?php if ( ! empty( $attributes['text'] ) ) : ?>
		<p class="text-note"><?php echo wp_kses_post( $attributes['text'] ); ?></p>
	<?php endif; ?>
</li>
