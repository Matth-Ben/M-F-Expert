<?php
/**
 * Questions fréquentes (E3) — front-end markup (mirror of edit.js).
 *
 * Left: eyebrow, H2, text and the chat button. Right: the accordion = core/details inner blocks
 * (native <details>/<summary>: keyboard and screen readers work without JS), styled in style.css.
 * Mobile: one column, the chat button moves under the accordion (DOM order = mobile order);
 * desktop: the button goes back under the text (grid placement).
 * Light section: bottom padding, top padding after a coloured band (components/content.css).
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks HTML (core/details).
 * @var WP_Block             $block      Block instance.
 *
 * @package WPGutenberg
 */

$has = static fn( string $key ): bool => '' !== trim( wp_strip_all_tags( $attributes[ $key ] ?? '' ) );
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'wpg-faq pb-section-lg' ) ); // phpcs:ignore ?>>
	<div class="container grid items-start gap-y-4 lg:grid-cols-[minmax(0,0.75fr)_minmax(0,1.25fr)] lg:grid-rows-[auto_1fr] lg:gap-x-16">
		<div class="flex flex-col gap-3 max-lg:mb-1.5 lg:gap-4">
			<?php if ( $has( 'eyebrow' ) ) : ?>
				<p class="eyebrow"><?php echo wp_kses_post( $attributes['eyebrow'] ); ?></p>
			<?php endif; ?>
			<?php if ( $has( 'title' ) ) : ?>
				<h2 class="h2"><?php echo wp_kses_post( $attributes['title'] ); ?></h2>
			<?php endif; ?>
			<?php if ( $has( 'text' ) ) : ?>
				<p class="max-w-[25rem]"><?php echo wp_kses_post( $attributes['text'] ); ?></p>
			<?php endif; ?>
		</div>

		<div class="wpg-faq__list flex flex-col gap-3 lg:col-start-2 lg:row-span-2 lg:row-start-1">
			<?php echo $content; // phpcs:ignore -- core/details output. ?>
		</div>

		<?php if ( $has( 'chatLabel' ) ) : ?>
			<div class="lg:col-start-1 lg:row-start-2">
				<a class="btn btn--primary btn--sm btn--block-mobile py-3.5 text-cream" href="<?php echo esc_url( wpg_chat_url() ); ?>">
					<?php echo wpg_icon( 'chat' ); // phpcs:ignore -- escaped in helper. ?>
					<?php if ( $has( 'chatLabelMobile' ) ) : ?>
						<span class="lg:hidden"><?php echo wp_kses_post( $attributes['chatLabelMobile'] ); ?></span>
						<span class="max-lg:hidden"><?php echo wp_kses_post( $attributes['chatLabel'] ); ?></span>
					<?php else : ?>
						<span><?php echo wp_kses_post( $attributes['chatLabel'] ); ?></span>
					<?php endif; ?>
				</a>
			</div>
		<?php endif; ?>
	</div>
</section>
