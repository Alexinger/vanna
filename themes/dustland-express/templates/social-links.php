<?php
if( get_theme_mod( 'kra-social-email', false ) ) :
    echo '<a href="' . esc_url( 'mailto:' . antispambot( get_theme_mod( 'kra-social-email' ), 1 ) ) . '" title="' . __( 'Send Us an Email', 'dustland-express' ) . '" class="social-email"><i class="fa fa-envelope-o"></i></a>';
endif;

if( get_theme_mod( 'kra-social-facebook', false ) ) :
    echo '<a href="' . esc_url( get_theme_mod( 'kra-social-facebook' ) ) . '" target="_blank" title="' . __( 'Find Us on Facebook', 'dustland-express' ) . '" class="social-facebook"><i class="fa fa-facebook"></i></a>';
endif;

if( get_theme_mod( 'kra-social-twitter', false ) ) :
    echo '<a href="' . esc_url( get_theme_mod( 'kra-social-twitter' ) ) . '" target="_blank" title="' . __( 'Follow Us on Twitter', 'dustland-express' ) . '" class="social-twitter"><i class="fa fa-twitter"></i></a>';
endif;

if( get_theme_mod( 'kra-social-instagram', false ) ) :
    echo '<a href="' . esc_url( get_theme_mod( 'kra-social-instagram' ) ) . '" target="_blank" title="' . __( 'Follow Us on Instagram', 'dustland-express' ) . '" class="social-instagram"><i class="fa fa-instagram"></i></a>';
endif; ?>