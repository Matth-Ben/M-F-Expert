<?php
/**
 * Section — front-end markup (mirror of edit.js).
 *
 * Color/padding classes & inline styles are added by get_block_wrapper_attributes()
 * from the block supports declared in block.json.
 *
 * @var array<string, mixed> $attributes
 * @var string               $content
 *
 * @package WPGutenberg
 */

$inner = ( 'narrow' === ( $attributes['width'] ?? '' ) ? 'mx-auto w-full max-w-3xl px-5' : 'container' ) . ' space-y-6';
?>
<section <?php echo get_block_wrapper_attributes(); // phpcs:ignore ?>>
	<div class="<?php echo esc_attr( $inner ); ?>">
		<?php echo $content; // phpcs:ignore -- inner blocks. ?>
	</div>
</section>
