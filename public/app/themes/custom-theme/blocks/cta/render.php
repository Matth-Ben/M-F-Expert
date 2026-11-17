<?php
/**
 * Appel à l'action (B5) — front-end markup (mirror of edit.js).
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks HTML (core/buttons).
 * @var WP_Block             $block      Block instance.
 *
 * @package WPGutenberg
 */
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'wpg-cta pb-section-lg' ) ); // phpcs:ignore ?>>
	<div class="container">
		<div class="flex flex-col gap-5.5 rounded-cta bg-cream px-6 py-8 lg:flex-row lg:items-center lg:justify-between lg:gap-10 lg:px-16 lg:py-13">
			<div class="flex max-w-155 flex-col gap-2.5">
				<?php if ( ! empty( $attributes['title'] ) ) : ?>
					<h2 class="script text-[2.875rem] text-bordeaux lg:text-[3.875rem]"><?php echo wp_kses_post( $attributes['title'] ); ?></h2>
				<?php endif; ?>
				<?php if ( ! empty( $attributes['text'] ) ) : ?>
					<p class="text-base font-semibold text-ink-soft lg:text-lg"><?php echo wp_kses_post( $attributes['text'] ); ?></p>
				<?php endif; ?>
			</div>
			<?php if ( trim( $content ) ) : ?>
				<div class="flex-none"><?php echo $content; // phpcs:ignore -- core/buttons output. ?></div>
			<?php endif; ?>
		</div>
	</div>
</section>
