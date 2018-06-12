<?php
/**
 * Functions for users wanting to upgrade to premium
 *
 * @package Dustland Express
 */

/**
 * Display the upgrade to Premium page & load styles.
 */
function dustlandexpress_premium_admin_menu() {
    global $dustlandexpress_upgrade_page;
    $dustlandexpress_upgrade_page = add_theme_page( __( 'About Dustland Express', 'dustland-express' ), '<span class="premium-link">' . __( 'About Dustland Express', 'dustland-express' ) . '</span>', 'edit_theme_options', 'theme_info', 'dustlandexpress_render_upgrade_page' );
}
add_action( 'admin_menu', 'dustlandexpress_premium_admin_menu' );

/**
 * Enqueue admin stylesheet only on upgrade page.
 */
function dustlandexpress_load_upgrade_page_scripts() {
    global $pagenow;
	if ( $pagenow == 'themes.php' ) {
		wp_enqueue_style( 'dustlandexpress-upgrade-css', get_template_directory_uri() . '/upgrade/css/upgrade-admin.css' );
	    wp_enqueue_script( 'caroufredsel', get_template_directory_uri() . '/js/jquery.carouFredSel-6.2.1-packed.js', array( 'jquery' ), DUSTLANDEXPRESS_THEME_VERSION, true );
	    wp_enqueue_script( 'dustlandexpress-upgrade-js', get_template_directory_uri() . '/upgrade/js/upgrade-custom.js', array( 'jquery' ), DUSTLANDEXPRESS_THEME_VERSION, true );
	}
}
add_action( 'admin_enqueue_scripts', 'dustlandexpress_load_upgrade_page_scripts' );

/**
 * Render the premium page to download premium upgrade plugin
 */
function dustlandexpress_render_upgrade_page() {
	get_template_part( 'upgrade/tpl/upgrade-page' );
}
