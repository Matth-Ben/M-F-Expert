<?php
/**
 * Étape — front-end markup (mirror of edit.js). The number comes from a CSS counter
 * (blocks/steps/style.css); the <ol> gives it to assistive tech.
 *
 * @var array<string, mixed> $attributes Block attributes.
 *
 * @package WPGutenberg
 */

?>
<li <?php echo get_block_wrapper_attributes( array( 'class' => 'wpg-step grid grid-cols-[3.5rem_minmax(0,1fr)] items-start gap-4 lg:flex lg:flex-col lg:items-center lg:gap-3.5 lg:text-center' ) ); // phpcs:ignore ?>>
	<span class="wpg-step__number relative z-[1] grid size-14 place-items-center rounded-full bg-bordeaux font-serif text-[1.625rem] leading-none text-cream lg:size-18 lg:text-[2rem]" aria-hidden="true"></span>
	<div class="pt-1.5 lg:flex lg:flex-col lg:items-center lg:gap-3.5 lg:pt-0">
		<?php if ( ! empty( $attributes['title'] ) ) : ?>
			<h3 class="script text-[2.125rem] leading-[1.05] text-bordeaux lg:text-[2.5rem]"><?php echo wp_kses_post( $attributes['title'] ); ?></h3>
		<?php endif; ?>
		<?php if ( ! empty( $attributes['text'] ) ) : ?>
			<p class="mt-1 text-note lg:mt-0 lg:max-w-[15.625rem]"><?php echo wp_kses_post( $attributes['text'] ); ?></p>
		<?php endif; ?>
	</div>
</li>
