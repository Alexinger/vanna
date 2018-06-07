<?php
/**
 * Plugin Name: WordPress Font Customizer
 * Plugin URI: http://presscustomizr.com/extension/wordpress-font-customizer
 * Description: Make beautiful Google font combinations and apply awesome CSS3 effects to any text of your website. Preview everything right from the WordPress customizer before publishing live. Cross browser compatible, fast and easy, the WordPress Font Customizer is the ultimate tool for typography lovers.
 * Version: 2.1.0
 * Author: Press Customizr
 * Author URI: http://presscustomizr.com
 * License: GPL2+
 */

/**
* Fires the plugin
* @author Nicolas GUILLAUME
* @since 1.0
*/
if ( ! class_exists( 'TC_wfc' ) ) :
class TC_wfc {
      //Access any method or var of the class with classname::$instance -> var or method():
      static $instance;
      public $version;
      public $plug_name;
      public $plug_file;
      public $plug_version;
      public $plug_prefix;
      public $plug_lang;
      public $tc_default_selector_list;
      public static $theme_name;
      public $tc_property_list;
      //public $tc_selector_list;
      public $tc_skin_colors;
      public $setting_prefix = 'tc_font_customizer_plug';
      public $is_customizing;


      public static $_is_new_version = false;
      public static $_is_plugin;
      public static $_selector_list;//<= build from the sets/json according to the theme name. Build on first load.

