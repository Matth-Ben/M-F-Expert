<?php
/**
 * Stat — front-end markup. `data-count-to` is animated by ../stats/view.js.
 *
 * @var array<string, mixed> $attributes
 *
 * @package WPGutenberg
 */

$value = (float) ( $attributes['value'] ?? 0 );
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'flex flex-col gap-2' ) ); // phpcs:ignore ?>>
	<p class="h1">
		<?php echo esc_html( $attributes['prefix'] ?? '' ); ?><span data-count-to="<?php echo esc_attr( (string) $value ); ?>"><?php echo esc_html( number_format_i18n( $value ) ); ?></span><?php echo esc_html( $attributes['suffix'] ?? '' ); ?>
	</p>
	<?php if ( ! empty( $attributes['label'] ) ) : ?>
		<p><?php echo wp_kses_post( $attributes['label'] ); ?></p>
	<?php endif; ?>
</div>
