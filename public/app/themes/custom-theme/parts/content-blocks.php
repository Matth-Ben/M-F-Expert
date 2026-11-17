<?php
/**
 * Block content wrapper.
 *
 * `is-layout-constrained has-global-padding` reproduces the editor canvas root:
 * children get theme.json contentSize, `alignwide` gets wideSize, `alignfull` goes edge to edge.
 * Same rules in the editor and on the front = real WYSIWYG.
 *
 * @package WPGutenberg
 */

?>
<div class="entry-content is-layout-constrained has-global-padding">
	<?php
	/*
	 * A page always opens with the hero, which carries its only <h1> (WCAG 1.3.1 / 2.4.6). A page not opened
	 * in the editor since the hero became mandatory (e.g. empty legal pages) gets the default hero here —
	 * page title + breadcrumb, exactly what src/editor/hero-first.js inserts when the page is edited.
	 */
	if ( is_page() && ! has_block( 'wpg/hero' ) ) {
		echo render_block( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- block output, escaped in render.php.
			array(
				'blockName'    => 'wpg/hero',
				'attrs'        => array(),
				'innerBlocks'  => array(),
				'innerHTML'    => '',
				'innerContent' => array(),
			)
		);
	}

	the_content();
	?>
</div>
