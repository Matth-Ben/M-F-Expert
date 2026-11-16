<?php
/**
 * Text + media — front-end markup (mirror of edit.js).
 *
 * @var array<string, mixed> $attributes
 * @var string               $content
 *
 * @package WPGutenberg
 */

$media_id = (int) ( $attributes['mediaId'] ?? 0 );
$is_right = 'right' === ( $attributes['mediaPosition'] ?? 'left' );
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'py-section-md' ) ); // phpcs:ignore ?>>
	<div class="container grid items-center gap-10 md:grid-cols-2">
		<div<?php echo $is_right ? ' class="md:order-last"' : ''; ?>>
			<?php
			if ( $media_id ) {
				echo wp_get_attachment_image(
					$media_id,
					'large',
					false,
					array(
						'class' => 'aspect-[4/3] w-full object-cover',
						'sizes' => '(min-width: 768px) 50vw, 100vw',
					)
				);
			}
			?>
		</div>
		<div class="space-y-6">
			<?php echo $content; // phpcs:ignore -- inner blocks. ?>
		</div>
	</div>
</section>
