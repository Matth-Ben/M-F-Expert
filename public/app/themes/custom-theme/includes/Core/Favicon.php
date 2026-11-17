<?php

namespace WPGutenberg\Core;

/**
 * Favicon / app icons from the logo emblem (public/img/favicon/), through WordPress' own « site icon » API:
 * WordPress then prints the standard tags on the site, the admin and the login page, and `/favicon.ico`
 * redirects to the theme's file instead of the WordPress logo. An icon chosen in Appearance > Customize >
 * Site Identity (option `site_icon`) still takes over.
 *
 * Files: favicon.ico (16/32/48, cream rounded tile), icon-192.png and icon-512.png (same tile),
 * apple-touch-icon.png (180, full square: iOS rounds the corners itself).
 */
final class Favicon {

	public function register(): void {
		add_filter( 'get_site_icon_url', array( $this, 'url' ), 10, 2 );
	}

	/**
	 * @param string $url  URL of the icon chosen in the Customizer (or the fallback).
	 * @param int    $size Requested size in pixels.
	 */
	public function url( $url, $size ): string {
		if ( (int) get_option( 'site_icon' ) ) {
			return (string) $url;
		}

		$file = match ( true ) {
			$size <= 48  => 'favicon.ico',
			$size <= 180 => 'apple-touch-icon.png',
			$size <= 192 => 'icon-192.png',
			default      => 'icon-512.png',
		};

		return WPG_URI . '/public/img/favicon/' . $file;
	}
}
