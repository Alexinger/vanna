<?php
/**
*
* This program is a free software; you can use it and/or modify it under the terms of the GNU
* General Public License as published by the Free Software Foundation; either version 2 of the License,
* or (at your option) any later version.
*
* This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
* even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*
* You should have received a copy of the GNU General Public License along with this program; if not, write
* to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*
* @package   	Customizr
* @since     	1.0
* @author    	Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright 	Copyright (c) 2013-2016, Nicolas GUILLAUME
* @link      	http://presscustomizr.com/customizr
* @license   	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/


/**
* This is where Customizr starts. This file defines and loads the theme's components :
* => Constants : CUSTOMIZR_VER, TC_BASE, TC_BASE_CHILD, TC_BASE_URL, TC_BASE_URL_CHILD, THEMENAME, CZR_WEBSITE
* => Default filtered values : images sizes, skins, featured pages, social networks, widgets, post list layout
* => Text Domain
* => Theme supports : editor style, automatic-feed-links, post formats, navigation menu, post-thumbnails, retina support
* => Plugins compatibility : JetPack, bbPress, qTranslate, WooCommerce and more to come
* => Default filtered options for the customizer
* => Customizr theme's hooks API : front end components are rendered with action and filter hooks
*
* The method CZR__::czr_fn__() loads the php files and instantiates all theme's classes.
* All classes files (except the class__.php file which loads the other) are named with the following convention : class-[group]-[class_name].php
*
* The theme is entirely built on an extensible filter and action hooks API, which makes customizations easy and safe, without ever needing to modify the core structure.
* Customizr's code acts like a collection of plugins that can be enabled, disabled or extended.
*
* If you're not familiar with the WordPress hooks concept, you might want to read those guides :
* http://docs.presscustomizr.com/article/26-wordpress-actions-filters-and-hooks-a-guide-for-non-developers
* https://codex.wordpress.org/Plugin_API
*/

//Fire Customizr
require_once( get_template_directory() . '/core/init-base.php' );

// function true_remove_default_image_sizes( $sizes ) {
// unset( $sizes['thumbnail']); // отключит миниатюры
// unset( $sizes['medium']); // отключит средний размер
// unset( $sizes['large']); // отключит крупный размер
// unset( $sizes['medium_large']);
// // unset( $sizes['shop_thumbnail']);
// // unset( $sizes['shop_catalog']);
// unset( $sizes['shop_single']);
// unset( $sizes['tc-grid-full']);
// unset( $sizes['tc-grid']);
// unset( $sizes['tc-thumb']);
// unset( $sizes['slider-full']);
// unset( $sizes['slider']);
// unset( $sizes['fpc-size']);
// unset( $sizes['original']);

// return $sizes;
// }

// add_filter('intermediate_image_sizes_advanced', 'true_remove_default_image_sizes');

// // отключаем создание миниатюр файлов для указанных размеров
// add_filter( 'intermediate_image_sizes_advanced', function( $sizes ) {
// 	unset( $sizes['blog-large'] );
// 	unset( $sizes['blog-medium'] );
// 	unset( $sizes['tabs-img'] );
// 	unset( $sizes['related-img'] );
// 	unset( $sizes['portfolio-full'] );
 
// 	return $sizes;
// } );

function mythemename_remove_some_tn() {
	// Миниатюры для портфолио
 	remove_image_size( 'portfolio-full' );
 	remove_image_size( 'portfolio-one' );
 	remove_image_size( 'portfolio-two' );
 	remove_image_size( 'portfolio-three' );
 	remove_image_size( 'portfolio-five' );
 	remove_image_size( 'recent-works-thumbnail' );
 
	// Миниатюры для сеточного расположения элементов
	// remove_image_size( 'medium_large' );
}
add_action( 'after_setup_theme', 'mythemename_remove_some_tn', 99);


function woo_new_product_tab( $tabs )
{
 // Добавление новой вкладки
 $tabs['test_tab'] = array(
 'title' => __( 'Доставка', 'woocommerce' ),
 'priority' => 10,
 'callback' => 'woo_new_product_tab_content',
 );
 return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'woo_new_product_tab' );

