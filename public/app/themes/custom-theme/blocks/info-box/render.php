<?php
/**
 * Encart d'information — front-end markup (mirror of edit.js).
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks HTML (paragraphs).
 *
 * @package WPGutenberg
 */

?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'wpg-info-box pb-5 lg:pb-7' ) ); // phpcs:ignore ?>>
	<div class="container">
		<div class="flex items-start gap-4 rounded-card bg-cream p-5.5 lg:p-7.5">
			<span class="icon-dot size-12 bg-bordeaux text-cream">
				<?php echo wpg_icon( $attributes['icon'] ?? 'calendar', 'size-6' ); // phpcs:ignore -- escaped by the helper. ?>
			</span>
			<div class="min-w-0 flex-1">
				<?php if ( ! empty( $attributes['title'] ) ) : ?>
					<h2 class="card-title leading-[1.7]"><?php echo wp_kses_post( $attributes['title'] ); ?></h2>
				<?php endif; ?>
				<div class="mt-1 flex flex-col gap-2 text-note">
					<?php echo $content; // phpcs:ignore -- inner blocks. ?>
				</div>
			</div>
		</div>
	</div>
</section>
