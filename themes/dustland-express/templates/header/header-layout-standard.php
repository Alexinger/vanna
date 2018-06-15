<?php global $woocommerce; ?>

<?php if ( get_theme_mod( 'kra-show-header-top-bar' ) == true ) : ?>
    
    <div class="site-top-bar border-bottom">
        <div class="site-container">
            
            <div class="site-top-bar-left">
                
                <?php wp_nav_menu( array( 'theme_location' => 'header-bar', 'fallback_cb' => false, 'container_class' => 'topbar-navigation' ) ); ?>
                
                <?php get_template_part( '/templates/social-links' ); ?>
                
            </div>
            <div class="site-top-bar-right">
                
                <?php
                if ( dustlandexpress_is_woocommerce_activated() ) { ?>
                    <div class="site-top-bar-right-text"><?php echo wp_kses_post( get_theme_mod( 'kra-header-info-text', 'Call Us: 082 444 BOOM' ) ) ?></div>
                <?php
                } ?>
                
                <?php
                if ( get_theme_mod( 'kra-header-search' ) ) :
                    echo '<i class="fa fa-search search-btn"></i>';
                endif; ?>
                
            </div>
            <div class="clearboth"></div>
            
            <?php if ( get_theme_mod( 'kra-header-search' ) ) : ?>
                <div class="search-block">
                    <?php get_search_form(); ?>
                </div>
            <?php endif; ?>
            
        </div>
        
    </div>

<?php endif; ?>

<div class="site-container">
    
    <div class="site-header-left <?php echo ( get_header_image() ) ? 'site-header-branding-img' : ''; ?>">
        
        <?php if ( get_header_image() ) : ?>
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-logo-img" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>"><img src="<?php esc_url( header_image() ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ) ?>" /></a>
        <?php else : ?>
            <h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
            <h2 class="site-description" style="font-size: 16px;color: #5c5c5c;"><?php bloginfo( 'description' ); ?></h2>
        <?php endif; ?>
        
    </div><!-- .site-branding -->
    
    <div class="site-header-right">
        
        <?php
        if ( dustlandexpress_is_woocommerce_activated() ) { ?>
        
            <div class="header-cart">
                <a class="header-cart-contents" href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php esc_attr_e('View your shopping cart', 'dustland-express'); ?>">
                    <span class="header-cart-amount">
                        <?php echo wp_kses_data( WC()->cart->get_cart_subtotal() ); ?> <?php echo sprintf( _n( '(%d)', '(%d)', WC()->cart->get_cart_contents_count(), 'dustland-express' ), WC()->cart->get_cart_contents_count() ); ?>
                    </span>
                    <span class="header-cart-checkout <?php echo ( WC()->cart->get_cart_contents_count() > 0 ) ? sanitize_html_class( 'cart-has-items' ) : ''; ?>">
                        <i class="fa fa-shopping-cart"></i>
                    </span>
                </a>
            </div>
            
        <?php
        } else { ?>
            
            <div class="site-top-bar-left-text"><?php echo wp_kses_post( get_theme_mod( 'kra-header-info-text', 'Call Us: 082 444 BOOM' ) ) ?></div>
            
        <?php
        } ?>
        
    </div>
    <div class="clearboth"></div>
    
</div>

<nav id="site-navigation" class="main-navigation" role="navigation">
    <span class="header-menu-button"><i class="fa fa-bars"></i><span><?php _e( 'Menu', 'dustland-express' ); ?></span></span>
    <div id="main-menu" class="main-menu-container">
        <span class="main-menu-close"><i class="fa fa-angle-right"></i><i class="fa fa-angle-left"></i></span>
        <div class="site-container">
            <?php wp_nav_menu( array( 'theme_location' => 'primary' ) ); ?>
            <div class="clearboth"></div>
        </div>
    </div>
</nav><!-- #site-navigation -->