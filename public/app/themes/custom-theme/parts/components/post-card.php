<?php
/**
 * Post card (inside The Loop).
 *
 * @package WPGutenberg
 */

?>
<article <?php post_class( 'group relative flex flex-col gap-4' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<div class="aspect-[4/3] overflow-hidden">
			<?php the_post_thumbnail( 'medium_large', array( 'class' => 'size-full object-cover motion-safe:transition-transform motion-safe:duration-500 motion-safe:group-hover:scale-105', 'sizes' => '(min-width: 1024px) 33vw, (min-width: 768px) 50vw, 100vw' ) ); ?>
		</div>
	<?php endif; ?>

	<h2 class="text-xl">
		<a class="after:absolute after:inset-0" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
	</h2>

	<p class="text-sm text-ink-soft"><?php echo esc_html( get_the_date() ); ?></p>
</article>
