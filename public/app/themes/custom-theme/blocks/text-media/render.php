<?php
/**
 * Texte + image (C1 « Bienvenue ») — front-end markup (mirror of edit.js).
 *
 * @var array<string, mixed> $attributes
 * @var string               $content    Inner blocks (paragraphs, .wpg-signature paragraph, buttons).
 *
 * @package WPGutenberg
 */

$media_id = (int) ( $attributes['mediaId'] ?? 0 );
$is_right = 'right' === ( $attributes['mediaPosition'] ?? 'left' );
$grid     = 'container grid items-center gap-10 lg:gap-20 ' . ( $is_right ? 'lg:grid-cols-[minmax(0,1.05fr)_minmax(0,0.95fr)]' : 'lg:grid-cols-[minmax(0,0.95fr)_minmax(0,1.05fr)]' );
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'wpg-text-media pb-section-lg' ) ); // phpcs:ignore ?>>
	<div class="<?php echo esc_attr( $grid ); ?>">
		<?php if ( $media_id ) : ?>
			<div class="photo-frame photo-frame--text<?php echo $is_right ? ' lg:order-last' : ''; ?>">
				<?php
				echo wp_get_attachment_image(
					$media_id,
					'large',
					false,
					array( 'sizes' => '(min-width: 1024px) 560px, 100vw' )
				);
				?>
			</div>
		<?php endif; ?>
		<div class="flex flex-col gap-3.5 lg:gap-4.5">
			<?php if ( ! empty( $attributes['eyebrow'] ) ) : ?>
				<p class="eyebrow"><?php echo wp_kses_post( $attributes['eyebrow'] ); ?></p>
			<?php endif; ?>
			<?php if ( ! empty( $attributes['title'] ) ) : ?>
				<h2 class="font-serif text-[3.125rem] leading-[1.02] font-normal tracking-[-0.02em] text-bordeaux lg:text-[4.5rem]"><?php echo wp_kses_post( $attributes['title'] ); ?></h2>
			<?php endif; ?>
			<div class="wpg-text-media__content flex flex-col gap-3.5 lg:gap-4.5"><?php echo $content; // phpcs:ignore -- inner blocks. ?></div>
		</div>
	</div>
</section>
