<?php
/**
 * Closes the Barba container opened in header.php, then site footer.
 *
 * @package WPGutenberg
 */

?>
		</div>
	</main>

	<?php get_template_part( 'parts/site-footer' ); ?>

	<?php wp_footer(); ?>

	<div id="svg-sprite" hidden>
		<?php
		$sprite = WPG_DIR . '/public/sprite.svg';
		if ( is_file( $sprite ) ) {
			echo file_get_contents( $sprite ); // phpcs:ignore -- trusted build artefact.
		}
		?>
	</div>
</body>
</html>
