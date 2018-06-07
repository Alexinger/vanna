/*!
 * Featured Pages Unlimited - Front javascript
 *
 * Copyright 2014 Nicolas Guillaume, GPLv2+ Licensed
 */
//Falls back to default params
var FPUFront = FPUFront || {
    Spanvalue : 4,
    ThemeName : '',
    imageCentered : 1,
    smartLoad : 0,
    DisableReorderingFour : 0
};
jQuery(function ($) {
    //prevents js conflicts
    "use strict";
    //variables declaration
    var $FPContainer     = $('.fpc-container'),
        SpanValue        = FPUFront.Spanvalue || 4,
        CurrentSpan      = 'fpc-col-md-' + SpanValue,
        $FPBlocks        = $( '.' + CurrentSpan , $FPContainer),
        $_window         = $(window);

    //adds theme name class to the body tag
    $('body').addClass(FPUFront.ThemeName);

    //adds hover class on hover
    $(".fpc-widget-front").hover(function () {
        $(this).addClass("hover");
    }, function () {
        $(this).removeClass("hover");
    });

    //CENTER
    if ( 'function' == typeof(jQuery.fn.centerImages) ) {
      $('.fpc-widget-front .fp-thumb-wrapper').centerImages( {
          enableCentering : 1 == FPUFront.imageCentered,
          enableGoldenRatio : false,
          disableGRUnder : 0,//<= don't disable golden ratio when responsive
          zeroTopAdjust : 0,
          oncustom : ['smartload', 'simple_load', 'block_resized', 'fpu-recenter']
      });
    }

    //helper to trigger a simple load
    //=> allow centering when smart load not triggered by smartload
    var _fpu_trigger_simple_load = function( $_imgs ) {
      if ( 0 === $_imgs.length )
        return;
      $_imgs.map( function( _ind, _img ) {
        $(_img).load( function () {
          $(_img).trigger('simple_load');
        });//end load
        if ( $(_img)[0] && $(_img)[0].complete )
          $(_img).load();
      } );//end map
    };//end of fn

    if ( ! FPUFront.smartLoad )
      _fpu_trigger_simple_load( $('.fpc-widget-front').find("img:not(.tc-holder-img)") );

    //simple-load event on holders needs to be needs to be triggered with a certain delay otherwise holders will be misplaced (centering)
    if ( 1 == FPUFront.imageCentered )
      setTimeout( function(){
        _fpu_trigger_simple_load( $('.fpc-widget-front').find("img.tc-holder-img") );
        }, 100
      );


    function isResponsive() {
          if ( window.matchMedia )
            return ( window.matchMedia("(max-width: 767px)").matches );

          //old browsers compatibility
          $_window = czrapp.$_window || $(window);
          return $(window).width() <= ( 767 - 15 );
    };

    //Resizes FP Container dynamically if too small
    function changeFPClass() {
      var is_resp       = isResponsive(),
          block_resized = false;

      switch ( SpanValue) {
        case '6' :
          if ( $FPContainer.width() <= 480 && ! $FPBlocks.hasClass('fpc-col-md-12') ) {
            $FPBlocks.removeClass(CurrentSpan).addClass('fpc-col-12');
            block_resized = true;
          } else if ( $FPContainer.width() > 480 && $FPBlocks.hasClass('fpc-col-md-12') ) {
            $FPBlocks.removeClass('fpc-col-md-12').addClass(CurrentSpan);
            block_resized = true;
          }
        break;

        case '3' :

          if ( 1 == FPUFront.DisableReorderingFour )
            return;

          if ( $FPContainer.width() <= 950 && ! $FPBlocks.hasClass('fpc-col-md-12') ) {
            $FPBlocks.removeClass(CurrentSpan).addClass('fpc-col-md-12');
            block_resized = true;
          } else if ( $FPContainer.width() > 950 && $FPBlocks.hasClass('fpc-col-md-12') ) {
            $FPBlocks.removeClass('fpc-col-md-12').addClass(CurrentSpan);
            block_resized = true;
          }
        break;

        /*case '4' :
        console.log($FPContainer.width());
          if ( $FPContainer.width() <= 800 ) {
            $FPBlocks.removeClass(CurrentSpan).addClass('fpc-col-md-12');
          } else if ( $FPContainer.width() > 800) {
            $FPBlocks.removeClass('fpc-col-md-12').addClass(CurrentSpan);
          }
        break;*/

        default :
          if ( $FPContainer.width() <= 767 && ! $FPBlocks.hasClass('fpc-col-md-12')) {
            $FPBlocks.removeClass(CurrentSpan).addClass('fpc-col-md-12');
            block_resized = true;
          } else if ( $FPContainer.width() > 767 && $FPBlocks.hasClass('fpc-col-md-12') ) {
            $FPBlocks.removeClass('fpc-col-md-12').addClass(CurrentSpan);
            block_resized = true;
          }
        break;
      }
      if ( block_resized )
        $FPBlocks.find('img').trigger('block_resized');
    } //end of fn

    changeFPClass();

    $(window).resize(function () {
        setTimeout(changeFPClass, 200);
    });


    // detect if the browser is IE and call our function for IE versions less than 11
    if ( $.browser.msie && ( '8.0' === $.browser.version || '9.0' === $.browser.version || '10.0' === $.browser.version ) ) {
      $('body').addClass('ie');
      //thumbsWithLinks();
    }
});
