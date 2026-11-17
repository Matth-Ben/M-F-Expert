<?php
/**
 * Carte de prestation — front-end markup (mirror of edit.js).
 * Title level: h3 under the section header, h2 when the section has no header.
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks HTML (paragraphs, check list, buttons).
 * @var WP_Block             $block      Block instance.
 *
 * @package WPGutenberg
 */

$heading   = ( $block->context['wpg/servicesHeader'] ?? true ) ? 'h3' : 'h2';
$link_text = $attributes['linkText'] ?? '';
$link_url  = $attributes['linkUrl'] ?? '';
?>
<article <?php echo get_block_wrapper_attributes( array( 'class' => 'flex flex-col gap-4 rounded-panel bg-bordeaux px-6.5 py-7.5 text-cream shadow-card lg:px-9 lg:py-10' ) ); // phpcs:ignore ?>>
	<div class="icon-dot size-20 bg-olive text-cream">
		<?php echo wpg_icon( $attributes['icon'] ?? 'search', 'size-10' ); // phpcs:ignore -- escaped by the helper. ?>
	</div>
	<?php if ( ! empty( $attributes['title'] ) ) : ?>
		<<?php echo $heading; // phpcs:ignore -- fixed value. ?> class="script text-[2.5rem] leading-[1.05] text-cream lg:text-[2.75rem]"><?php echo wp_kses_post( $attributes['title'] ); ?></<?php echo $heading; // phpcs:ignore ?>>
	<?php endif; ?>
	<?php if ( trim( $content ) ) : ?>
		<div class="flex flex-col gap-4">
			<?php echo $content; // phpcs:ignore -- inner blocks. ?>
		</div>
	<?php endif; ?>
	<?php if ( $link_text && $link_url ) : ?>
		<a class="link-arrow link-arrow--light mt-auto text-md text-gold-light" href="<?php echo esc_url( $link_url ); ?>">
			<span><?php echo wp_kses_post( $link_text ); ?></span>
			<?php echo wpg_icon( 'arrow' ); // phpcs:ignore -- escaped by the helper. ?>
		</a>
	<?php endif; ?>
</article>
