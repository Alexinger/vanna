
jQuery(function($){
	$('.add-new-shortcode').click(function(){
		$('.block-form').slideToggle();
	});
	$(".tve_select").change(function() {
		if ($(this).val()){
			wp.media.editor.insert('[wpmfc_short code="'+$(this).val()+'"]');
			$(this).val(0);
		}
	});
	$('.tve-import').click(function(){
		$('.block-import').slideToggle();
	});
});