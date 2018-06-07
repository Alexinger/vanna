<?php
/**
* Plugin front end functions
* @author Nicolas GUILLAUME
* @since 1.0
*/
class TC_front_font_customizer {

    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;

    public $is_customizing;

    function __construct () {
        //add_action( 'init'                              , array( $this, 'tc_add_style' ) );
        add_action( 'init'                              , array( $this , 'tc_enqueue_plug_resources' ) , 0 );
        add_action( 'wp_head'                           , array( $this , 'tc_write_gfonts'), 0 );
        add_action( 'wp_head'                           , array( $this , 'tc_write_font_dynstyle'), 0 );
        add_action( 'wp_head'                           , array( $this , 'tc_write_other_dynstyle'), 999 );

        //$this -> is_customizing = isset($_REQUEST['wp_customize']) ? 1 : 0;
    }//end of construct



    function tc_write_gfonts() {
        $_opt_prefix              = TC_wfc::$instance -> plug_option_prefix;
        if ( ! get_option("{$_opt_prefix}_gfonts") ) {
            TC_utils_wfc::$instance -> tc_update_front_end_gfonts();
        }
        $families   = str_replace( '|', '%7C', get_option("{$_opt_prefix}_gfonts") );
        if ( empty($families) )
            return;

        printf('<link rel="stylesheet" id="tc-front-gfonts" href="%1$s">',
            "//fonts.googleapis.com/css?family={$families}"
        );
    }


    function tc_write_font_dynstyle() {
        ?>
        <style id="dyn-style-fonts" type="text/css">
            <?php do_action( '__dyn_style' , 'fonts' ); ?>
        </style>
        <?php
    }


    function tc_write_other_dynstyle() {
        ?>
        <style id="dyn-style-others" type="text/css">
            <?php do_action( '__dyn_style' , 'other' ); ?>
        </style>
        <?php
    }


    /* PLUGIN FRONT END FUNCTIONS */
    function tc_enqueue_plug_resources() {
         wp_enqueue_style(
          'font-customizer-style' ,
          sprintf('%1$s/front/assets/css/font_customizer%2$s.css' , TC_WFC_BASE_URL, ( defined('WP_DEBUG') && true === WP_DEBUG ) ? '' : '.min'),
          array(),
          TC_wfc::$instance -> plug_version,
          $media = 'all'
        );

        //register and enqueue jQuery if necessary
        if ( ! wp_script_is( 'jquery', $list = 'registered') ) {
            wp_register_script('jquery', '//code.jquery.com/jquery-latest.min.js', array(), false, false );
        }
        if ( ! wp_script_is( 'jquery', $list = 'enqueued') ) {
          wp_enqueue_script( 'jquery');
        }

        //WFC front scripts
        wp_enqueue_script(
            'font-customizer-script' ,
            sprintf('%1$s/front/assets/js/font-customizer-front%2$s.js' , TC_WFC_BASE_URL, ( defined('WP_DEBUG') && true === WP_DEBUG ) ? '' : '.min'),
            array('jquery'),
            TC_wfc::$instance -> plug_version,
            true
        );

        //localize font-customizer-script with settings fonts
        wp_localize_script(
          'font-customizer-script',
          'WfcFrontParams',
            array(
                'effectsAndIconsSelectorCandidates' => $this -> get_effect_and_icon_user_settings_localized_data_js()
            )
        );
    }

    //The saved options and $default_settings are formed like this :
    //[body] => Array
    //     (
    //         [zone] => body
    //         [selector] => body
    //         [not] => .social-block a, footer#footer .colophon .social-block a, .social-block.widget_social a
    //         [subset] =>
    //         [font-family] => Helvetica Neue, Helvetica, Arial, sans-serif
    //         [font-weight] => normal
    //         [font-style] =>
    //         [color] => #5A5A5A
    //         [font-size] => 14px
    //         [line-height] => 20px
    //         [text-align] => inherit
    //         [text-decoration] => none
    //         [text-transform] => none
    //         [letter-spacing] => 0
    //         [static-effect] => none
    //         [icon] =>
    //         [important] =>
    //         [title] =>
    //     )

    // [site_title] => Array
    //     (
    //         [zone] => header
    //         [selector] => .tc-header .brand .site-title
    //         [not] =>
    //         [subset] =>
    //         [font-family] => Helvetica Neue, Helvetica, Arial, sans-serif
    //         [font-weight] => bold
    //         [font-style] =>
    //         [color] => main
    //         [color-hover] => main
    //         [font-size] => 40px
    //         [line-height] => 38px
    //         [text-align] => inherit
    //         [text-decoration] => none!important
    //         [text-transform] => none
    //         [letter-spacing] => 0
    //         [static-effect] => none
    //         [icon] =>
    //         [important] =>
    //         [title] =>
    //     )
    //
    //@return array of effect or icon settings that needs front js treatments => add css classes for effect and hide icon
    function get_effect_and_icon_user_settings_localized_data_js() {
        $candidates = array();
        $default_settings = TC_wfc::$instance -> tc_get_selector_list();

        foreach ( TC_wfc::$instance -> tc_get_saved_option( null , false ) as $key => $data) {
              //Are we well formed ?
              if ( ! is_array( $data ) || ! array_key_exists('static-effect', $data ) || ! array_key_exists('icon', $data ) || ! array_key_exists('selector', $data ) || ! array_key_exists('not', $data ) )
                return array();

              //Do we have an effect set ?
              if ( ! empty( $data['static-effect'] ) && 'none' != $data['static-effect'] ) {
                  $candidates[ $key ] = array( 'static_effect' => $data['static-effect'] , 'static_effect_selector' => $data['selector'], 'static_effect_not_selector' => $data['not'] );
              }

              //Shall we hide the icon <= this is only relevant for Customizr theme, classical style.
              if ( 'hide' == $data['icon'] ) {
                  if ( is_array( $default_settings ) && array_key_exists( $key, $default_settings ) && is_array( $default_settings[ $key ] ) && array_key_exists( 'icon', $default_settings[ $key ] ) ) {
                      $candidates[ $key ] = array_key_exists( $key, $candidates ) && is_array( $candidates[ $key ] ) ? $candidates[ $key ] : array();

                      $candidates[ $key ] = array_merge( $candidates[ $key ], array( 'icon_state' => 'hidden', 'icon_selector' => $default_settings[ $key ][ 'icon' ] ) );
                  }
              }

              //Are we in a "not" case
        }

        return $candidates;
    }


} //end of class
