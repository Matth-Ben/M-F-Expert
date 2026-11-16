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

<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>
	<a class="screen-reader-shortcut" href="#content"><?php esc_html_e( 'Skip to content', 'wp-gutenberg' ); ?></a>

	<?php get_template_part( 'parts/site-header' ); ?>

	<main id="content" class="relative" data-barba="wrapper">
		<div data-barba="container" data-barba-namespace="<?php echo esc_attr( $namespace ?: 'page' ); ?>">
