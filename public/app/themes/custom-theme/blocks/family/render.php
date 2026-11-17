<?php
/**
 * Toute la famille (C3) — front-end markup (mirror of edit.js).
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks HTML (paragraph + core/list .check-list).
 * @var WP_Block             $block      Block instance.
 *
 * @package WPGutenberg
 */

$media_id = (int) ( $attributes['mediaId'] ?? 0 );
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'wpg-family bg-cream py-section-lg' ) ); // phpcs:ignore ?>>
	<div class="container grid items-center gap-11 lg:grid-cols-[minmax(0,0.9fr)_minmax(0,1.1fr)] lg:gap-22">
		<div class="relative">
			<?php if ( $media_id ) : ?>
				<div class="photo-frame">
					<?php
					echo wp_get_attachment_image(
						$media_id,
						'large',
						false,
						array(
							'class' => 'object-[36%_40%]',
							'sizes' => '(min-width: 1024px) 500px, 100vw',
						)
					);
					?>
				</div>
			<?php endif; ?>
			<?php
			if ( ! empty( $attributes['showBadge'] ) ) {
				get_template_part( 'parts/components/badge', null, array( 'class' => 'absolute -top-6 -right-2 lg:-top-9.5 lg:-right-8.5' ) );
			}
			?>
		</div>

		<div class="flex flex-col gap-3.5 lg:gap-4.5">
			<?php if ( ! empty( $attributes['eyebrow'] ) ) : ?>
				<p class="eyebrow"><?php echo wp_kses_post( $attributes['eyebrow'] ); ?></p>
			<?php endif; ?>
			<?php if ( ! empty( $attributes['title'] ) ) : ?>
				<h2 class="script text-[3.25rem] text-bordeaux lg:text-[4.25rem]"><?php echo wp_kses_post( $attributes['title'] ); ?></h2>
			<?php endif; ?>
			<div class="flex flex-col gap-3.5 lg:gap-4.5 [&_p]:max-w-135 [&_ul]:mt-1.5"><?php echo $content; // phpcs:ignore -- core blocks output. ?></div>
		</div>
	</div>
</section>
