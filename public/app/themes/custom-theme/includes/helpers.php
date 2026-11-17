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
/**
 * Logo image URL: the image chosen in the back-office « En-tête » page, else the theme emblem (public/img/).
 *
 * @param string $size `sm` (header) or `lg` (large logos of the heroes).
 */
function wpg_logo_url( string $size = 'sm' ): string {
	$logo_id = (int) wpg_header()['logo_id'];
	$url     = $logo_id ? wp_get_attachment_image_url( $logo_id, 'lg' === $size ? 'large' : 'medium' ) : '';

	return $url ?: WPG_URI . '/public/img/' . ( 'lg' === $size ? 'logo.webp' : 'logo-sm.webp' );
}

/**
 * Text alternative of the logo when it is shown alone (large logo of the home hero):
 * the alt of the chosen image, else a description of the theme emblem.
 */
function wpg_logo_alt(): string {
	$logo_id = (int) wpg_header()['logo_id'];

	if ( ! $logo_id ) {
		return __( 'M&F Expert logo: two children and a comb', 'wp-gutenberg' );
	}

	$alt = trim( (string) get_post_meta( $logo_id, '_wp_attachment_image_alt', true ) );
	return $alt ?: wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
}

/**
 * Header logo (back-office « En-tête »): image + text, linking home.
 * Without text, the image carries the link name (site name).
 */
function wpg_logo( string $class = '' ): string {
	$header = wpg_header();
	$text   = trim( (string) $header['logo_text'] );
	$alt    = $text ? '' : wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
	$attrs  = array(
		'alt'     => $alt,
		'class'   => 'h-11 w-auto lg:h-[57px]',
		'loading' => 'eager',
	);
	$img    = $header['logo_id']
		? wp_get_attachment_image( (int) $header['logo_id'], 'medium', false, $attrs )
		: sprintf( '<img src="%1$s" alt="%2$s" width="60" height="57" class="%3$s" loading="eager">', esc_url( wpg_logo_url() ), esc_attr( $alt ), esc_attr( $attrs['class'] ) );

	return sprintf(
		'<a href="%1$s" class="%2$s">%3$s%4$s</a>',
		esc_url( home_url( '/' ) ),
		esc_attr( trim( 'flex min-h-11 items-center gap-2.5 lg:gap-3 ' . $class ) ),
		$img,
		$text ? sprintf( '<span class="font-serif text-[1.3125rem] leading-[1.1] whitespace-nowrap text-bordeaux lg:text-3xl">%s</span>', esc_html( $text ) ) : ''
	);
}

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
		esc_attr( trim( "{$icon} {$class}" ) ),
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
 * Social networks that have a URL filled in the back-office « Pied de page ».
 *
 * @return array<string, array{title: string, url: string, link?: string}>
 */
function wpg_socials(): array {
	$values  = wpg_footer();
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

function wpg_contact_config(): array {
	static $config = null;
	return $config ??= require WPG_DIR . '/config/contact.php';
}

/**
 * Contact details from Réglages > Coordonnées (defaults from config/contact.php until saved).
 *
 * @return array<string, string>
 */
function wpg_contact(): array {
	return array_map( 'strval', \WPGutenberg\Admin\Contact::values() );
}

/**
 * URL of the page whose slug is `contact`, or the home page.
 */
function wpg_contact_page_url(): string {
	$page = get_page_by_path( 'contact' );
	return $page ? (string) get_permalink( $page ) : home_url( '/' );
}

/**
 * Target of every « Appelez-nous » button: tel: link, or the contact page while no number is set.
 */
function wpg_phone_url(): string {
	$phone = preg_replace( '/[^0-9+]/', '', wpg_contact()['phone'] );
	return $phone ? 'tel:' . $phone : wpg_contact_page_url();
}

/**
 * Target of every « Une question ? » / « Chat » button.
 */
function wpg_chat_url(): string {
	return wpg_contact()['chat_url'] ?: wpg_contact_page_url();
}

/**
 * Announcement bar text (back-office « Bandeau d'annonce »), or '' when the bar is hidden or empty.
 * Already restricted to OptionsPage::INLINE_HTML on save; escape again on output.
 */
function wpg_announcement(): string {
	$settings = \WPGutenberg\Admin\Announcement::values();
	return $settings['enabled'] ? trim( (string) $settings['text'] ) : '';
}

/**
 * Header settings (back-office « En-tête »): logo_id, logo_text, button_enabled, button_label, button_url, button_icon.
 *
 * @return array<string, mixed>
 */
function wpg_header(): array {
	return \WPGutenberg\Admin\Header::values();
}

/**
 * Footer settings (back-office « Pied de page »): tagline, button_*, show_details, social_title, <network>, copyright.
 *
 * @return array<string, mixed>
 */
function wpg_footer(): array {
	return \WPGutenberg\Admin\Footer::values();
}

/**
 * Resolve a link typed in the back-office: tokens #appeler / #chat (Coordonnées), else the URL itself.
 */
function wpg_link_url( string $url ): string {
	return \WPGutenberg\Core\ContactLinks::url( $url );
}
