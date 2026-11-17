<?php
/**
 * Prestations — front-end markup (mirror of edit.js).
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks HTML (wpg/service, wpg/service-family).
 *
 * @package WPGutenberg
 */

$detailed    = 'detailed' === ( $attributes['layout'] ?? 'grid' );
$show_header = ! empty( $attributes['showHeader'] );
$eyebrow     = $attributes['eyebrow'] ?? '';
$title       = $attributes['title'] ?? '';
$link_text   = $attributes['linkText'] ?? '';
$link_url    = $attributes['linkUrl'] ?? '';

$grid_class = $detailed
	? 'grid gap-4.5 lg:grid-cols-2 lg:gap-6'
	: 'grid items-stretch gap-4.5 lg:grid-cols-[minmax(0,1.1fr)_minmax(0,1.1fr)_minmax(0,0.8fr)] lg:gap-6';
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'wpg-services pb-section-lg' ) ); // phpcs:ignore ?>>
	<div class="container flex flex-col gap-6 lg:gap-9">
		<?php if ( $show_header && ( $eyebrow || $title || ( $link_text && $link_url ) ) ) : ?>
			<div class="flex flex-wrap items-end justify-between gap-x-6 gap-y-4">
				<div class="flex flex-col gap-3">
					<?php if ( $eyebrow ) : ?>
						<p class="eyebrow"><?php echo wp_kses_post( $eyebrow ); ?></p>
					<?php endif; ?>
					<?php if ( $title ) : ?>
						<h2 class="h2"><?php echo wp_kses_post( $title ); ?></h2>
					<?php endif; ?>
				</div>
				<?php if ( $link_text && $link_url ) : ?>
					<a class="link-arrow text-md text-bordeaux" href="<?php echo esc_url( $link_url ); ?>">
						<span><?php echo wp_kses_post( $link_text ); ?></span>
						<?php echo wpg_icon( 'arrow' ); // phpcs:ignore -- escaped by the helper. ?>
					</a>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<div class="<?php echo esc_attr( $grid_class ); ?>">
			<?php echo $content; // phpcs:ignore -- inner blocks. ?>
		</div>
	</div>
</section>