      function __construct () {

            self::$instance =& $this;

            /* LICENSE AND UPDATES */
            // the name of your product. This should match the download name in EDD exactly
            $this -> plug_name    = 'WordPress Font Customizer';
            $this -> plug_file    = __FILE__; //main plugin root file.
            $this -> plug_prefix  = 'font_customizer';
            $this -> plug_version = '2.1.0';
            $this -> plug_lang    = 'tc_font_customizer';

            self::$_is_plugin     = ! did_action('plugins_loaded');

            //define the plug option key
            $this -> plug_option_prefix     = self::$_is_plugin ? 'tc_wfc' : 'tc_pro_wfc';


            //Customizr Skin Colors
            $this -> tc_skin_colors = array(//color , color-hover

                  'blue.css'        =>  array( '#08c', '#005580' ),
                  'blue2.css'       =>  array( '#27CBCD', '#1b8b8d' ),
                  'blue3.css'       =>  array( '#27CDA5', '#1b8d71' ),
                  'green.css'       =>  array( '#9db668', '#768d44' ),
                  'green2.css'      =>  array( '#26CE61', '#1a8d43' ),
                  'yellow.css'      =>  array( '#e9a825', '#b07b12' ),
                  'yellow2.css'     =>  array( '#d2d62a', '#94971d' ),
                  'orange.css'      =>  array( '#F78C40', '#e16309' ),
                  'orange2.css'     =>  array( '#E79B5D', '#d87220' ),
                  'red.css'         =>  array( '#e10707', '#970505' ),
                  'red2.css'        =>  array( '#e7797a', '#db383a' ),
                  'purple.css'      =>  array( '#e67fb9', '#da3f96' ),
                  'purple2.css'     =>  array( '#8183D8', '#474ac6' ),
                  'grey.css'        =>  array( '#5A5A5A', '#343434' ),
                  'grey2.css'       =>  array( '#E4E4E4', '#bebebe' ),
                  'black.css'       =>  array( '#000', '#000000' ),

                  'black2.css'      =>  array( '#394143', '#16191a' )
            );

            //checks if is customizing : two context, admin and front (preview frame)
            $this -> is_customizing = $this -> tc_is_customizing();

            self::$theme_name = $this -> tc_get_theme_name();
            $theme_name       = self::$theme_name;


            //check if theme is customizr/hueman pro and plugin mode (did_action not triggered yet)
            if ( in_array( self::$theme_name, array( 'customizr-pro', 'hueman-pro' ) ) && self::$_is_plugin ) {
                add_action( 'admin_notices', array( $this , 'presscustomizr_pro_admin_notice' ) );
                return;
            }


            /* die if addon mode and previewing a different theme */
            if ( ( ! in_array( self::$theme_name, array( 'customizr-pro', 'hueman-pro' ) ) ) && !self::$_is_plugin ) {
                return;
            }


            //Some actions to do on new install or update
            $plug_options = self::tc_get_plug_options();

            //plug_options is empty -> first install :
            // or
            //plug options is array, plug_version field exists and shows a different version than the current one
            //do:
            // => write versions
            // => store static variable : is_new_version
            if (  empty( $plug_options ) || ( is_array( $plug_options ) && isset( $plug_options[ 'tc_plugin_version' ] ) && 0 != version_compare( $plug_options[ 'tc_plugin_version' ], $this->plug_version ) ) ){
                // => writes versions
                self::tc_write_versions( $plug_options );
                // => store static variable : is_new_version
                self::$_is_new_version = true;
            }


            //USEFUL CONSTANTS
            if ( ! defined( 'TC_WFC_DIR_NAME' ) )      { define( 'TC_WFC_DIR_NAME' , basename( dirname( __FILE__ ) ) ); }


            if ( ! defined( 'TC_WFC_BASE_URL' ) ) {

                //plugin context
                if ( ! ( defined( 'TC_BASE_URL' ) || defined( 'HU_BASE_URL' ) ) ) {
                    // 2 cases:
                    // a) in hueman pro addons
                    // b) standalone
                    //case a)
                    if ( method_exists( 'HU_AD',  'ha_is_pro_addons' ) && HU_AD()->ha_is_pro_addons() ) {
                        define( 'TC_WFC_BASE_URL' , HA_BASE_URL . 'addons/pro/' . TC_WFC_DIR_NAME );
                    }
                    //case b)
                    else {
                        define( 'TC_WFC_BASE_URL' , plugins_url( TC_WFC_DIR_NAME ) );
                    }
                } else { //addon context
                    //a) in Customizr-PRO
                    if ( defined( 'TC_BASE_URL' ) ) {
                        define( 'TC_WFC_BASE_URL' , sprintf('%s/%s' , TC_BASE_URL . 'addons' , TC_WFC_DIR_NAME ) );
                    }
                    //b) in Hueman-PRO
                    else {
                        define( 'TC_WFC_BASE_URL' , sprintf('%s/%s' , HU_BASE_URL . 'addons/pro' , TC_WFC_DIR_NAME ) );
                    }
                }
            }


            //adds plugin text domain
            add_action( 'plugins_loaded'                    , array( $this , 'tc_plugin_lang' ) );

            //adds custom selectors to theme defaults selectors list
            //see self::tc_get_selector_list()
            add_filter( "tc_default_selectors_{$theme_name}", array( $this , 'tc_add_customs_to_selector_list' ) );


            //Plugin mode only (Note for the future: get_stylesheet correctly returns the current stylesheet in the callbacks)
            //activation : delete the setting option
            register_activation_hook( __FILE__              , array( __CLASS__ , 'tc_wfc_clean_settings' ) );

            //check if Font Customizer WP.org is already install and enabled
            register_activation_hook( __FILE__              , array( __CLASS__   , 'tc_wfc_abort_if_font_customizer_enabled' ) );

            //add / register the following actions only in plugin context
            if ( ! did_action('plugins_loaded') ) {
                  //on theme switch
                  add_action( 'after_switch_theme'                , array( $this , 'tc_update_saved_options' ) );

                  register_deactivation_hook( __FILE__            , array( __CLASS__ , 'tc_wfc_clean_settings' ) );

                  //uninstall : clean database options
                  register_uninstall_hook( __FILE__               , array( __CLASS__ , 'tc_wfc_clean_db' ) );
            } else {
                  //adds plugin text domain when as addon in customizr-pro
                  add_action( 'after_setup_theme'                 , array( $this , 'tc_plugin_lang' ), 20 );
            }

            //LOAD FILES
            // 1) Loads utils now because TC_utils_wfc is used in hueman pro addons early to cache skope excluded settings
            // => @see HA_Skop_Option_Base::ha_cache_skope_excluded_settings() where we check if ( class_exists( 'TC_utils_wfc' ) )
            require_once ( dirname( __FILE__ ) . '/utils/classes/class_utils_wfc.php' );
            new TC_utils_wfc();

            //2) load other classes after setup theme, always, so that we have everything we need to know from the theme
            add_action( 'after_setup_theme'                       , array( $this, 'tc_wfc_load' ) );


            //Since the implementation of the modern style in the Customizr theme, in June 2017, there are 2 sets of selectors in sets/.....json
            //For this reason, we need to always make sure that the saved options are using the correct selectors.
            //a Saved option looks like this :
            //[site_title] => Array
            // (
            //     [zone] => header
            //     [selector] => .tc-header .brand .site-title <= THIS CAN BE DIFFERENT IN CLASSICAL AND MODERN STYLE FOR THE CUSTOMIZR THEME
            //     [not] =>
            //     [subset] =>
            //     [font-family] => Helvetica Neue, Helvetica, Arial, sans-serif
            //     [font-weight] => bold
            //     [font-style] =>
            //     [color] => main
            //     [color-hover] => main
            //     [font-size] => 40px
            //     [line-height] => 38px
            //     [text-align] => inherit
            //     [text-decoration] => none!important
            //     [text-transform] => none
            //     [letter-spacing] => 0
            //     [static-effect] => none
            //     [icon] =>
            //     [important] =>
            //     [title] =>
            // )
            //=> To solve this problem, we hook on after_setup_theme, where we know that the CZR_IS_MODERN_STYLE boolean is set in the customizr theme
            //and we compare this boolean value with a WFC dedicated transient, storing the last style applied.
            //if they don't match, then we updated the saved option selectors with the correct values.
            //
            //NOTE: must be fired after the utils class is loaded as when updating the options the raw options will merged
            // the raw options are in the form
            // array ( customized_zone_1 => array( property_1 => value_1 ), customized_zone_2 => array( property_2 => value_2 ), customized_zone_N => array( property_N=> value_N ) )
            // They are stored in the 'tc_font_customizer_plug' option, and retrieved via
            //   apply_filters( '__get_wfc_option'  , $selector , 'tc_font_customizer_plug' )
            // the __get_wfc_option callback is declared in the utils/class_utils_wfc.php contructor
            add_action( 'after_setup_theme'                       , array( $this, 'tc_maybe_update_saved_options_with_correct_style_selectors' ) );

        }//end construct


