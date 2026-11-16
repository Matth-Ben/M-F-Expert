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
	<?php the_content(); ?>
</div>
