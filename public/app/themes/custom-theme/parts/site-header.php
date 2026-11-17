<?php
/**
 * A1 announcement bar + A2 header (logo, six links, call button; round menu button on mobile).
 * Both inside <header> (banner landmark): no page content is left outside a landmark.
 */

$announcement = wpg_announcement(); // Back-office « Bandeau d'annonce » (includes/Admin/Announcement.php).
$header       = wpg_header();       // Back-office « En-tête » (includes/Admin/Header.php): logo and button.
?>
<header class="site-header">
	<?php if ( $announcement ) : ?>
		<div class="flex items-center justify-center gap-2.5 bg-bordeaux px-5 py-2.5 text-center text-[0.84375rem] leading-[1.4] font-semibold text-cream lg:py-[11px] lg:text-md">
			<?php echo wpg_icon( 'calendar', 'size-[18px] flex-none text-gold-pale' ); // phpcs:ignore -- escaped in helper. ?>
			<p class="[&_a]:underline [&_a]:underline-offset-4"><?php echo wp_kses( $announcement, \WPGutenberg\Admin\OptionsPage::INLINE_HTML ); ?></p>
		</div>
	<?php endif; ?>

	<div class="container flex items-center justify-between gap-8 py-3.5 lg:py-6">
		<?php echo wpg_logo(); // phpcs:ignore -- escaped in helper. ?>

		<?php
		get_template_part(
			'parts/components/menu',
			null,
			array(
				'location' => 'main',
				'label'    => __( 'Main navigation', 'wp-gutenberg' ),
				'class'    => 'site-nav flex gap-[clamp(1rem,2.4vw,2.125rem)] text-md font-semibold',
				'nav_class' => 'max-nav:hidden',
			)
		);
		?>

		<?php if ( $header['button_enabled'] && $header['button_label'] ) : ?>
			<a class="btn btn--primary btn--sm max-nav:hidden" href="<?php echo esc_url( wpg_link_url( $header['button_url'] ?: '#appeler' ) ); ?>">
				<?php echo 'none' === $header['button_icon'] ? '' : wpg_icon( $header['button_icon'] ); // phpcs:ignore -- escaped in helper. ?>
				<?php echo esc_html( $header['button_label'] ); ?>
			</a>
		<?php endif; ?>

		<button type="button" class="grid size-12 flex-none place-items-center rounded-full bg-bordeaux text-cream nav:hidden" data-panel-open="menu" aria-haspopup="dialog" aria-expanded="false" aria-controls="panel" aria-label="<?php esc_attr_e( 'Open menu', 'wp-gutenberg' ); ?>">
			<?php echo wpg_icon( 'menu', 'size-[22px]' ); // phpcs:ignore -- escaped in helper. ?>
		</button>
	</div>
</header>
