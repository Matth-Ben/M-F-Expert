<?php
/**
 * Panel fragment: menu (loaded via REST, see includes/Fragments).
 *
 * @var array{title?: string, menu_items?: array<int, array{url: string, title: string}>} $args
 *
 * @package WPGutenberg
 */

$items = $args['menu_items'] ?? array();
?>
<nav class="panel-menu" aria-label="<?php esc_attr_e( 'Panel menu', 'wp-gutenberg' ); ?>">
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
