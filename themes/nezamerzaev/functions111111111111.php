<?php
/**
 * Twenty Twelve functions and definitions
 *
 * Sets up the theme and provides some helper functions, which are used
 * in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 *
 * When using a child theme (see https://codex.wordpress.org/Theme_Development and
 * https://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are instead attached
 * to a filter or action hook.
 *
 * For more information on hooks, actions, and filters, @link https://codex.wordpress.org/Plugin_API
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */

add_filter( 'woocommerce_checkout_fields' , 'no_required_checkout_fields' );
 function no_required_checkout_fields( $fields ) {
 $fields['billing']['billing_last_name']['required'] = false;  /*Убрали обязательные поля в форме оформления заказа*/
 $fields['billing']['billing_address_1']['required'] = false;
 $fields['billing']['billing_phone']['required'] = false;
 $fields['billing']['billing_postcode']['required'] = false;
 $fields['billing']['billing_state']['required'] = false;
 return $fields;
 }
add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields' );
 function custom_override_checkout_fields( $fields ) { /*Убрали ненужные поля формы оформления заказа*/
 unset($fields['billing']['billing_company']);
 unset($fields['billing']['billing_country']);
 unset($fields['billing']['billing_address_2']);
 unset($fields['billing']['billing_state']);
 unset($fields['billing']['billing_postcode']);
 unset($fields['billing']['billing_last_name']);
 unset($fields['billing']['billing_first_name']);
 unset($fields['billing']['billing_address_1']);
 $fields['order']['order_comments']['placeholder'] = 'Можете указать то, что важно для вас, например, пожелания по доставке ...';
 return $fields;
 }