        //hook : 'after_setup_theme'
        function tc_maybe_update_saved_options_with_correct_style_selectors() {
            //Are we in the customizr theme and is the modern style implemented in this version of the themes
            if ( ! defined( 'CZR_IS_MODERN_STYLE' ) )
              return;

            $is_modern = CZR_IS_MODERN_STYLE;

            //do nothing if in the customizer (as the update is already triggered in that case) but saving the transient
            if ( $this -> is_customizing ) {
                set_transient( 'wfc_current_customizr_style', $is_modern ? 'modern' : 'classical', 60*60*24*365*20 );
                return;
            }

            $current_wfc_customizr_style = get_transient( 'wfc_current_customizr_style');
            //always update the options if the transient is not set yet.
            //If set, make sure it's matching with the current activated style in the Customizr theme and update not matching
            if ( ! $current_wfc_customizr_style ) {
                $this -> tc_update_saved_options();
                set_transient( 'wfc_current_customizr_style', $is_modern ? 'modern' : 'classical', 60*60*24*365*20 );
            } else {
                if ( ( 'modern' == $current_wfc_customizr_style && ! $is_modern ) || ( 'modern' != $current_wfc_customizr_style && $is_modern )) {
                    $this -> tc_update_saved_options();
                    set_transient( 'wfc_current_customizr_style', $is_modern ? 'modern' : 'classical', 60*60*24*365*20 );
                }
            }
        }




