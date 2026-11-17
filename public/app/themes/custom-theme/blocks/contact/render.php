<?php
/**
 * Contact complet (F1) — front-end markup (mirror of edit.js).
 *
 * DOM order = mobile order: contact details (bordeaux) → form → quick question → map.
 * Desktop grid: the form spans the left column, details + quick question on the right. Kept on purpose
 * (WCAG 1.3.2 / 2.4.3): three independent cards, each with its own heading, so reading/focus order details →
 * form → quick question keeps its meaning on desktop too; reordering for desktop would break the mobile order.
 * The form is handled by WPGutenberg\Forms\ContactForm (admin-post.php, no JS); after the redirect,
 * ContactForm::state() gives the status, the field errors and the typed values.
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Unused (no inner blocks).
 * @var WP_Block             $block      Block instance.
 *
 * @package WPGutenberg
 */

use WPGutenberg\Forms\ContactForm;

$contact = wpg_contact();
$state   = ContactForm::state();
$has     = static fn( string $key ): bool => '' !== trim( wp_strip_all_tags( $attributes[ $key ] ?? '' ) );
$map_url = (string) ( $attributes['mapUrl'] ?? '' );
$map_url = str_starts_with( $map_url, 'https://' ) ? $map_url : '';

$details = array_filter(
	array(
		array( 'phone', __( 'Phone', 'wp-gutenberg' ), $contact['phone'], wpg_phone_url() ),
		array( 'mail', __( 'Email', 'wp-gutenberg' ), $contact['email'], 'mailto:' . $contact['email'] ),
		array( 'pin', __( 'Address', 'wp-gutenberg' ), $contact['address'], '' ),
		array( 'clock', __( 'Opening hours', 'wp-gutenberg' ), $contact['hours'], '' ),
		array( 'car', __( 'Access & parking', 'wp-gutenberg' ), $contact['access'], '' ),
	),
	static fn( array $detail ): bool => '' !== trim( $detail[2] )
);

$fields = array(
	'name'    => array( __( 'Your name', 'wp-gutenberg' ), 'text', 'name', true ),
	'email'   => array( __( 'Your email', 'wp-gutenberg' ), 'email', 'email', true ),
	'phone'   => array( __( 'Your phone (optional)', 'wp-gutenberg' ), 'tel', 'tel', false ),
	'message' => array( __( 'Your message', 'wp-gutenberg' ), 'textarea', 'off', true ),
);

$messages = array(
	'expired' => __( 'The page has expired. Please send your message again.', 'wp-gutenberg' ),
	'mail'    => __( 'Your message could not be sent because of a technical problem. Please try again, or contact us by phone or email.', 'wp-gutenberg' ),
	'invalid' => __( 'Your message has not been sent. Please correct the following fields:', 'wp-gutenberg' ),
	''        => __( 'Your message has not been sent. Please try again.', 'wp-gutenberg' ),
);

