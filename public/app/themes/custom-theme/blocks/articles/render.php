<?php
/**
 * Articles de conseils (E1) — front-end markup (mirror of edit.js).
 *
 * Latest published posts (optionally from one category). Nothing is printed when there is no post.
 * One link per card: the title link is stretched over the whole card (`after:absolute after:inset-0`),
 * so « Lire le conseil » is a visual cue only (aria-hidden).
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Unused (no inner blocks).
 * @var WP_Block             $block      Block instance.
 *
 * @package WPGutenberg
 */

$count       = max( 1, min( 12, (int) ( $attributes['count'] ?? 3 ) ) );
$category_id = (int) ( $attributes['categoryId'] ?? 0 );

$query_args = array(
	'post_type'           => 'post',
	'post_status'         => 'publish',
	'posts_per_page'      => $count,
	'ignore_sticky_posts' => true,
	'no_found_rows'       => true,
);

if ( $category_id ) {
	$query_args['cat'] = $category_id;
}

if ( is_singular( 'post' ) ) {
	$query_args['post__not_in'] = array( get_queried_object_id() ); // Never suggest the article being read.
}

$posts = get_posts( $query_args );

if ( ! $posts ) {
	return;
}

$default_category = (int) get_option( 'default_category' );

/**
 * Label of a card: SEOPress primary category, else the first category that is not « Non classé ».
 */
$label = static function ( WP_Post $post ) use ( $default_category ): string {
	$primary = (int) get_post_meta( $post->ID, '_seopress_robots_primary_cat', true );

	if ( $primary && has_category( $primary, $post ) ) {
		$term = get_term( $primary, 'category' );
		if ( $term instanceof WP_Term ) {
			return $term->name;
		}
	}

	foreach ( get_the_category( $post->ID ) as $term ) {
		if ( $term->term_id !== $default_category ) {
			return $term->name;
		}
	}

	return '';
};

$eyebrow = trim( wp_strip_all_tags( $attributes['eyebrow'] ?? '' ) );
$title   = $attributes['title'] ?? '';
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'wpg-articles pb-section-lg' ) ); // phpcs:ignore ?>>
	<div class="container flex flex-col gap-5.5 lg:gap-8">
		<?php if ( $eyebrow || $title ) : ?>
			<div class="flex flex-col gap-3">
				<?php if ( $eyebrow ) : ?>
					<p class="eyebrow"><?php echo wp_kses_post( $attributes['eyebrow'] ); ?></p>
				<?php endif; ?>
				<?php if ( $title ) : ?>
					<h2 class="h2"><?php echo wp_kses_post( $title ); ?></h2>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<ul class="grid list-none gap-5 p-0 sm:grid-cols-2 lg:grid-cols-3" role="list">
			<?php foreach ( $posts as $post ) : ?>
				<?php
				$category = $label( $post );
				$excerpt  = wp_trim_words( get_the_excerpt( $post ), 24 );
				?>
				<li class="relative flex flex-col rounded-card bg-cream">
					<div class="h-[9.375rem] overflow-hidden rounded-t-card bg-rose/20 lg:h-45">
						<?php if ( has_post_thumbnail( $post ) ) : ?>
							<?php
							echo get_the_post_thumbnail(
								$post,
								'medium_large',
								array(
									'class' => 'size-full object-cover',
									'sizes' => '(min-width: 1024px) 400px, (min-width: 640px) 50vw, 100vw',
									'alt'   => '', // Decorative: the title follows.
								)
							);
							?>
						<?php else : ?>
							<div class="grid size-full place-items-center">
								<img class="h-20 w-auto opacity-60" src="<?php echo esc_url( wpg_logo_url( 'lg' ) ); ?>" alt="" width="84" height="80" loading="lazy">
							</div>
						<?php endif; ?>
					</div>

					<div class="flex flex-1 flex-col items-start gap-2.5 px-6 pt-5.5 pb-6.5">
						<?php if ( $category ) : ?>
							<span class="rounded-full bg-bordeaux px-3 py-1 text-xs font-bold tracking-[0.06em] text-cream uppercase"><?php echo esc_html( $category ); ?></span>
						<?php endif; ?>

						<h3 class="card-title leading-[1.3]">
							<a class="underline-offset-4 after:absolute after:inset-0 after:rounded-card hover:underline focus-visible:outline-none focus-visible:after:outline-3 focus-visible:after:outline-offset-2 focus-visible:after:outline-gold-dark" href="<?php echo esc_url( get_permalink( $post ) ); ?>">
								<?php echo esc_html( get_the_title( $post ) ); ?>
							</a>
						</h3>

						<?php if ( $excerpt ) : ?>
							<p class="text-md"><?php echo esc_html( $excerpt ); ?></p>
						<?php endif; ?>

						<span class="link-arrow mt-auto text-md" aria-hidden="true">
							<?php esc_html_e( 'Read the article', 'wp-gutenberg' ); ?>
							<?php echo wpg_icon( 'arrow' ); // phpcs:ignore -- escaped in helper. ?>
						</span>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