        //hook : 'after_setup_theme'
        //load classes after setup theme, always, so that we have everything we need to know from the theme
        function tc_wfc_load() {
            //here would be where we can safely cache  the selectors (as property of this class ) getting rid of the transient and the selectors option
            $_activation_classes = array(

                  'TC_activation_key'             => array('/back/classes/activation-key/activation/class_activation_key.php', array(  $this -> plug_name , $this -> plug_prefix , $this -> plug_version )),
                  'TC_plug_updater'               => array('/back/classes/activation-key/updates/class_plug_updater.php'),
                  'TC_check_updates'              => array('/back/classes/activation-key/updates/class_check_updates.php', array(  $this -> plug_name , $this -> plug_prefix , $this -> plug_version, $this -> plug_file ))

            );

            $_plug_core_classes = array(

                  //the admin notices
                  //'TC_wfc_admin_notices'   => array('/back/classes/class_font_customizer_admin_notices.php', array( $this -> plug_name , $this -> plug_prefix )),
                  //'TC_utils_wfc'                  => array('/utils/classes/class_utils_wfc.php'),
                  'TC_admin_font_customizer'      => array('/back/classes/class_admin_font_customizer.php'),
                  'TC_back_system_info'           => array('/back/classes/class_back_system_info.php'),
                  'TC_front_font_customizer'      => array('/front/classes/class_front_font_customizer.php'),
                  'TC_dyn_style'                  => array('/front/classes/class_dyn_style.php'),

            );//end of plug_classes array

            $plug_classes       =  self::$_is_plugin ? $_plug_core_classes : array_merge($_activation_classes , $_plug_core_classes);




            //loads and instanciates the plugin classes
            foreach ($plug_classes as $name => $params) {

                  //don't load admin classes if not admin && not customizing
                  if ( is_admin() && ! $this -> is_customizing ) {

                        if ( false != strpos($params[0], 'front') )
                              continue;
                  }

                  if ( ! is_admin() && ! $this -> is_customizing ) {

                        if ( false != strpos($params[0], 'back') )
                              continue;

                  }

                  if( !class_exists( $name ) && file_exists( dirname( __FILE__ ) . $params[0] ) )
                        require_once ( dirname( __FILE__ ) . $params[0] );

                  if( class_exists( $name ) ) {

                    $args = isset( $params[1] ) ? $params[1] : null;

                        if ( $name !=  'TC_plug_updater' ) {
                              new $name( $args );
                        }

                  }

            }

      }//end load


      /**
      * @uses  wp_get_theme() the optional stylesheet parameter value takes into account the possible preview of a theme different than the one activated
      *
      * @return  theme name string
      */
      function tc_get_theme_name() {

            // $_REQUEST['theme'] is set both in live preview and when we're customizing a non active theme
            $stylesheet = $this -> is_customizing && isset($_REQUEST['theme']) ? $_REQUEST['theme'] : ''; //old wp versions
            $stylesheet = $this -> is_customizing && isset($_REQUEST['customize_theme']) ? $_REQUEST['customize_theme'] : $stylesheet;

            //gets the theme name (or parent if child)
            $tc_theme               = wp_get_theme($stylesheet);
            $theme_name             = $tc_theme -> parent() ? $tc_theme -> parent() -> Name : $tc_theme-> Name;
            return sanitize_file_name( strtolower($theme_name) );

      }


      /**
      * Returns a boolean on the customizer's state
      *
      */
      function tc_is_customizing() {
            //checks if is customizing : two contexts, admin and front (preview frame)
            global $pagenow;
            $is_customizing = false;
            if ( is_admin() && isset( $pagenow ) && 'customize.php' == $pagenow ) {
            $is_customizing = true;
            } else if ( is_customize_preview() || ( ! is_admin() && isset($_REQUEST['customize_messenger_channel']) ) ) {
            $is_customizing = true;
            }
            return $is_customizing;
      }



      //declares the translation domain
      function tc_plugin_lang() {
            //declares the plugin translation domain
            if ( current_filter() == 'plugins_loaded' )
              load_plugin_textdomain( $this -> plug_lang , false, basename( dirname( __FILE__ ) ) . '/lang' );
            else { // load textdomain as addon
              $locale = apply_filters( 'theme_locale', get_locale(), $this -> plug_lang );
              $mofile = $this -> plug_lang . '-' . $locale . '.mo';
              load_textdomain( $this -> plug_lang , dirname( __FILE__ ) . '/lang/' . $mofile );
            }
      }



      function tc_get_property_list() {
            //Default property list
            $tc_selector_properties     = array(
                        'zone'            => null,
                        'selector'        => null,
                        'not'             => null,
                        'subset'          => null,
                        'font-family'     => 'Helvetica Neue, Helvetica, Arial, sans-serif',
                        'font-weight'     => 'normal',
                        'font-style'      => null,
                        'color'           => 'main',
                        'color-hover'     => 'main',
                        'font-size'       => "14px",
                        'line-height'     => "20px",
                        'text-align'      => 'inherit',
                        'text-decoration' => 'none',
                        'text-transform'  => 'none',
                        'letter-spacing'  => 0,
                        'static-effect'   => 'none',
                        'icon'            => false,
                        'important'       => false,
                        'title'           => false //used for custom selectors
            );
            return apply_filters('tc_selector_properties' , $tc_selector_properties);
      }


