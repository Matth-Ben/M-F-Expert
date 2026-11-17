<?php
/**
 * Notre différence (C2) — front-end markup (mirror of edit.js).
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks HTML (optional core/buttons + core/list .check-list--cards).
 * @var WP_Block             $block      Block instance.
 *
 * @package WPGutenberg
 */
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'wpg-difference bg-bordeaux py-section-md text-cream' ) ); // phpcs:ignore ?>>
	<div class="wpg-difference__grid container">
		<?php if ( ! empty( $attributes['title'] ) ) : ?>
			<h2 class="wpg-difference__title h2 !text-cream"><?php echo wp_kses_post( $attributes['title'] ); ?></h2>
		<?php endif; ?>
		<?php if ( ! empty( $attributes['text'] ) ) : ?>
			<p class="wpg-difference__box rounded-box border border-cream/35 px-6 py-5 text-base [&_strong]:text-gold-pale"><?php echo wp_kses_post( $attributes['text'] ); ?></p>
		<?php endif; ?>
		<div class="contents"><?php echo $content; // phpcs:ignore -- core blocks output. ?></div>
	</div>
</section>
