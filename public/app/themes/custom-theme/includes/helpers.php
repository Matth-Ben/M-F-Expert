<?php
/**
 * Global template helpers (prefixed `wpg_`).
 *
 * These replace the Twig functions / Timber context of the previous theme.
 *
 * @package WPGutenberg
 */

use WPGutenberg\Core\Vite;

/**
 * URL of a Vite-processed asset (images, fonts, svg…).
 *
 * @example <img src="<?php echo esc_url( wpg_asset( 'src/img/hero.jpg' ) ); ?>">
 *
 * @param string $path Source path relative to the theme root.
 * @return string
 */
function wpg_asset( string $path ): string {
	return Vite::asset( $path );
}

/**
 * Site logo: the custom logo (Appearance > Customize > Site Identity) or the site name.
 *
 * @param string $class Classes for the link.
 * @return string
 */
function wpg_logo( string $class = '' ): string {
	$logo_id = (int) get_theme_mod( 'custom_logo' );
	$name    = get_bloginfo( 'name' );
	$inner   = $logo_id
		? wp_get_attachment_image( $logo_id, 'medium', false, array( 'alt' => '', 'class' => 'h-6 w-auto', 'loading' => 'eager' ) )
		: '<span class="text-lg font-medium">' . esc_html( $name ) . '</span>';

	return sprintf(
		'<a href="%1$s" class="%2$s" aria-label="%3$s">%4$s</a>',
		esc_url( home_url( '/' ) ),
		esc_attr( $class ),
		esc_attr( $name ),
		$inner
	);
}

/**
 * Render a template part and return it as a string (parts/*.php).
 *
 * @example echo wpg_render( 'parts/components/post-card', array( 'post' => $post ) );
 *
 * @param string               $slug Template slug, without .php.
 * @param array<string, mixed> $args Variables available as $args in the part.
 * @return string
 */
function wpg_render( string $slug, array $args = array() ): string {
	ob_start();
	get_template_part( $slug, null, $args );
	return (string) ob_get_clean();
}

/**
 * Build an HTML attribute string from an array. `true` => boolean attribute, null/false => skipped.
 *
 * @example <div <?php echo wpg_attrs( array( 'class' => 'foo', 'data-speed' => 2, 'hidden' => false ) ); ?>>
 *
 * @param array<string, scalar|null> $attrs Attributes.
 * @return string
 */
function wpg_attrs( array $attrs ): string {
	$html = array();

	foreach ( $attrs as $name => $value ) {
		if ( null === $value || false === $value || '' === $value ) {
			continue;
		}
		$html[] = true === $value ? esc_attr( $name ) : sprintf( '%s="%s"', esc_attr( $name ), esc_attr( (string) $value ) );
	}

	return implode( ' ', $html );
}

/**
 * Inline SVG <use> from the sprite generated in public/sprite.svg (src/icons/*.svg).
 *
 * @param string $icon  Icon id (file name without .svg).
 * @param string $class Extra classes.
 * @return string
 */
function wpg_icon( string $icon, string $class = '' ): string {
	return sprintf(
		'<svg class="%1$s" aria-hidden="true" focusable="false"><use href="#%2$s"></use></svg>',
		esc_attr( trim( "{$icon} fill-current {$class}" ) ),
		esc_attr( $icon )
	);
}

/**
 * Menu items of a registered location (flat list, per-request cache).
 *
 * @param string $location Menu location slug (see Core\Setup).
 * @return WP_Post[] Items with ->title, ->url, ->target, ->menu_item_parent, ->classes.
 */
function wpg_menu_items( string $location ): array {
	static $cache = array();

	if ( isset( $cache[ $location ] ) ) {
		return $cache[ $location ];
	}

	$locations = get_nav_menu_locations();
	$items     = empty( $locations[ $location ] ) ? array() : wp_get_nav_menu_items( $locations[ $location ] );

	return $cache[ $location ] = is_array( $items ) ? $items : array();
}

/**
 * Social networks configuration (config/socials.php).
 *
 * @return array<string, array{title: string, link?: string}>
 */
function wpg_socials_config(): array {
	static $config = null;
	return $config ??= require WPG_DIR . '/config/socials.php';
}

/**
 * Social networks that have a URL filled in Settings > Socials.
 *
 * @return array<string, array{title: string, url: string, link?: string}>
 */
function wpg_socials(): array {
	$values  = (array) get_option( 'socials', array() );
	$socials = array();

	foreach ( wpg_socials_config() as $id => $network ) {
		if ( ! empty( $values[ $id ] ) ) {
			$socials[ $id ] = $network + array( 'url' => $values[ $id ] );
		}
	}

	return $socials;
}

/**
 * Classes for the <html> element (filterable via `html_class`).
 *
 * @param string $class Space separated classes.
 * @return string
 */
function wpg_html_class( string $class = '' ): string {
	$classes = apply_filters( 'html_class', preg_split( '#\s+#', trim( $class ) ) ?: array() );
	return implode( ' ', array_unique( array_map( 'sanitize_html_class', $classes ) ) );
}
