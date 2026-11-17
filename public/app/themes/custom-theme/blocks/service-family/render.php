<?php
/**
 * Carte « Toute la famille » — front-end markup (mirror of edit.js, classes in classes.js).
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var WP_Block             $block      Block instance.
 *
 * @package WPGutenberg
 */

$detailed = 'detailed' === ( $block->context['wpg/servicesLayout'] ?? 'grid' );
$heading  = ( $block->context['wpg/servicesHeader'] ?? true ) ? 'h3' : 'h2';

$classes = $detailed
	? array(
		'card'  => 'relative flex flex-col gap-4 rounded-panel bg-cream px-6.5 py-7.5 lg:col-span-2 lg:flex-row lg:items-center lg:gap-7 lg:px-10 lg:py-7.5',
		'body'  => 'flex flex-col gap-4 lg:flex-1 lg:gap-2',
		'text'  => 'lg:max-w-[40rem]',
		'badge' => 'absolute -top-3.5 -right-1.5 size-[7.375rem] lg:relative lg:top-auto lg:right-auto lg:-my-3.5 lg:size-[8.25rem]',
	)
	: array(
		'card'  => 'relative flex flex-col gap-4 rounded-panel bg-cream px-6.5 py-7.5 lg:px-8 lg:py-10',
		'body'  => 'flex flex-col gap-4',
		'text'  => '',
		'badge' => 'absolute -top-3.5 -right-1.5 size-[7.375rem] lg:-top-5.5 lg:-right-4.5 lg:size-[8.25rem]',
	);
?>
<article <?php echo get_block_wrapper_attributes( array( 'class' => $classes['card'] ) ); // phpcs:ignore ?>>
	<div class="icon-dot size-20 bg-olive-dark text-cream">
		<?php echo wpg_icon( $attributes['icon'] ?? 'users', 'size-10' ); // phpcs:ignore -- escaped by the helper. ?>
	</div>
	<div class="<?php echo esc_attr( $classes['body'] ); ?>">
		<?php if ( ! empty( $attributes['title'] ) ) : ?>
			<<?php echo $heading; // phpcs:ignore -- fixed value. ?> class="script text-[2.5rem] leading-[1.05] text-bordeaux lg:text-[2.75rem]"><?php echo wp_kses_post( $attributes['title'] ); ?></<?php echo $heading; // phpcs:ignore ?>>
		<?php endif; ?>
		<?php if ( ! empty( $attributes['text'] ) ) : ?>
			<p<?php echo $classes['text'] ? ' class="' . esc_attr( $classes['text'] ) . '"' : ''; ?>><?php echo wp_kses_post( $attributes['text'] ); ?></p>
		<?php endif; ?>
	</div>
	<?php
	if ( ! empty( $attributes['showBadge'] ) ) {
		get_template_part( 'parts/components/badge', null, array( 'class' => $classes['badge'] ) );
	}
	?>
</article>