      //hook :  tc_default_selectors_{$theme_name}
      function tc_add_customs_to_selector_list( $selectors ) {
            $theme_name   = self::$theme_name;
            $_opt_prefix  = TC_wfc::$instance -> plug_option_prefix;
            //@todo performance ?
            //first check if option exists and get it, else create/update option
            if ( ! get_option("{$_opt_prefix}_customs_{$theme_name}" ) )
                  return $selectors;

            $customs = get_option("{$_opt_prefix}_customs_{$theme_name}");
            return ( is_array($customs) && !empty($customs) ) ? apply_filters( "tc_all_selectors_{$theme_name}" , array_merge( $selectors , $customs ) ) : $selectors;
      }




      function _clean_selector_css( $_to_return ) {
            if ( ! is_array($_to_return) ) {
                  $_to_return = html_entity_decode($_to_return);
            }
            else {
                  foreach ( $_to_return as $selector => $data ) {
                        $_to_return[$selector]['selector'] = html_entity_decode($_to_return[$selector]['selector']);
                  }
            }
            return $_to_return;
      }



      function tc_get_custom_selector_list() {
            $theme_name       = self::$theme_name;
            $_opt_prefix      = TC_wfc::$instance -> plug_option_prefix;
            $_to_return       = get_option("{$_opt_prefix}_customs_{$theme_name}");
            $_to_return       = $this -> _clean_selector_css($_to_return);
            return $_to_return;
      }


      //This methods returns an array formed like :
      //Array
      // (
      //     [body] => Array
      //         (
      //             [zone] => body
      //             [selector] => body
      //             [not] => .social-block a, footer#footer .colophon .social-block a, .social-block.widget_social a
      //             [subset] =>
      //             [font-family] => Helvetica Neue, Helvetica, Arial, sans-serif
      //             [font-weight] => normal
      //             [font-style] =>
      //             [color] => #5A5A5A
      //             [font-size] => 14px
      //             [line-height] => 20px
      //             [text-align] => inherit
      //             [text-decoration] => none
      //             [text-transform] => none
      //             [letter-spacing] => 0
      //             [static-effect] => none
      //             [icon] =>
      //             [important] =>
      //             [title] =>
      //         )

      //     [site_title] => Array
      //         (
      //             [zone] => header
      //             [selector] => .tc-header .brand .site-title
      //             [not] =>
      //             [subset] =>
      //             [font-family] => Helvetica Neue, Helvetica, Arial, sans-serif
      //             [font-weight] => bold
      //             [font-style] =>
      //             [color] => main
      //             [color-hover] => main
      //             [font-size] => 40px
      //             [line-height] => 38px
      //             [text-align] => inherit
      //             [text-decoration] => none!important
      //             [text-transform] => none
      //             [letter-spacing] => 0
      //             [static-effect] => none
      //             [icon] =>
      //             [important] =>
      //             [title] =>
      //         )
      //
      // Until June 2017, the selector list was saved in a 24 hours transient
      // Now, it is always parsed from the json on first load, and stored as a property of this class.
      function tc_get_selector_list() {
          $theme_name = $json_theme_name = self::$theme_name;
          $path       = dirname( __FILE__).'/sets/';

          if ( defined( 'CZR_IS_MODERN_STYLE' ) && CZR_IS_MODERN_STYLE ) {
              $json_theme_name = $theme_name . '_modern';
          }

          //Did we already parse the json and store it ?
          if ( isset( self::$_selector_list ) && is_array( self::$_selector_list ) && ! empty( self::$_selector_list ) )
            return self::$_selector_list;

          $default_selector_settings       = file_exists("{$path}{$json_theme_name}.json") ? @file_get_contents( "{$path}{$json_theme_name}.json" ) : @file_get_contents( "{$path}default.json" );
          if ( $default_selector_settings === false ) {
                $default_selector_settings = ! wp_remote_fopen( sprintf( "%s/sets/{$json_theme_name}.json" , TC_WFC_BASE_URL ) ) ? wp_remote_fopen( sprintf( "%s/sets/default.json" , TC_WFC_BASE_URL ) ) : wp_remote_fopen( sprintf( "%s/sets/{$json_theme_name}.json" , TC_WFC_BASE_URL ) );
          }

          $default_selector_settings    = json_decode( $default_selector_settings , true );
          $default_selector_settings    = isset( $default_selector_settings['default'] ) ? $default_selector_settings['default'] : $default_selector_settings;

          $property_list                = $this -> tc_get_property_list();
          $property_list_keys           = array_keys($property_list);

          $selector_list = array();
          foreach ($default_selector_settings as $sel => $sets) {
                foreach ($sets as $key => $value) {
                      $prop                           = $property_list_keys[$key];
                      switch ($prop) {
                            case 'color-hover':
                                  if ( 0 === $value )
                                        continue;
                                  //if color-hover set to 1 then it is the main color, else there's a custom value
                                  $selector_list[$sel][$prop]    = ( 1 === $value ) ? $property_list[$prop] : $value;
                            break;

                            default:
                                  $selector_list[$sel][$prop]    = ( 0 === $value ) ? $property_list[$prop] : $value;
                            break;
                      }

               }
          }

           //html_entity_decode for selector => fixes characters (unrecognized expression) issue in javascript
          $_to_return  = apply_filters("tc_default_selectors_{$theme_name}" , $selector_list);
          $_to_return  = $this -> _clean_selector_css($_to_return);

          //Let's store it
          self::$_selector_list = $_to_return;

          return $_to_return;
      }


