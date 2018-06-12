/**
 * Dustland Express Customizer Custom Functionality
 *
 */
( function( $ ) {
    
    $( window ).load( function() {
        
        var the_select_value = $( '#customize-control-kra-slider-type select' ).val();
        dustlandexpress_customizer_slider_check( the_select_value );
        
        $( '#customize-control-kra-slider-type select' ).on( 'change', function() {
            var select_value = $( this ).val();
            dustlandexpress_customizer_slider_check( select_value );
        } );
        
        function dustlandexpress_customizer_slider_check( select_value ) {
            if ( select_value == 'kra-slider-default' ) {
                $( '#customize-control-kra-meta-slider-shortcode' ).hide();
                $( '#customize-control-kra-slider-cats' ).show();
                $( '#customize-control-kra-upsell-two-up' ).show();
            } else if ( select_value == 'kra-meta-slider' ) {
                $( '#customize-control-kra-slider-cats' ).hide();
                $( '#customize-control-kra-upsell-two-up' ).hide();
                $( '#customize-control-kra-meta-slider-shortcode' ).show();
            } else {
                $( '#customize-control-kra-slider-cats' ).hide();
                $( '#customize-control-kra-upsell-two-up' ).hide();
                $( '#customize-control-kra-meta-slider-shortcode' ).hide();
            }
        }
        
    } );
    
} )( jQuery );