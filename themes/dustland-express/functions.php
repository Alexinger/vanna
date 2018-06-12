<?php
/**
 * Dustland Express functions and definitions
 *
 * @package Dustland Express
 */
define( 'DUSTLANDEXPRESS_THEME_VERSION' , '1.2.93' );

// Upgrade / Order Premium page
require get_template_directory() . '/upgrade/upgrade.php';

// Load WP included scripts
require get_template_directory() . '/includes/inc/template-tags.php';
require get_template_directory() . '/includes/inc/extras.php';
require get_template_directory() . '/includes/inc/jetpack.php';
require get_template_directory() . '/includes/inc/customizer.php';

// Load Customizer Library scripts
require get_template_directory() . '/customizer/customizer-options.php';
require get_template_directory() . '/customizer/customizer-library/customizer-library.php';
require get_template_directory() . '/customizer/styles.php';
require get_template_directory() . '/customizer/mods.php';

// Load TGM plugin class
require_once get_template_directory() . '/includes/inc/class-tgm-plugin-activation.php';
// Add customizer Upgrade class
require_once( get_template_directory() . '/includes/dustland-express-pro/class-customize.php' );

if ( ! function_exists( 'dustlandexpress_theme_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function dustlandexpress_theme_setup() {
	
	/**
	 * Set the content width based on the theme's design and stylesheet.
	 */
	global $content_width;
	if ( ! isset( $content_width ) ) {
		$content_width = 640; /* pixels */
	}

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on Dustland Express, use a find and replace
	 * to change 'dustland-express' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'dustland-express', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );
    add_image_size( 'blog_img_side', 352, 230, true );
    add_image_size( 'blog_img_top', 1100, 440, true );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'dustland-express' ),
		'header-bar' => __( 'Header Bar Menu', 'dustland-express' ),
        'footer-bar' => __( 'Footer Bar Menu', 'dustland-express' )
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
	) );

	/*
	 * Enable support for Post Formats.
	 * See http://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array(
		'aside', 'image', 'video', 'quote', 'link',
	) );
	
	// The custom header is used for the logo
	add_theme_support( 'custom-header', array(
        'default-image' => '',
		'width'         => 280,
		'height'        => 91,
		'flex-width'    => true,
		'flex-height'   => true,
		'header-text'   => false,
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'dustlandexpress_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );
    
    add_theme_support( 'title-tag' );
	
	add_theme_support( 'woocommerce' );
}
endif; // dustlandexpress_theme_setup
add_action( 'after_setup_theme', 'dustlandexpress_theme_setup' );

/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function dustlandexpress_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'dustland-express' ),
		'id'            => 'sidebar-1',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>'
	) );
	
	register_sidebar(array(
		'name' => __( 'Dustland Express Footer', 'dustland-express' ),
		'id' => 'dustlandexpress-site-footer',
        'description' => __( 'The footer will divide into however many widgets are put here.', 'dustland-express' )
	));
}
add_action( 'widgets_init', 'dustlandexpress_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function dustlandexpress_theme_scripts() {
    wp_enqueue_style( 'dustlandexpress-google-body-font-default', '//fonts.googleapis.com/css?family=Ubuntu:300,400,500,700,300italic,400italic,500italic,700italic', array(), DUSTLANDEXPRESS_THEME_VERSION );
    wp_enqueue_style( 'dustlandexpress-google-heading-font-default', '//fonts.googleapis.com/css?family=Roboto:500,400italic,700italic,300,700,500italic,300italic,400', array(), DUSTLANDEXPRESS_THEME_VERSION );
    
	wp_enqueue_style( 'dustlandexpress-font-awesome', get_template_directory_uri().'/includes/font-awesome/css/font-awesome.css', array(), '4.7.0' );
	wp_enqueue_style( 'dustlandexpress-style', get_stylesheet_uri(), array(), DUSTLANDEXPRESS_THEME_VERSION );
    
    if ( dustlandexpress_is_woocommerce_activated() ) {
        wp_enqueue_style( 'dustlandexpress-woocommerce-style', get_template_directory_uri().'/templates/css/dustlandexpress-woocommerce-style.css', array(), DUSTLANDEXPRESS_THEME_VERSION );
    }
	
	if ( get_theme_mod( 'kra-header-layout' ) == 'kra-header-layout-centered' ) {
		wp_enqueue_style( 'dustlandexpress-header-style', get_template_directory_uri().'/templates/css/dustlandexpress-header-centered.css', array(), DUSTLANDEXPRESS_THEME_VERSION );
	} else {
		wp_enqueue_style( 'dustlandexpress-header-style', get_template_directory_uri().'/templates/css/dustlandexpress-header-standard.css', array(), DUSTLANDEXPRESS_THEME_VERSION );
	}

	wp_enqueue_script( 'dustlandexpress-caroufredSel', get_template_directory_uri() . '/js/jquery.carouFredSel-6.2.1-packed.js', array('jquery'), DUSTLANDEXPRESS_THEME_VERSION, true );
	
	if ( get_theme_mod( 'kra-sticky-header' ) ) {
		wp_enqueue_script( 'dustlandexpress-waypoints', get_template_directory_uri() . '/js/waypoints.min.js', array('jquery'), DUSTLANDEXPRESS_THEME_VERSION, true );
	    wp_enqueue_script( 'dustlandexpress-waypoints-sticky', get_template_directory_uri() . '/js/waypoints-sticky.min.js', array('jquery'), DUSTLANDEXPRESS_THEME_VERSION, true );
	}
	
	wp_enqueue_script( 'dustlandexpress-customjs', get_template_directory_uri() . '/js/custom.js', array('jquery'), DUSTLANDEXPRESS_THEME_VERSION, true );

	wp_enqueue_script( 'dustlandexpress-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), DUSTLANDEXPRESS_THEME_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'dustlandexpress_theme_scripts' );

/**
 * Print Dustland Express styling settings.
 */
