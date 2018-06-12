<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package Dustland Express
 */
global $woocommerce;
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<div id="page">
<header id="masthead" class="site-header border-bottom<?php echo ( get_theme_mod( 'kra-header-layout', false ) == 'kra-header-layout-centered' ) ? ' kra-header-layout-centered' : ' kra-header-layout-standard'; ?> <?php echo ( get_theme_mod( 'kra-slider-type', false ) == 'kra-no-slider' ) ? ' kra-header-no-slider' : ' kra-header-has-slider'; ?>" role="banner">
    
    <?php if ( get_theme_mod( 'kra-header-layout', false ) == 'kra-header-layout-centered' ) : ?>
    
        <?php get_template_part( '/templates/header/header-layout-centered' ); ?>
        
    <?php else : ?>
        
        <?php get_template_part( '/templates/header/header-layout-standard' ); ?>
        
    <?php endif; ?>
    
</header><!-- #masthead -->

<?php if ( is_front_page() ) : ?>
	
	<?php get_template_part( '/templates/slider/homepage-slider' ); ?>
	
<?php endif; ?>

<div id="content" class="site-content site-container<?php echo ( ! is_active_sidebar( 'sidebar-1' ) ) ? ' content-no-sidebar' : ' content-has-sidebar'; ?>">