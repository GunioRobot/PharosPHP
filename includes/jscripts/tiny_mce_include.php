<?php /*
<script type="text/javascript">

	<?php if ( $_GET['pid'] == '28' ): ?>
		
		tinyMCE.init({
			mode : "textareas",
			theme : "simple",
			oninit: function() {
				tinyMCE.get('state-enter-area').setContent($('input#state1').val());
			}
		});
		
	<?php else: ?>
				
		tinyMCE.init({
			mode : "textareas",
			theme : "simple"
		});
		
	<?php endif ?>
	
</script>


<script type="text/javascript">

	tinyMCE.init({
		mode : "textareas",
		theme : "simple",
		plugins : "advlink",
		extended_valid_elements:"a[name|href|target|title|onclick|rel]",
		oninit: function() {
			$('.mceIframeContainer iframe').css('height', '200px');
		}
	});
	
*/
?>

<script type="text/javascript">

	$(function() {
	
		$('textarea').tinymce({
		
			// Location of TinyMCE script
			script_url : '<?=INCLUDES_SERVER?>jscripts/tinymce/tiny_mce.js',

			// General options
			theme : "advanced",
			plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,inlinepopups,searchreplace,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

			// Theme options
			theme_advanced_buttons1 : "bold,italic,underline,|,undo,redo,|,link,unlink",
			theme_advanced_buttons2 : "",
			theme_advanced_buttons3 : "",
			theme_advanced_buttons4 : "",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "center",
			theme_advanced_statusbar_location : "bottom",
			theme_advanced_resizing : true,
			theme_advanced_resize_horizontal : false,
			// Example content CSS (should be your site CSS)
			//content_css : "../css/content.css",

			// Drop lists for link/image/media/template dialogs
			template_external_list_url : "lists/template_list.js",
			external_link_list_url : "lists/link_list.js",
			external_image_list_url : "lists/image_list.js",
			media_external_list_url : "lists/media_list.js"

		});
	
	});

</script>
