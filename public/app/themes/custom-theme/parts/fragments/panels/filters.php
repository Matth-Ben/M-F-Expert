<?php
/**
 * Panel fragment: filters (loaded via REST, see includes/Fragments).
 *
 * @var array{title?: string} $args
 *
 * @package WPGutenberg
 */

$categories = get_categories( array( 'hide_empty' => true ) );
?>
<div class="panel-filters">
	<div class="panel-filters__header">
		<h2 class="panel-filters__title"><?php echo esc_html( $args['title'] ?? __( 'Filters', 'wp-gutenberg' ) ); ?></h2>
	</div>

	<form class="panel-filters__form" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<div class="panel-filters__group">
			<label for="filter-category" class="panel-filters__label"><?php esc_html_e( 'Category', 'wp-gutenberg' ); ?></label>
			<select id="filter-category" name="category_name" class="panel-filters__select">
				<option value=""><?php esc_html_e( 'All categories', 'wp-gutenberg' ); ?></option>
				<?php foreach ( $categories as $category ) : ?>
					<option value="<?php echo esc_attr( $category->slug ); ?>"><?php echo esc_html( $category->name ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>

		<div class="panel-filters__group">
			<label for="filter-search" class="panel-filters__label"><?php esc_html_e( 'Search', 'wp-gutenberg' ); ?></label>
			<input type="search" id="filter-search" name="s" class="panel-filters__input" value="<?php echo esc_attr( get_search_query() ); ?>">
		</div>

		<button type="submit" class="panel-filters__submit"><?php esc_html_e( 'Apply', 'wp-gutenberg' ); ?></button>
	</form>
</div>
