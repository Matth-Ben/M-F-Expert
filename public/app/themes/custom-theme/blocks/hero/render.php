<?php
/**
 * Hero — front-end markup (mirror of edit.js).
 *
 * Two layouts (attribute `variant`):
 * - `home` (B1): logo-text H1 (site name by default, « & » at half size), brand subtitle, gold rule,
 *   script signature, core/buttons (inner blocks), large emblem on the right;
 * - `page` (B2): breadcrumb, H1 (page title by default), gold rule, lead text, decorative emblem.
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks HTML (buttons, home only).
 * @var WP_Block             $block      Block instance.
 *
 * @package WPGutenberg
 */

$variant = 'home' === ( $attributes['variant'] ?? 'page' ) ? 'home' : 'page';
$plain   = static fn ( string $value ): string => trim( html_entity_decode( wp_strip_all_tags( $value ), ENT_QUOTES, 'UTF-8' ) );

if ( 'home' === $variant ) :
	$logo_text = $plain( $attributes['title'] ?? '' );
	$logo_text = '' !== $logo_text ? $logo_text : $plain( get_bloginfo( 'name' ) );
	// « M&F Expert »: the ampersand is set at half size, like the printed logo.
	$logo_html = str_replace( '&amp;', '<span class="mx-[0.05em] text-[0.5em]">&amp;</span>', esc_html( $logo_text ) );
	?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'wpg-hero wpg-hero--home pt-3.5 pb-14 lg:pt-11' ) ); // phpcs:ignore ?>>
	<div class="container grid items-center gap-12 lg:grid-cols-[minmax(0,1.08fr)_minmax(0,0.92fr)] lg:gap-16">
		<div class="flex flex-col">
			<h1 class="display whitespace-nowrap"><?php echo $logo_html; // phpcs:ignore -- escaped above. ?></h1>

			<?php if ( ! empty( $attributes['subtitle'] ) ) : ?>
				<p class="mt-1.5 text-[1.9375rem] leading-[1.05] font-extrabold tracking-[-0.04em] text-olive lg:text-[2.875rem]"><?php echo wp_kses_post( $attributes['subtitle'] ); ?></p>
			<?php endif; ?>

			<div class="rule mt-3.5 max-lg:w-14 lg:mt-5.5"></div>

			<?php if ( ! empty( $attributes['signature'] ) ) : ?>
				<p class="script mt-2.5 text-[1.9375rem] leading-[1.15] text-bordeaux lg:mt-3.5 lg:text-[2.5rem]"><?php echo wp_kses_post( $attributes['signature'] ); ?></p>
			<?php endif; ?>

			<?php if ( trim( $content ) ) : ?>
				<div class="mt-6 lg:mt-8.5"><?php echo $content; // phpcs:ignore -- core blocks output. ?></div>
			<?php endif; ?>
		</div>

		<div class="flex justify-center">
			<img
				src="<?php echo esc_url( wpg_logo_url( 'lg' ) ); ?>"
				alt="<?php echo esc_attr( wpg_logo_alt() ); ?>"
				width="620" height="590"
				class="h-auto w-70 lg:w-110"
				loading="eager" fetchpriority="high" decoding="async">
		</div>
	</div>
</section>
	<?php
	return;
endif;

// Page layout: empty title = page title.
$title     = '' !== trim( wp_strip_all_tags( $attributes['title'] ?? '' ) ) ? wp_kses_post( $attributes['title'] ) : esc_html( get_the_title() );
$long      = mb_strlen( $plain( $title ) ) > 16; // DS v2: 46 px on mobile, 39 px when the title is long.
$parent_id = (int) wp_get_post_parent_id( get_the_ID() );
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'wpg-hero wpg-hero--page pt-1 pb-11 lg:pt-6.5 lg:pb-18' ) ); // phpcs:ignore ?>>
	<div class="container grid items-center gap-10 lg:grid-cols-[minmax(0,1.3fr)_minmax(0,0.7fr)]">
		<div class="flex flex-col gap-3.5 lg:gap-5">
			<?php if ( ! is_front_page() ) : ?>
				<nav aria-label="<?php esc_attr_e( 'Breadcrumb', 'wp-gutenberg' ); ?>">
					<ol class="m-0 flex list-none flex-wrap items-center gap-x-2.5 p-0 text-sm font-semibold text-olive-dark">
						<li><a class="inline-flex min-h-11 items-center underline underline-offset-4" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'wp-gutenberg' ); ?></a></li>
						<?php if ( $parent_id ) : ?>
							<li class="text-gold-dark" aria-hidden="true">/</li>
							<li><a class="inline-flex min-h-11 items-center underline underline-offset-4" href="<?php echo esc_url( get_permalink( $parent_id ) ); ?>"><?php echo esc_html( get_the_title( $parent_id ) ); ?></a></li>
						<?php endif; ?>
						<li class="text-gold-dark" aria-hidden="true">/</li>
						<li class="font-bold text-bordeaux" aria-current="page"><?php echo esc_html( get_the_title() ); ?></li>
					</ol>
				</nav>
			<?php endif; ?>

			<h1 class="h1<?php echo $long ? ' max-lg:text-[2.4375rem]' : ''; ?>"><?php echo $title; // phpcs:ignore -- escaped above. ?></h1>

			<div class="rule max-lg:w-14"></div>

			<?php if ( ! empty( $attributes['text'] ) ) : ?>
				<p class="lead max-w-160"><?php echo wp_kses_post( $attributes['text'] ); ?></p>
			<?php endif; ?>
		</div>

		<div class="hidden justify-center lg:flex">
			<img src="<?php echo esc_url( wpg_logo_url( 'lg' ) ); ?>" alt="" width="620" height="590" class="h-auto w-75" loading="lazy" decoding="async"><?php // Lazy: hidden on mobile, so not downloaded there. ?>
		</div>
	</div>
</section>
