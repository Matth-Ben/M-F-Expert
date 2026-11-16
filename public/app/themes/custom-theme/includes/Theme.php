<?php
/**
 * Theme service container.
 *
 * To add a feature: create a class in `includes/` with a `register()` method
 * that only hooks into WordPress, then add it to SERVICES.
 *
 * @package WPGutenberg
 */

namespace WPGutenberg;

/**
 * Boots every theme service.
 */
final class Theme {

	/**
	 * Services booted on every request, in order.
	 *
	 * @var array<class-string>
	 */
	private const SERVICES = array(
		Core\Setup::class,
		Core\Cleanup::class,
		Core\Media::class,
		Core\Assets::class,
		Editor\Blocks::class,
		Editor\Editor::class,
		Admin\Socials::class,
		Fragments\Rest::class,
	);

	/**
	 * Instantiate services and let them register their hooks.
	 *
	 * @return void
	 */
	public static function boot(): void {
		foreach ( self::SERVICES as $service ) {
			( new $service() )->register();
		}
	}
}
