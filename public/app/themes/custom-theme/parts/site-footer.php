<?php
/**
 * A3 footer (rose wave, tagline, contact details, socials, legal links)
 * + A4 quick access: floating chat button (desktop) and fixed call/chat bar (mobile).
 */

$footer  = wpg_footer(); // Back-office « Pied de page » (includes/Admin/Footer.php).
$contact = wpg_contact();
$socials = wpg_socials();
$details = ! $footer['show_details'] ? array() : array_filter(
	array(
		array( 'pin', __( 'Address', 'wp-gutenberg' ), $contact['address'] ),
		array( 'clock', __( 'Opening hours', 'wp-gutenberg' ), $contact['hours'] ),
		array( 'car', __( 'Access & parking', 'wp-gutenberg' ), $contact['access'] ),
		array( 'mail', __( 'Email', 'wp-gutenberg' ), $contact['email'] ),
	),
	static fn( array $detail ): bool => '' !== $detail[2]
);
// Desktop: the social links sit bottom right, right where the floating chat button is (fixed, 86 px from the bottom).
// Extra bottom space keeps them clear of it when the page is scrolled to the end (WCAG 2.4.11).
$bottom_space = $socials ? 'lg:pb-18' : 'lg:pb-0';
?>
<footer class="site-footer relative mt-auto bg-bordeaux pt-10 pb-[4.75rem] text-cream lg:pt-20 <?php echo esc_attr( $bottom_space ); ?>">
	<svg class="absolute top-[-1px] left-0 block h-[50px] w-full lg:h-[90px]" aria-hidden="true" focusable="false" viewBox="0 0 1440 90" preserveAspectRatio="none">
		<path d="M0 0h1440v30C1200 80 960 70 720 40S240 10 0 60z" fill="var(--color-beige)"/>
		<path d="M0 62C240 12 480 42 720 42s480 38 720-12v20C1200 100 960 80 720 52S240 22 0 82z" fill="var(--color-rose)"/>
	</svg>

	<div class="container grid items-center gap-8 pt-11 pb-8 lg:grid-cols-[1.1fr_1fr] lg:gap-14 lg:pt-[70px]">
		<div class="flex flex-col items-start gap-4.5">
			<?php if ( $footer['tagline'] ) : ?>
				<p class="script text-[2rem] leading-[1.15] text-gold-pale lg:text-[2.75rem]"><?php echo esc_html( $footer['tagline'] ); ?></p>
			<?php endif; ?>
			<?php if ( $footer['button_enabled'] && $footer['button_label'] ) : ?>
				<a class="btn btn--light" href="<?php echo esc_url( wpg_link_url( $footer['button_url'] ?: '#appeler' ) ); ?>">
					<?php echo 'none' === $footer['button_icon'] ? '' : wpg_icon( $footer['button_icon'] ); // phpcs:ignore -- escaped in helper. ?>
					<?php echo esc_html( $footer['button_label'] ); ?>
				</a>
			<?php endif; ?>
		</div>

		<?php if ( $details ) : ?>
			<ul class="grid gap-4.5 text-md sm:grid-cols-2" role="list">
				<?php foreach ( $details as [ $icon, $label, $value ] ) : ?>
					<li class="flex gap-3">
						<?php echo wpg_icon( $icon, 'size-[22px] flex-none text-gold-pale' ); // phpcs:ignore -- escaped in helper. ?>
						<div>
							<strong class="block"><?php echo esc_html( $label ); ?></strong>
							<?php if ( 'mail' === $icon ) : ?>
								<a class="underline-offset-4 hover:underline" href="<?php echo esc_url( 'mailto:' . $value ); ?>"><?php echo esc_html( $value ); ?></a>
							<?php else : ?>
								<?php echo nl2br( esc_html( $value ) ); // phpcs:ignore -- escaped. ?>
							<?php endif; ?>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>

	<div class="container flex flex-wrap items-center justify-between gap-5 border-t border-cream/25 pt-6 pb-7 text-sm">
		<div class="flex flex-col gap-x-2 lg:flex-row lg:flex-wrap lg:items-center">
			<span>© <?php echo esc_html( trim( wp_date( 'Y' ) . ' ' . $footer['copyright'] ) ); ?></span>
			<?php
			get_template_part(
				'parts/components/menu',
				null,
				array(
					'location' => 'legals',
					'label'    => __( 'Legal information', 'wp-gutenberg' ),
					'class'    => 'footer-legals flex flex-wrap',
				)
			);
			?>
		</div>

		<?php if ( $socials ) : ?>
			<div class="flex items-center gap-3">
				<?php if ( $footer['social_title'] ) : ?>
					<span class="script text-[1.375rem]" aria-hidden="true"><?php echo esc_html( $footer['social_title'] ); ?></span>
				<?php endif; ?>
				<ul class="flex gap-3" role="list">
					<?php foreach ( $socials as $id => $social ) : ?>
						<li>
							<a class="grid size-11 place-items-center rounded-full bg-cream text-bordeaux" href="<?php echo esc_url( $social['url'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: social network */ __( '%s (new window)', 'wp-gutenberg' ), $social['title'] ) ); ?>">
								<?php echo wpg_icon( $id, 'size-5' ); // phpcs:ignore -- escaped in helper. ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>
	</div>

	<?php // Floating (fixed) but kept inside the footer landmark: last item of the page for keyboard and screen readers. ?>
	<a class="btn btn--chat fixed right-8 bottom-8 z-40 max-lg:hidden" href="<?php echo esc_url( wpg_chat_url() ); ?>">
		<?php echo wpg_icon( 'chat', 'size-[22px]' ); // phpcs:ignore -- escaped in helper. ?>
		<?php esc_html_e( 'A question?', 'wp-gutenberg' ); ?>
	</a>
</footer>

<nav class="quick-bar fixed inset-x-0 bottom-0 z-40 flex gap-2.5 bg-cream px-4 py-3 shadow-bar lg:hidden" aria-label="<?php esc_attr_e( 'Quick contact', 'wp-gutenberg' ); ?>">
	<a class="btn btn--primary min-h-[3.25rem] flex-1 px-4 py-2 text-md" href="<?php echo esc_url( wpg_phone_url() ); ?>">
		<?php echo wpg_icon( 'phone' ); // phpcs:ignore -- escaped in helper. ?>
		<?php esc_html_e( 'Call', 'wp-gutenberg' ); ?>
	</a>
	<a class="btn btn--outline min-h-[3.25rem] flex-1 px-4 py-2 text-md" href="<?php echo esc_url( wpg_chat_url() ); ?>">
		<?php echo wpg_icon( 'chat' ); // phpcs:ignore -- escaped in helper. ?>
		<?php esc_html_e( 'Chat', 'wp-gutenberg' ); ?>
	</a>
</nav>
