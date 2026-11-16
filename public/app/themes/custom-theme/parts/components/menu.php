<?php
/**
 * Flat menu for a registered location.
 *
 * @var array{location: string, class?: string} $args
 *
 * @package WPGutenberg
 */

$items = wpg_menu_items( $args['location'] ?? '' );

if ( ! $items ) {
	return;
}
?>
<nav aria-label="<?php echo esc_attr( $args['location'] ); ?>">
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
							'aria-current' => untrailingslashit( $item->url ) === untrailingslashit( home_url( add_query_arg( array() ) ) ) ? 'page' : null,
						)
					);
					?>
				><?php echo esc_html( $item->title ); ?></a>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>
