<?php
/**
 * Document head + site header + opening of the Barba container.
 *
 * Closed in footer.php.
 *
 * @package WPGutenberg
 */

$namespace = is_singular() ? get_post_field( 'post_name' ) : ( is_front_page() ? 'home' : 'archive' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>

<body <?php body_class( "flex min-h-screen flex-col" ); ?>>
	<?php wp_body_open(); ?>
	<a class="screen-reader-shortcut" href="#content"><?php esc_html_e( 'Skip to content', 'wp-gutenberg' ); ?></a>

	<?php get_template_part( 'parts/site-header' ); ?>

	<?php // tabindex="-1": the skip link (and Barba, as a fallback) moves the focus here, not only the scroll. ?>
	<main id="content" class="relative flex-1" data-barba="wrapper" tabindex="-1">
		<div data-barba="container" data-barba-namespace="<?php echo esc_attr( $namespace ?: 'page' ); ?>">
