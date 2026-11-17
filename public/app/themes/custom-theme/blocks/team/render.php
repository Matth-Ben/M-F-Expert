<?php
/**
 * Équipe — front-end markup (mirror of edit.js).
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks HTML (wpg/team-member).
 *
 * @package WPGutenberg
 */

?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'wpg-team pb-section-lg' ) ); // phpcs:ignore ?>>
	<div class="container flex flex-col gap-5.5 lg:gap-8">
		<?php if ( ! empty( $attributes['title'] ) ) : ?>
			<h2 class="h2"><?php echo wp_kses_post( $attributes['title'] ); ?></h2>
		<?php endif; ?>
		<div class="grid gap-5 md:grid-cols-2 lg:max-w-[53.75rem]">
			<?php echo $content; // phpcs:ignore -- inner blocks. ?>
		</div>
	</div>
</section>