function dustlandexpress_print_styles(){
    $custom_css = '';
    if ( get_theme_mod( 'kra-custom-css' ) ) {
        $custom_css = get_theme_mod( 'kra-custom-css' );
    } ?>
    <style type="text/css" media="screen">
        <?php echo htmlspecialchars_decode( $custom_css ); ?>
    </style>
<?php
}
add_action('wp_head', 'dustlandexpress_print_styles', 11);

/**
 * Enqueue admin styling.
 */
function dustlandexpress_load_admin_script() {
    wp_enqueue_style( 'dustlandexpress-admin-css', get_template_directory_uri() . '/upgrade/css/admin-css.css' );
}
add_action( 'admin_enqueue_scripts', 'dustlandexpress_load_admin_script' );

/**
 * Enqueue Dustland Express custom customizer styling.
 */
function dustlandexpress_load_customizer_script() {
    wp_enqueue_script( 'dustlandexpress-customizer-js', get_template_directory_uri() . '/customizer/customizer-library/js/customizer-custom.js', array('jquery'), DUSTLANDEXPRESS_THEME_VERSION, true );
    wp_enqueue_style( 'dustlandexpress-customizer-css', get_template_directory_uri() . '/customizer/customizer-library/css/customizer.css' );
}
add_action( 'customize_controls_enqueue_scripts', 'dustlandexpress_load_customizer_script' );

// Create function to check if WooCommerce exists.
if ( ! function_exists( 'dustlandexpress_is_woocommerce_activated' ) ) :
    
function dustlandexpress_is_woocommerce_activated() {
    if ( class_exists( 'woocommerce' ) ) { return true; } else { return false; }
}

endif; // dustlandexpress_is_woocommerce_activated

if ( dustlandexpress_is_woocommerce_activated() ) {
    require get_template_directory() . '/includes/inc/woocommerce-inc.php';
}

