<?php
/**
 * Membre de l'équipe — front-end markup (mirror of edit.js).
 *
 * @var array<string, mixed> $attributes Block attributes.
 *
 * @package WPGutenberg
 */

$media_id = (int) ( $attributes['mediaId'] ?? 0 );
$portrait = $media_id
	? wp_get_attachment_image(
		$media_id,
		'medium',
		false,
		array(
			'class' => 'size-[9.375rem] rounded-full object-cover lg:size-[10.625rem]',
			'sizes' => '(min-width: 1024px) 170px, 150px',
		)
	)
	: '';
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'flex flex-col items-center gap-2.5 rounded-card bg-cream p-6 text-center' ) ); // phpcs:ignore ?>>
	<?php if ( $portrait ) : ?>
		<?php echo $portrait; // phpcs:ignore -- core markup. ?>
	<?php else : ?>
		<div class="grid size-[9.375rem] place-items-center rounded-full border-2 border-dashed border-gold bg-beige text-gold-dark lg:size-[10.625rem]" aria-hidden="true">
			<svg class="size-[2.125rem] fill-none stroke-current" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" focusable="false"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-5-5L5 21"/></svg>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $attributes['name'] ) ) : ?>
		<h3 class="font-serif text-[1.625rem] leading-[1.7] font-normal tracking-normal text-bordeaux"><?php echo wp_kses_post( $attributes['name'] ); ?></h3>
	<?php endif; ?>
	<?php if ( ! empty( $attributes['role'] ) ) : ?>
		<p class="text-md font-semibold text-olive-dark"><?php echo wp_kses_post( $attributes['role'] ); ?></p>
	<?php endif; ?>
	<?php if ( ! empty( $attributes['bio'] ) ) : ?>
		<p class="text-md"><?php echo wp_kses_post( $attributes['bio'] ); ?></p>
	<?php endif; ?>
</div>
