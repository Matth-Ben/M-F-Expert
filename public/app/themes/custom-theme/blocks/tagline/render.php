<?php
/**
 * Accroche (B4) — front-end markup (mirror of edit.js).
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Unused.
 * @var WP_Block             $block      Block instance.
 *
 * @package WPGutenberg
 */

$lines = array(
	'hook'      => 'hook',
	'text'      => 'max-w-165 text-card leading-[1.4] font-bold tracking-[-0.02em] text-accent lg:text-3xl',
	'signature' => 'script text-[2.375rem] leading-[1.15] text-bordeaux lg:text-[3.125rem]',
);
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'wpg-tagline pb-section-lg' ) ); // phpcs:ignore ?>>
	<div class="mx-auto flex max-w-215 flex-col items-center gap-2.5 px-5 text-center lg:gap-3.5 lg:px-10">
		<?php foreach ( $lines as $key => $class ) : ?>
			<?php if ( ! empty( $attributes[ $key ] ) ) : ?>
				<p class="<?php echo esc_attr( $class ); ?>"><?php echo wp_kses_post( $attributes[ $key ] ); ?></p>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
</section>
