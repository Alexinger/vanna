var czrapp = czrapp || {};
/************************************************
* MASONRY GRID SUB CLASS
*************************************************/
/*
* In this script we fire the grid masonry on the grid only when all the images
* therein are fully loaded in case we're not using the images on scroll loading
* Imho would be better use a reliable plugin like imagesLoaded (from the same masonry's author)
* which addresses various cases, failing etc, as it is not very big. Or at least dive into it
* to see if it really suits our needs.
*
* We can use different approaches while the images are loaded:
* 1) loading animation
* 2) display the grid in a standard way (organized in rows) and modify che html once the masonry is fired.
* 3) use namespaced events
* This way we "ensure" a compatibility with browsers not running js
*
* Or we can also fire the masonry at the start and re-fire it once the images are loaded
*/
(function( $, czrapp ) {
    var _methods =  {

        initOnCzrReady : function() {

            if ( typeof undefined === typeof $.fn.masonry )
                  return;

            var $grid_container = $('.masonry__wrapper'),
                masonryReady = $.Deferred();

            if ( 1 > $grid_container.length ) {
                  czrapp.errorLog('Masonry container does not exist in the DOM.');
                  return;
            }

            $grid_container.bind( 'masonry-init.customizr', function() {
                  masonryReady.resolve();
            });

            //Init Masonry on imagesLoaded
            $grid_container.imagesLoaded( function() {
                  // init Masonry after all images have loaded
                  $grid_container.masonry({
                        itemSelector: '.grid-item',
                  })
                  //Refresh layout on image loading
                  .on( 'smartload simple_load', 'img', function() {
                        $grid_container.masonry('layout');
                  })
                  .trigger( 'masonry-init.customizr' );
            });

            //Reacts to the infinite post appended
            czrapp.$_body.on( 'post-load', function( evt, data ) {
                  var _do = function( evt, data ) {
                      if( data && data.type && 'success' == data.type && data.collection && data.html ) {
                            //initial state
                            var _saved_options         = $.extend( {}, $grid_container.data('masonry').options ),
                                _options_no_transition = $.extend( {}, _saved_options, { 'transitionDuration': 0 } );
                            /* Whole set mode */
                            $grid_container
                                        .masonry( _options_no_transition )
                                        .masonry( 'appended', $(data.html, $grid_container ) )
                                        .masonry( 'reloadItems' )
                                        // re-layout masonry after all images have loaded
                                        .imagesLoaded( function() {
                                              $grid_container
                                                    .masonry( 'layout' )
                                                    .masonry( _saved_options );

                                              //fire masonry done to allow delayed animation
                                              $grid_container.trigger( 'masonry.customizr', data );

                                              setTimeout( function(){
                                                    //trigger scroll
                                                    $(window).trigger('scroll.infinity');
                                              }, 150);
                                        });
                      }
                };
                if ( 'resolved' == masonryReady.state() ) {
                      _do( evt, data );
                } else {
                      masonryReady.then( function() {
                            _do( evt, data );
                      });
                }
            });

        }
    };//_methods{}


    czrapp.methods.MasonryGrid = {};
    $.extend( czrapp.methods.MasonryGrid , _methods );

    //Instantiate and fire on czrapp ready
    czrapp.Base.extend( czrapp.methods.MasonryGrid );
    czrapp.ready.done( function() {
      czrapp.methods.MasonryGrid.initOnCzrReady();
    });

})( jQuery, czrapp );