<?php
/**
 * Tarifs — front-end markup (mirror of edit.js, classes in classes.js).
 * The call button links to the phone number (Réglages > Coordonnées) unless a URL is set.
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks HTML (wpg/price).
 *
 * @package WPGutenberg
 */

$is_list     = 'list' === ( $attributes['layout'] ?? 'cards' );
$show_header = ! empty( $attributes['showHeader'] );
$title       = $attributes['title'] ?? '';
$intro       = $attributes['intro'] ?? '';
$note        = $attributes['note'] ?? '';
$button_text = $attributes['buttonText'] ?? '';
$button_url  = ( $attributes['buttonUrl'] ?? '' ) ?: wpg_phone_url();
$is_phone    = '' === ( $attributes['buttonUrl'] ?? '' ); // Default target = phone: show the phone icon.

$stack_class = $is_list ? 'container flex flex-col gap-5.5 lg:gap-8' : 'container flex flex-col gap-6 lg:gap-9';
$list_class  = $is_list
	? 'm-0 grid list-none gap-3.5 p-0 lg:grid-cols-2 lg:gap-4.5'
	: 'm-0 grid list-none gap-3.5 p-0 sm:grid-cols-2 lg:grid-cols-4 lg:gap-4.5';
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'wpg-prices pb-section-lg' ) ); // phpcs:ignore ?>>
	<div class="<?php echo esc_attr( $stack_class ); ?>">
		<?php if ( $show_header && ( $title || $intro ) ) : ?>
			<div class="grid gap-2.5 lg:grid-cols-[minmax(0,0.8fr)_minmax(0,1.2fr)] lg:items-end lg:gap-10">
				<?php if ( $title ) : ?>
					<h2 class="script text-[3.25rem] text-bordeaux lg:text-[4.25rem]"><?php echo wp_kses_post( $title ); ?></h2>
				<?php endif; ?>
				<?php if ( $intro ) : ?>
					<p class="max-w-[35rem] lg:justify-self-end"><?php echo wp_kses_post( $intro ); ?></p>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<ul class="<?php echo esc_attr( $list_class ); ?>" role="list">
			<?php echo $content; // phpcs:ignore -- inner blocks. ?>
		</ul>

		<?php if ( $note || $button_text ) : ?>
			<div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
				<?php if ( $note ) : ?>
					<p class="text-sm font-semibold text-gold-dark"><?php echo wp_kses_post( $note ); ?></p>
				<?php endif; ?>
				<?php if ( $button_text ) : ?>
					<a class="btn btn--primary btn--block-mobile text-cream max-lg:px-2.5 max-lg:text-md lg:ml-auto" href="<?php echo esc_url( $button_url ); ?>">
						<?php echo $is_phone ? wpg_icon( 'phone' ) : ''; // phpcs:ignore -- escaped by the helper. ?>
						<span><?php echo wp_kses_post( $button_text ); ?></span>
					</a>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