      //Merges the default settings with the saved options
      //This is fired :
      //1) on 'customize_save_after'
      //2) in self::tc_get_saved_option()
      function tc_update_saved_options( $return = false ) {
          $default_options    = $this -> tc_get_selector_list();
          $saved_options      = array( 'settings' => array() , 'bools' => array() );
          $_opt_prefix        = TC_wfc::$instance -> plug_option_prefix;

          //we can safely cycle only over the current theme default options as the actual customized options
          //are stored in the 'tc_font_customizer_plug' option
          foreach( $default_options as $selector => $settings ) {
                //settings
                $set_raw                                    = (array)json_decode( apply_filters( '__get_wfc_option'  , $selector , 'tc_font_customizer_plug' ) );
                $saved_options['settings'][$selector]       = array_merge( $set_raw , array_diff_key( $default_options[$selector] , $set_raw ) );
                //html_entity_decode for selector => fixes characters (unrecognized expression) issue in javascript
                $saved_options['settings'][$selector]['selector'] = $this -> _clean_selector_css($saved_options['settings'][$selector]['selector']);

                //bools
                $saved_options['bools'][$selector]          = $set_raw ? true : false;
                $saved_options['bools']['hassavedsets']     = isset($saved_options['bools']['hassavedsets']) ? $saved_options['bools']['hassavedsets'] : false;
                $saved_options['bools']['hassavedsets']     = $saved_options['bools'][$selector] ? true : $saved_options['bools']['hassavedsets'];
          }//end foreach

          update_option("{$_opt_prefix}_saved_options" , $saved_options );

          if ( true == $return )
            return $saved_options;
      }


      //Utility fired :
      //1) in back\classes\class_admin_font_customizer.php => when building the localized params 'DBSettings'           => TC_wfc::$instance -> tc_get_saved_option( null , false ),
      //2) in front\classes\class_dyn_style.php => in tc_render_dyn_style(), $merged_settings_with_selector  = TC_wfc::$instance -> tc_get_saved_option( 'selector_in_key' , false );
      //3) in front\classes\class_front_font_customizer.php => foreach ( TC_wfc::$instance -> tc_get_saved_option( null , false ) as $key => $data) {...}
      function tc_get_saved_option( $selector_in_key = null , $bool = false ) {
            $_opt_prefix  = TC_wfc::$instance -> plug_option_prefix;
            $saved        = array();

            //First case : typically when the method is fired to build the DBSettings localized js params
            if ( $this -> is_customizing || is_admin() ) {
                $saved = $this -> tc_update_saved_options( $return = true );
            } else {
                $saved = get_option( "{$_opt_prefix}_saved_options" );
                $saved = false != $saved ? $saved : $this -> tc_update_saved_options( $return = true );
            }

            if ( 'selector_in_key' == $selector_in_key ) {
                  foreach ( $saved['settings'] as $selector => $settings ) {
                        $new_val                            = $saved['settings'][$selector];
                        unset($saved['settings'][$selector]);
                        $new_key                            = $selector.'|'.$settings['selector'];
                        $saved['settings'][$new_key]        = $new_val;
                  }
                  return $saved['settings'];
            }
            if ( true == $bool ) {
                  return $saved['bools'];
            }

            return $saved['settings'];
      }