// Содержимое новой вкладки
function woo_new_product_tab_content()
{
 echo do_shortcode("[wpmfc_short code='tov-dost']");
}


function woo_new_product_tab_garant( $tabs )
{
 // Добавление новой вкладки
 $tabs['garant_tab'] = array(
 'title' => __( 'Гарантия', 'woocommerce' ),
 'priority' => 11,
 'callback' => 'woo_new_product_tab_content_garant',
 );
 return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'woo_new_product_tab_garant' );
// Содержимое новой вкладки
function woo_new_product_tab_content_garant()
{
 echo do_shortcode("[wpmfc_short code='tov-garant']");
}




function woo_new_product_tab_oplata( $tabs )
{
 // Добавление новой вкладки
 $tabs['oplata_tab'] = array(
 'title' => __( 'Оплата', 'woocommerce' ),
 'priority' => 12,
 'callback' => 'woo_new_product_tab_content_oplata',
 );
 return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'woo_new_product_tab_oplata' );
// Содержимое новой вкладки
function woo_new_product_tab_content_oplata()
{
 echo do_shortcode("[wpmfc_short code='tov-oplata']");
}






add_shortcode('tov_title', 'tovar_title');

function tovar_title(){
    // $get = woocommerce_template_single_title();
    
    $get_tovar_title = "<span class='tovar-title'> " . esc_html(get_the_title($post)) . " </span>";
    return $get_tovar_title;
}

add_shortcode('cat_title', 'categoryes_title');

function categoryes_title(){
    $categories = get_queried_object()->name;
    return esc_html($categories);
}

add_filter( 'woocommerce_checkout_fields' , 'no_required_checkout_fields' );
 function no_required_checkout_fields( $fields ) {
 $fields['billing']['billing_last_name']['required'] = false;  /*Убрали обязательные поля в форме оформления заказа*/
 $fields['billing']['billing_address_1']['required'] = false;
 $fields['billing']['billing_phone']['required'] = false;
 $fields['billing']['billing_postcode']['required'] = false;
 $fields['billing']['billing_state']['required'] = false;
  $fields['shipping']['shipping_last_name']['required'] = false;  /*Убрали обязательные поля в форме оформления заказа*/
 $fields['shipping']['shipping_address_1']['required'] = false;
 $fields['shipping']['shipping_phone']['required'] = false;
 $fields['shipping']['shipping_postcode']['required'] = false;
 $fields['shipping']['shipping_state']['required'] = false;
 return $fields;
 }
add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields' );
 function custom_override_checkout_fields( $fields ) { /*Убрали ненужные поля формы оформления заказа*/
 unset($fields['billing']['billing_company']);
 unset($fields['billing']['billing_country']);
 unset($fields['billing']['billing_state']);
 unset($fields['billing']['billing_postcode']);
 unset($fields['shipping']['shipping_company']);
 unset($fields['shipping']['shipping_country']);
 unset($fields['shipping']['shipping_state']);
 unset($fields['shipping']['shipping_postcode']);
 unset($fields['shipping']['shipping_phone']);
 $fields['order']['order_comments']['placeholder'] = 'Можете указать то, что важно для вас, например, пожелания по доставке ...';
 return $fields;
  }
/**
* THE BEST AND SAFEST WAY TO EXTEND THE CUSTOMIZR THEME WITH YOUR OWN CUSTOM CODE IS TO CREATE A CHILD THEME.
* You can add code here but it will be lost on upgrade. If you use a child theme, you are safe!
*
* Don't know what a child theme is ? Then you really want to spend 5 minutes learning how to use child themes in WordPress, you won't regret it :) !
* https://codex.wordpress.org/Child_Themes
*
* More informations about how to create a child theme with Customizr : http://docs.presscustomizr.com/article/24-creating-a-child-theme-for-customizr/
* A good starting point to customize the Customizr theme : http://docs.presscustomizr.com/article/35-how-to-customize-the-customizr-wordpress-theme/
*/

