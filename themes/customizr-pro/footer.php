<?php
 /**
 * The template for displaying the footer.
 *
 *
 * @package Customizr
 * @since Customizr 3.0
 */
if ( apply_filters( 'czr_ms', false ) ) {
  do_action( 'czr_ms_tmpl', 'footer' );
  return;
}

  	do_action( '__before_footer' ); ?>
  		<!-- FOOTER -->
  		<footer id="footer" class="<?php echo czr_fn__f('tc_footer_classes', '') ?>">
  		 	<?php do_action( '__footer' ); // hook of footer widget and colophon?>
  		</footer>
    </div><!-- //#tc-page-wrapper -->
		<?php
    do_action( '__after_page_wrap' );
		wp_footer(); //do not remove, used by the theme and many plugins
	  do_action( '__after_footer' ); ?>
	</body>
	<?php do_action( '__after_body' ); ?>
	<!-- Yandex.Metrika counter -->
<script type="text/javascript" >
    (function (d, w, c) {
        (w[c] = w[c] || []).push(function() {
            try {
                w.yaCounter47076141 = new Ya.Metrika({
                    id:47076141,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true,
                    webvisor:true
                });
            } catch(e) { }
        });

        var n = d.getElementsByTagName("script")[0],
            s = d.createElement("script"),
            f = function () { n.parentNode.insertBefore(s, n); };
        s.type = "text/javascript";
        s.async = true;
        s.src = "https://mc.yandex.ru/metrika/watch.js";

        if (w.opera == "[object Opera]") {
            d.addEventListener("DOMContentLoaded", f, false);
        } else { f(); }
    })(document, window, "yandex_metrika_callbacks");
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/47076141" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
</html>