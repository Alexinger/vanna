<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package Dustland Express
 */
?>
</div><!-- #content -->

<footer id="colophon" class="site-footer" role="contentinfo">
	
	<div class="site-footer-widgets">
        <div class="site-container">
        	<?php if ( is_active_sidebar( 'dustlandexpress-site-footer' ) ) : ?>
            <ul>
                <?php dynamic_sidebar( 'dustlandexpress-site-footer' ); ?>
            </ul>
            <?php else : ?>
	        	<div class="site-footer-no-widgets">
	        		<?php _e( 'Add your own widgets here', 'dustland-express' ); ?>
	        	</div>
	    	<?php endif; ?>
            <div class="clearboth"></div>
        </div>
    </div>
	
		<?php printf( __( '<div class="site-footer-bottom-bar"><div class="site-container"><div class="site-footer-bottom-bar-left">Theme: %1$s by %2$s', 'dustland-express' ), 'Dustland Express', '<a href="https://www.kairaweb.com/">Kaira</a></div><div class="site-footer-bottom-bar-right">' ); ?>
			
	            <?php wp_nav_menu( array( 'theme_location' => 'footer-bar','container' => false, 'fallback_cb' => false, 'depth'  => 1 ) ); ?>
                
	        </div>
	        
	    </div>
		
        <div class="clearboth"></div>
	</div>
	
</footer> <!-- .site-footer -->
</div> <!-- #page -->
<?php wp_footer(); ?>
</body>
</html>