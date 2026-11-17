<?php
/**
 * Réassurance (B3) — front-end markup (mirror of edit.js).
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks HTML (one core/list with .check-list--band).
 * @var WP_Block             $block      Block instance.
 *
 * @package WPGutenberg
 */

if ( ! trim( wp_strip_all_tags( $content ) ) ) {
	return;
}
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'wpg-reassurance pb-12 lg:pb-18' ) ); // phpcs:ignore ?>>
	<div class="container"><?php echo $content; // phpcs:ignore -- core/list output. ?></div>
</section>
