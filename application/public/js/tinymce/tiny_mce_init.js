
	$(function() {
	
		$('textarea:not(.exclude-tinymce)').tinymce({
		
			// Location of TinyMCE script
			script_url : CMSLite.PUBLIC_URL+'js/tinymce/tiny_mce.js',

			// General options
			theme : "advanced",
			plugins : "safari,pagebreak,style,advhr,inlinepopups,searchreplace,contextmenu,paste,directionality,xhtmlxtras,template",			

			
			// Theme options
			theme_advanced_buttons1 : "bold,italic,underline,|,bullist,numlist,charmap,formatselect,|,undo,redo,|,link,unlink,|,code",
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