      function tc_get_raw_option() {
            $saved_options = get_option( 'tc_font_customizer_plug' );
            if ( empty($saved_options) )
                  return;
            foreach( $saved_options as $selector => $settings ) {
                  $saved_options[$selector] = (array)json_decode($saved_options[$selector]);
            }
            return $saved_options;
      }





      //@todo : include custom selector options ?
      public static function tc_wfc_clean_db() {
            $theme_name = self::$theme_name;

            //OPTIONS
            $options = array(
                  "tc_font_customizer_last_modified",
                  "tc_font_customizer_plug",
                  "tc_font_customizer_selectors_{$theme_name}"
            );
            foreach ($options as $value) {
                  delete_option($value);
            }

            //TRANSIENT
            delete_transient('tc_gfonts');
            delete_transient('czr_gfonts_june_2017');
      }


      public static function tc_wfc_clean_settings() {
            $theme_name = self::$theme_name;
            delete_option("tc_font_customizer_selectors_{$theme_name}");
      }

      //write current and previous version => used for system infos
      public static function tc_write_versions( $plug_options = null ) {
            //Gets options
            $plug_options = ! is_array( $plug_options ) ? self::$tc_get_plug_options() : $plug_options;

            //Adds Upgraded From Option
            if ( isset($plug_options['tc_plugin_version']) ) {
                  $plug_options['tc_upgraded_from'] = $plug_options['tc_plugin_version'];
            }
            //Sets new version
            $plug_options['tc_plugin_version'] = TC_wfc::$instance -> plug_version;
            //Updates
            update_option( TC_wfc::$instance -> plug_option_prefix , $plug_options );
      }

      //get current version
      public static function tc_get_plug_options() {

            //Gets options
            return get_option(TC_wfc::$instance -> plug_option_prefix) ? get_option(TC_wfc::$instance -> plug_option_prefix) : array();

      }

      public static function tc_wfc_abort_if_font_customizer_enabled() {
            if ( class_exists('TC_font_customizer') )
                  //add_action( 'admin_notices', array(TC_wfc::$instance , 'my_admin_notice' ));
                  wp_die( sprintf('The <strong>Font Customizer</strong> plugin has to be disabled before enabling the WordPress Font Customizer.</br><a href="%1$s">&laquo; Back to plugin\'s page</a>' , admin_url() . 'plugins.php' ) );

      }


      function my_admin_notice() {
          ?>
          <div class="updated">
              <p><?php _e( 'Updated!', 'my-text-domain' ); ?></p>
          </div>
         <?php
      }

      function presscustomizr_pro_admin_notice() {
          ?>
          <div class="error">
              <p>
                <?php
                printf( __( 'The <strong>%s</strong> plugin must be disabled since it is included in this theme. Open the <a href="%s">plugins page</a> to desactivate it.' , $this -> plug_lang ),
                  $this -> plug_name,
                  admin_url('plugins.php')
                  );
                ?>
              </p>
          </div>
          <?php
      }

}//end of class

//Creates a new instance of front and admin
new TC_wfc;

endif;

/**
* The tc__f() function is an extension of WP built-in apply_filters() where the $value param becomes optional.
* It is shorter than the original apply_filters() and only used on already defined filters.
* Can pass up to five variables to the filter callback.
*
* @since TCF 1.0
*/

if( ! function_exists( 'tc__f' )) :
    function tc__f ( $tag , $value = null , $arg_one = null , $arg_two = null , $arg_three = null , $arg_four = null , $arg_five = null) {
       return apply_filters( $tag , $value , $arg_one , $arg_two , $arg_three , $arg_four , $arg_five );
    }
endif;
