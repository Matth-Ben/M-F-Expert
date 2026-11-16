<?php
/**
 * Hero — front-end markup (mirror of edit.js).
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks HTML (buttons).
 * @var WP_Block             $block      Block instance.
 *
 * @package WPGutenberg
 */

$media_id = (int) ( $attributes['mediaId'] ?? 0 );
$parallax = ! empty( $attributes['parallax'] );
$animate  = ! empty( $attributes['animateTitle'] );

$image_attrs = array(
	'class'         => 'size-full object-cover' . ( $parallax ? ' scale-110' : '' ),
	'sizes'         => '100vw',
	'loading'       => 'eager', // Above the fold.
	'fetchpriority' => 'high',
	'alt'           => '',
);

if ( $parallax ) {
	$image_attrs['data-parallax'] = '';
	$image_attrs['data-speed']    = '2';
}
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'wpg-hero relative flex items-end overflow-hidden bg-black text-white' ) ); // phpcs:ignore ?>>
	<?php if ( $media_id ) : ?>
		<div class="absolute inset-0">
			<?php echo wp_get_attachment_image( $media_id, 'full', false, $image_attrs ); ?>
			<div class="absolute inset-0 bg-black/40"></div>
		</div>
	<?php endif; ?>

	<div class="container relative py-section-md">
		<?php if ( ! empty( $attributes['title'] ) ) : ?>
			<h1 class="h1 max-w-4xl" <?php echo $animate ? 'data-text-split="chars" data-text-animation="fade"' : ''; ?>>
				<?php echo wp_kses_post( $attributes['title'] ); ?>
			</h1>
		<?php endif; ?>

		<?php if ( ! empty( $attributes['text'] ) ) : ?>
			<p class="mt-6 max-w-2xl text-lg"><?php echo wp_kses_post( $attributes['text'] ); ?></p>
		<?php endif; ?>

		<?php if ( trim( $content ) ) : ?>
			<div class="mt-8"><?php echo $content; // phpcs:ignore -- core blocks output. ?></div>
		<?php endif; ?>
	</div>
</section>
