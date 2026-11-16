<?php
/**
 * Stats — front-end markup. `data-wpg-block` hooks the element to view.js.
 *
 * @var array<string, mixed> $attributes
 * @var string               $content    Rendered wpg/stat children.
 *
 * @package WPGutenberg
 */

?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'py-section-md', 'data-wpg-block' => 'stats' ) ); // phpcs:ignore ?>>
	<div class="container grid gap-10 sm:grid-cols-2 lg:grid-cols-3">
		<?php echo $content; // phpcs:ignore -- inner blocks. ?>
	</div>
</section>
