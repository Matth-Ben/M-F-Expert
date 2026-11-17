<?php
/**
 * Citation en bandeau (E2) — front-end markup (mirror of edit.js).
 *
 * Bordeaux band: small capitals eyebrow, serif quote, script signature.
 * `border-0 pl-0 not-italic` neutralise the default blockquote style of free content (components/content.css),
 * `my-0` the bottom margin WordPress gives to every <figure>.
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Unused (no inner blocks).
 * @var WP_Block             $block      Block instance.
 *
 * @package WPGutenberg
 */

$has = static fn( string $key ): bool => '' !== trim( wp_strip_all_tags( $attributes[ $key ] ?? '' ) );

if ( ! $has( 'quote' ) ) {
	return;
}
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'wpg-quote-band bg-bordeaux py-section-md text-cream' ) ); // phpcs:ignore ?>>
	<div class="container">
		<figure class="mx-auto my-0 flex max-w-[56.25rem] flex-col items-center gap-3.5 text-center lg:gap-5">
			<?php if ( $has( 'eyebrow' ) ) : ?>
				<p class="text-sm font-bold tracking-[0.14em] text-gold-pale uppercase"><?php echo wp_kses_post( $attributes['eyebrow'] ); ?></p>
			<?php endif; ?>

			<blockquote class="quote border-0 pl-0 not-italic">
				<p><?php echo wp_kses_post( $attributes['quote'] ); ?></p>
			</blockquote>

			<?php if ( $has( 'signature' ) ) : ?>
				<figcaption class="script text-[1.875rem] leading-[1.15] text-gold-pale lg:text-[2.375rem]"><?php echo wp_kses_post( $attributes['signature'] ); ?></figcaption>
			<?php endif; ?>
		</figure>
	</div>
</section>
