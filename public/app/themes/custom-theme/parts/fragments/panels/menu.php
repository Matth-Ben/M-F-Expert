<?php
/**
 * Mobile menu panel (loaded via the fragments REST endpoint).
 */

$items = $args['menu_items'] ?? array();
?>
<nav class="panel-menu" aria-label="<?php esc_attr_e( 'Main navigation', 'wp-gutenberg' ); ?>">
	<div class="panel-menu__header">
		<h2 class="panel-menu__title"><?php echo esc_html( $args['title'] ?? __( 'Menu', 'wp-gutenberg' ) ); ?></h2>
	</div>

	<ul class="panel-menu__list">
		<?php foreach ( $items as $item ) : ?>
			<li class="panel-menu__item">
				<a href="<?php echo esc_url( $item['url'] ); ?>" class="panel-menu__link"><?php echo esc_html( $item['title'] ); ?></a>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>

<a class="btn btn--primary mt-8 min-h-[3.375rem] w-full" href="<?php echo esc_url( wpg_phone_url() ); ?>">
	<?php echo wpg_icon( 'phone' ); // phpcs:ignore -- escaped in helper. ?>
	<?php esc_html_e( 'Call us', 'wp-gutenberg' ); ?>
</a>
