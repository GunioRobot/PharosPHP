<script type="text/javascript" src="<?=TEMPLATE_SERVER?>js/tinymce/jquery.tinymce.js"></script>
<script type="text/javascript">

	$(function() {
	
		$('textarea').tinymce({
		
			// Location of TinyMCE script
			script_url : '<?=TEMPLATE_SERVER?>js/tinymce/tiny_mce.js',

			// General options
			theme : "advanced",
			plugins : "safari,pagebreak,style,advhr,inlinepopups,searchreplace,contextmenu,paste,directionality,xhtmlxtras,template",			

			convert_fonts_to_spans: false,
			inline_styles : false,

			valid_elements : ""
			+"a[href|target],"
			+"b,"
			+"br,"
			+"font[color|face|size],"
			+"img[src|id|width|height|align|hspace|vspace],"
			+"i,"
			+"li,"
			+"p[align|class],"
			+"h1,"
			+"h2,"
			+"h3,"
			+"h4,"
			+"h5,"
			+"h6,"
			+"span[class],"
			+"textformat[blockindent|indent|leading|leftmargin|rightmargin|tabstops],"
			+"u",
		
			
			// Theme options
			theme_advanced_buttons1 : "bold,italic,underline,|,bullist,numlist,charmap,formatselect,|,undo,redo,|,link,unlink,|,forecolor",
			theme_advanced_buttons2 : "",
			theme_advanced_buttons3 : "",
			theme_advanced_buttons4 : "",
			theme_advanced_blockformats : "p,div,h1,h2,h3",
	
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
