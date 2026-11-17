<?php
/**
 * Méthode en étapes — front-end markup (mirror of edit.js).
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks HTML (wpg/step).
 *
 * @package WPGutenberg
 */

$title = $attributes['title'] ?? '';
$intro = $attributes['intro'] ?? '';
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'wpg-steps bg-cream py-section-md' ) ); // phpcs:ignore ?>>
	<div class="container flex flex-col gap-8 lg:gap-13">
		<?php if ( $title || $intro ) : ?>
			<div class="flex flex-col gap-2.5 lg:items-center lg:text-center">
				<?php if ( $title ) : ?>
					<h2 class="h2"><?php echo wp_kses_post( $title ); ?></h2>
				<?php endif; ?>
				<?php if ( $intro ) : ?>
					<p class="max-w-[35rem]"><?php echo wp_kses_post( $intro ); ?></p>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<ol class="wpg-steps__list m-0 flex list-none flex-col gap-7 p-0 lg:grid lg:auto-cols-fr lg:grid-flow-col lg:gap-6" role="list">
			<?php echo $content; // phpcs:ignore -- inner blocks. ?>
		</ol>
	</div>
</section>