// 19 px line: fields 48 px, message (5 rows) 123 px, like the mock-up.
$field_class = 'min-h-12 w-full scroll-mt-12 rounded-2xl border-2 border-field bg-white px-4 py-3 text-base leading-[1.1875rem] text-ink aria-invalid:border-bordeaux';
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'wpg-contact pb-section-lg' ) ); // phpcs:ignore ?>>
	<div class="container flex flex-col gap-5 lg:gap-8">
		<div class="grid grid-cols-1 gap-5 lg:grid-cols-[minmax(0,1.05fr)_minmax(0,0.95fr)] lg:grid-rows-[1fr_auto] lg:gap-x-8 lg:gap-y-6">

			<div class="flex flex-col gap-5 rounded-panel bg-bordeaux p-7 text-cream shadow-card lg:col-start-2 lg:row-start-1 lg:gap-6.5 lg:p-10">
				<?php if ( $has( 'detailsTitle' ) ) : ?>
					<h2 class="script text-[2.5rem] text-gold-pale lg:text-[2.875rem]"><?php echo wp_kses_post( $attributes['detailsTitle'] ); ?></h2>
				<?php endif; ?>

				<?php if ( $details ) : ?>
					<ul class="flex list-none flex-col gap-4 p-0 lg:gap-4.5" role="list">
						<?php foreach ( $details as [ $icon, $label, $value, $href ] ) : ?>
							<li class="flex items-center gap-4">
								<span class="icon-dot size-12 bg-cream text-bordeaux">
									<?php echo wpg_icon( $icon, 'size-[22px]' ); // phpcs:ignore -- escaped in helper. ?>
								</span>
								<div class="leading-[1.45]">
									<strong class="block"><?php echo esc_html( $label ); ?></strong>
									<?php if ( $href ) : ?>
										<a class="text-note wrap-anywhere underline underline-offset-4" href="<?php echo esc_url( $href ); ?>"><?php echo esc_html( $value ); ?></a>
									<?php else : ?>
										<span class="text-note"><?php echo nl2br( esc_html( $value ) ); // phpcs:ignore -- escaped. ?></span>
									<?php endif; ?>
								</div>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<?php if ( '' !== $contact['phone'] ) : ?>
					<a class="btn btn--light min-h-[3.375rem] w-full text-bordeaux" href="<?php echo esc_url( wpg_phone_url() ); ?>">
						<?php echo wpg_icon( 'phone' ); // phpcs:ignore -- escaped in helper. ?>
						<?php esc_html_e( 'Call us', 'wp-gutenberg' ); ?>
					</a>
				<?php endif; ?>
			</div>

			<form id="<?php echo esc_attr( ContactForm::ANCHOR ); ?>" class="flex scroll-mt-6 flex-col gap-4 rounded-panel bg-cream p-6 lg:col-start-1 lg:row-span-2 lg:row-start-1 lg:p-10" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" aria-labelledby="wpg-contact-title">
				<h2 id="wpg-contact-title" class="script text-[2.375rem] text-bordeaux lg:text-[2.75rem]"><?php echo $has( 'formTitle' ) ? wp_kses_post( $attributes['formTitle'] ) : esc_html__( 'Contact form', 'wp-gutenberg' ); ?></h2>

				<?php
				/*
				 * Result of the submission. A live region present at page load is not announced reliably, so the
				 * message receives the focus, without JS: it is the target of the redirect (#wpg-contact-status,
				 * browsers focus a focusable fragment target) and `autofocus`. The page title is prefixed too
				 * (ContactForm::title_status()). Focusable wrapper + inner role, like the GOV.UK error summary.
				 */
				if ( $state['status'] ) :
					?>
					<div id="<?php echo esc_attr( ContactForm::STATUS_ANCHOR ); ?>" class="scroll-mt-20 rounded-box" tabindex="-1" autofocus>
						<?php if ( 'sent' === $state['status'] ) : ?>
							<div class="flex items-start gap-3 rounded-box border-2 border-olive-dark bg-white px-4 py-3 font-semibold text-ink" role="status">
								<?php echo wpg_icon( 'check', 'mt-0.5 size-5 flex-none text-olive-dark' ); // phpcs:ignore -- escaped in helper. ?>
								<p><?php esc_html_e( 'Thank you! Your message has been sent. We will get back to you as soon as possible.', 'wp-gutenberg' ); ?></p>
							</div>
						<?php else : ?>
							<div class="rounded-box border-2 border-bordeaux bg-white px-4 py-3 text-ink" role="alert">
								<p class="font-bold text-bordeaux"><?php echo esc_html( $messages[ $state['reason'] ] ?? $messages[''] ); ?></p>
								<?php if ( $state['errors'] ) : ?>
									<ul class="mt-2 list-disc pl-5 text-md">
										<?php foreach ( $state['errors'] as $field => $error ) : ?>
											<li><a class="underline underline-offset-4" href="#wpg-contact-<?php echo esc_attr( $field ); ?>"><?php echo esc_html( $error ); ?></a></li>
										<?php endforeach; ?>
									</ul>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php
				foreach ( $fields as $name => [ $label, $type, $autocomplete, $required ] ) :
					$id    = 'wpg-contact-' . $name;
					$error = $state['errors'][ $name ] ?? '';
					$attrs = wpg_attrs(
						array(
							'id'               => $id,
							'name'             => $name,
							'type'             => 'textarea' === $type ? null : $type,
							'class'            => $field_class . ( 'textarea' === $type ? ' resize-y' : '' ),
							'autocomplete'     => $autocomplete,
							'required'         => $required,
							'aria-required'    => $required ? 'true' : null,
							'aria-invalid'     => $error ? 'true' : null,
							'aria-describedby' => $error ? $id . '-error' : null,
							'rows'             => 'textarea' === $type ? '5' : null,
							'value'            => 'textarea' === $type ? null : $state['values'][ $name ],
						)
					);
					?>
					<div class="flex flex-col gap-1.5">
						<label class="text-[0.90625rem] font-bold text-bordeaux" for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label>
						<?php if ( 'textarea' === $type ) : ?>
							<textarea <?php echo $attrs; // phpcs:ignore -- escaped by wpg_attrs(). ?>><?php echo esc_textarea( $state['values'][ $name ] ); ?></textarea>
						<?php else : ?>
							<input <?php echo $attrs; // phpcs:ignore -- escaped by wpg_attrs(). ?>>
						<?php endif; ?>
						<?php if ( $error ) : ?>
							<p id="<?php echo esc_attr( $id . '-error' ); ?>" class="text-sm font-semibold text-bordeaux">
								<?php
								/* translators: %s: error message, or page title (contact form result) */
								echo esc_html( sprintf( __( 'Error: %s', 'wp-gutenberg' ), $error ) );
								?>
							</p>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>

				<div class="sr-only" aria-hidden="true">
					<label for="wpg-contact-<?php echo esc_attr( ContactForm::HONEYPOT ); ?>"><?php esc_html_e( 'Leave this field empty', 'wp-gutenberg' ); ?></label>
					<input id="wpg-contact-<?php echo esc_attr( ContactForm::HONEYPOT ); ?>" type="text" name="<?php echo esc_attr( ContactForm::HONEYPOT ); ?>" value="" tabindex="-1" autocomplete="off">
				</div>
				<input type="hidden" name="action" value="<?php echo esc_attr( ContactForm::ACTION ); ?>">
				<?php wp_nonce_field( ContactForm::ACTION, ContactForm::NONCE ); ?>

				<?php if ( $has( 'consent' ) ) : ?>
					<p class="text-xs text-ink-soft"><?php echo wp_kses_post( $attributes['consent'] ); ?></p>
				<?php endif; ?>

				<button class="btn btn--primary self-start" type="submit">
					<?php echo wpg_icon( 'mail' ); // phpcs:ignore -- escaped in helper. ?>
					<?php esc_html_e( 'Send the message', 'wp-gutenberg' ); ?>
				</button>
			</form>

			<div class="flex flex-col items-stretch gap-4 rounded-panel bg-cream px-6 py-6.5 lg:col-start-2 lg:row-start-2 lg:px-8 xl:flex-row xl:items-center xl:justify-between xl:gap-5">
				<div class="flex flex-col gap-1.5">
					<?php if ( $has( 'chatTitle' ) ) : ?>
						<h2 class="script text-[2.5rem] text-bordeaux"><?php echo wp_kses_post( $attributes['chatTitle'] ); ?></h2>
					<?php endif; ?>
					<?php if ( $has( 'chatText' ) ) : ?>
						<p class="text-md leading-normal text-ink-soft"><?php echo wp_kses_post( $attributes['chatText'] ); ?></p>
					<?php endif; ?>
				</div>
				<?php if ( $has( 'chatLabel' ) ) : ?>
					<a class="btn btn--primary btn--sm btn--block-mobile flex-none py-3.5 text-cream" href="<?php echo esc_url( wpg_chat_url() ); ?>">
						<?php echo wpg_icon( 'chat' ); // phpcs:ignore -- escaped in helper. ?>
						<span><?php echo wp_kses_post( $attributes['chatLabel'] ); ?></span>
					</a>
				<?php endif; ?>
			</div>
		</div>

		<?php if ( $map_url ) : ?>
			<div class="h-60 overflow-hidden rounded-panel bg-cream lg:h-80">
				<iframe class="size-full border-0" src="<?php echo esc_url( $map_url ); ?>" title="<?php esc_attr_e( 'Map: how to find the salon', 'wp-gutenberg' ); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
			</div>
		<?php endif; ?>
	</div>
</section>
