<?php
/**
 * Tarif — front-end markup (mirror of edit.js, classes in classes.js).
 * Name level: h3 under the section header, h2 when the section has no header.
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var WP_Block             $block      Block instance.
 *
 * @package WPGutenberg
 */

$is_list = 'list' === ( $block->context['wpg/pricesLayout'] ?? 'cards' );
$heading = ( $block->context['wpg/pricesHeader'] ?? true ) ? 'h3' : 'h2';

$base = array(
	'item'  => 'grid grid-cols-[3.5rem_minmax(0,1fr)] items-center gap-x-3 gap-y-3.5 rounded-card bg-cream p-5.5',
	'text'  => 'col-span-2 row-start-2 flex flex-col gap-2',
	'price' => 'col-start-2 row-start-1 justify-self-end rounded-full bg-bordeaux px-4 py-2 text-sm leading-[1.7] font-extrabold text-cream',
);

$classes = $is_list
	? array(
		'item'        => $base['item'] . ' lg:grid-cols-[3.5rem_minmax(0,1fr)_auto] lg:gap-x-5 lg:px-7 lg:py-6.5',
		'text'        => $base['text'] . ' lg:col-span-1 lg:col-start-2 lg:row-start-1 lg:gap-0',
		'name'        => 'card-title lg:text-[1.25rem]',
		'description' => 'text-md leading-normal lg:text-note',
		'price'       => $base['price'] . ' lg:col-start-3',
	)
	: array(
		'item'        => $base['item'] . ' content-start lg:gap-y-5 lg:p-7',
		'text'        => $base['text'] . ' lg:gap-2.5',
		'name'        => 'card-title',
		'description' => 'text-md leading-normal',
		'price'       => $base['price'],
	);
?>
<li <?php echo get_block_wrapper_attributes( array( 'class' => $classes['item'] ) ); // phpcs:ignore ?>>
	<span class="icon-dot col-start-1 row-start-1 size-14 bg-bordeaux text-cream">
		<?php echo wpg_icon( $attributes['icon'] ?? 'check', 'size-6.5' ); // phpcs:ignore -- escaped by the helper. ?>
	</span>
	<div class="<?php echo esc_attr( $classes['text'] ); ?>">
		<?php if ( ! empty( $attributes['name'] ) ) : ?>
			<<?php echo $heading; // phpcs:ignore -- fixed value. ?> class="<?php echo esc_attr( $classes['name'] ); ?>"><?php echo wp_kses_post( $attributes['name'] ); ?></<?php echo $heading; // phpcs:ignore ?>>
		<?php endif; ?>
		<?php if ( ! empty( $attributes['description'] ) ) : ?>
			<p class="<?php echo esc_attr( $classes['description'] ); ?>"><?php echo wp_kses_post( $attributes['description'] ); ?></p>
		<?php endif; ?>
	</div>
	<?php if ( ! empty( $attributes['price'] ) ) : ?>
		<p class="<?php echo esc_attr( $classes['price'] ); ?>"><?php echo wp_kses_post( $attributes['price'] ); ?></p>
	<?php endif; ?>
</li>
