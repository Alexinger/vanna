<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package Dustland Express
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
} ?>

<div id="secondary" class="widget-area position-sticky" style="top: 15px;" role="complementary">
	<?php dynamic_sidebar( 'sidebar-1' ); ?>
</div><!-- #secondary -->