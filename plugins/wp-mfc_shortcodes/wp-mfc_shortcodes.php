<?php
/*
Plugin Name: WP-MFC Shortcodes
Version: 1.3
Description: WP-MFC Shortcode Plugin – это плагин для удобного и быстрого управления контентными блоками с помощью шоткодов для любого типа сайтов на wordpress.
Plugin URI: http://mfc.guru/
Author: wp-mfc team - разработка
Author URI: http://mfc.guru/

Copyright 2016  ( http://mfc.guru/ )

*/

define( 'TVE_SHORTCODES_PATH', plugin_dir_path( __FILE__ ) );

load_plugin_textdomain( 'tve-shortcode', false, TVE_SHORTCODES_PATH . '/lang' );

/**
*
*/
class tveshortcode
{
	private $wpdb;
    private $table;
    static $version="1.2";

	function __construct()
	{
		global $wpdb;
		$this->wpdb = $wpdb;
		$this->table = $wpdb->prefix.'tve_shortcode';

		if ( is_admin() ){
			add_action('admin_menu', array($this, 'admin_menu') );
			add_action('admin_enqueue_scripts', array($this, 'custom_admin_js') );
			add_action('media_buttons', array($this, 'add_select'), 11);
		}else{
			add_shortcode('tve_short', array($this, 'tve_short'));
			add_shortcode('wpmfc_short', array($this, 'tve_short'));
		}
		add_action('admin_init', array($this, 'check_update'));

	}

	public function check_update(){
		if (!class_exists('PucFactory')) {
			require 'plugin-updates/plugin-update-checker.php';
		}

		$update_file= 'http://cabinet.mfc.agency/wp-update-server/wpmfc_shortcode_info.json';

		$wpmfc_gi_update_checker= PucFactory::buildUpdateChecker(
			$update_file,
			__FILE__,
			'wp-mfc-shortcode'
		);
	}

	function admin_menu() {
		add_menu_page( 'WP-MFC Shortcode', 'WP-MFC Shortcode', 'administrator', 'shortcode', array($this, 'admin_list') );
		/*
		add_submenu_page( 'shortcode', 'Настройки', "Настройки", 'administrator', 'shortcode-settings', array($this, 'settings') );
		*/
	}

	function custom_admin_js(){
		wp_register_script( 'tveshortcode_admin_script', plugins_url( 'assets/admin.js', __FILE__ ), array('jquery') );
		wp_register_style('tveshortcode_admin_style', plugins_url( 'assets/admin.css', __FILE__ ) );
		wp_enqueue_style('tveshortcode_admin_style');
		wp_enqueue_script('tveshortcode_admin_script');
	}

	function admin_list(){
		include 'tpl/admin_list.php';
	}

	function add_select(){
		$list = get_option('tve_shortcode_list', array());
		$groups = array();
		foreach ($list as $key => $value)
			$groups[$value['group']] = 1;

		foreach ($groups as $key => $v) {
			echo '<select class="tve_select">';
				echo '<option value="0">'.$key.'</option>';
				foreach ($list as $value)
					if ($value['status'] and ($value['group'] == $key))
						echo '<option value="'.$value['code'].'">'.$value['description'].'</option>';

			echo '</select>';
		}
	}

	function tve_short($atts){
		if (isset($atts['code'])){
			$list = get_option('tve_shortcode_list', array());
			foreach ($list as $key => $value)
				if ($value['status'] and ($atts['code'] == $value['code'])){
					if (isset($value['noHTML']) and $value['noHTML'])
						return do_shortcode($value['text']);
					else
						return wpautop( do_shortcode($value['text']) );
			}
		}
		return '';
	}

}
$tveshortcode = new tveshortcode;
if (isset($_GET['page']) and ($_GET['page'] == 'shortcode') and isset($_GET['export'])){
	header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=short_export.txt');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');

    $list = get_option('tve_shortcode_list', array());
	if (!is_array($list))
		$list = array();

	echo serialize($list);
	die;
}