/**
 * Adjust is_home query if kra-blog-cats is set
 */
function dustlandexpress_set_blog_queries( $query ) {
    $blog_query_set = '';
    if ( get_theme_mod( 'kra-blog-cats', false ) ) {
        $blog_query_set = get_theme_mod( 'kra-blog-cats' );
    }
    
    if ( $blog_query_set ) {
        // do not alter the query on wp-admin pages and only alter it if it's the main query
        if ( !is_admin() && $query->is_main_query() ){
            if ( is_home() ){
                $query->set( 'cat', $blog_query_set );
            }
        }
    }
}
add_action( 'pre_get_posts', 'dustlandexpress_set_blog_queries' );

/**
 * Remove slider category from the sidebar categories widget
 */
function dustlandexpress_exclude_slider_categories_widget( $args ) {
	$exclude = ''; // ID's of the categories to exclude
	if ( get_theme_mod( 'kra-slider-cats', false ) ) {
        $exclude = get_theme_mod( 'kra-slider-cats' );
    }
	$args['exclude'] = $exclude;
	return $args;
}
add_filter( 'widget_categories_args', 'dustlandexpress_exclude_slider_categories_widget' );

/**
 * Display recommended plugins with the TGM class
 */
function dustlandexpress_register_required_plugins() {
	$plugins = array(
		// The recommended WordPress.org plugins.
		array(
			'name'      => __( 'Elementor', 'dustland-express' ),
			'slug'      => 'elementor',
			'required'  => false,
		),
		array(
			'name'      => __( 'WooCommerce', 'dustland-express' ),
			'slug'      => 'woocommerce',
			'required'  => false,
		),
		array(
			'name'      => __( 'Breadcrumb NavXT', 'dustland-express' ),
			'slug'      => 'breadcrumb-navxt',
			'required'  => false,
		),
		array(
			'name'      => __( 'Meta Slider', 'dustland-express' ),
			'slug'      => 'ml-slider',
			'required'  => false,
		)
	);
	$config = array(
		'id'           => 'dustland-express',
		'menu'         => 'tgmpa-install-plugins',
	);

	tgmpa( $plugins, $config );
}
add_action( 'tgmpa_register', 'dustlandexpress_register_required_plugins' );
/**
 * Elementor Check
 */
if ( ! defined( 'ELEMENTOR_PARTNER_ID' ) ) {
    define( 'ELEMENTOR_PARTNER_ID', 2118 );
}

/**
 * Register a custom Post Categories ID column
 */
function dustlandexpress_edit_cat_columns( $dustlandexpress_cat_columns ) {
    $dustlandexpress_cat_in = array( 'cat_id' => 'Category ID <span class="cat_id_note">For the Default Slider</span>' );
    $dustlandexpress_cat_columns = dustlandexpress_cat_columns_array_push_after( $dustlandexpress_cat_columns, $dustlandexpress_cat_in, 0 );
    return $dustlandexpress_cat_columns;
}
add_filter( 'manage_edit-category_columns', 'dustlandexpress_edit_cat_columns' );

/**
 * Print the ID column
 */
function dustlandexpress_cat_custom_columns( $value, $name, $cat_id ) {
    if( 'cat_id' == $name ) 
        echo $cat_id;
}
add_filter( 'manage_category_custom_column', 'dustlandexpress_cat_custom_columns', 10, 3 );

/**
 * Insert an element at the beggining of the array
 */
function dustlandexpress_cat_columns_array_push_after( $src, $dustlandexpress_cat_in, $pos ) {
    if ( is_int( $pos ) ) {
        $R = array_merge( array_slice( $src, 0, $pos + 1 ), $dustlandexpress_cat_in, array_slice( $src, $pos + 1 ) );
    } else {
        foreach ( $src as $k => $v ) {
            $R[$k] = $v;
            if ( $k == $pos )
                $R = array_merge( $R, $dustlandexpress_cat_in );
        }
    }
    return $R;
}
