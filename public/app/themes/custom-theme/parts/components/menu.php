<?php
/**
 * Nav menu for a location (top level only).
 *
 * @var array{location: string, label?: string, class?: string, nav_class?: string} $args
 */

$items = wpg_menu_items( $args['location'] ?? '' );

if ( ! $items ) {
	return;
}

// Current URL without query string (`?contact=sent` after the contact form must still mark « Contact »).
$current = untrailingslashit( home_url( (string) wp_parse_url( add_query_arg( array() ), PHP_URL_PATH ) ) );
?>
<nav aria-label="<?php echo esc_attr( $args['label'] ?? $args['location'] ); ?>"<?php echo ! empty( $args['nav_class'] ) ? ' class="' . esc_attr( $args['nav_class'] ) . '"' : ''; ?>>
	<ul class="<?php echo esc_attr( $args['class'] ?? '' ); ?>">
		<?php foreach ( $items as $item ) : ?>
			<?php if ( (int) $item->menu_item_parent ) { continue; } ?>
			<li>
				<a
					href="<?php echo esc_url( $item->url ); ?>"
					<?php
					echo wpg_attrs( // phpcs:ignore -- escaped in helper.
						array(
							'target'       => $item->target ?: null,
							'rel'          => '_blank' === $item->target ? 'noopener noreferrer' : null,
							'aria-current' => untrailingslashit( $item->url ) === $current ? 'page' : null,
						)
					);
					?>
				><?php echo esc_html( $item->title ); ?></a>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>
