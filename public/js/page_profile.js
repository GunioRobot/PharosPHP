
	$(function() {
		
		
		previousTitle = $('input#title').val();
		
		$('input#title').blur(function(e) {
		
			var title = $(this).val();
			if ( title != previousTitle ) {
				
				previousTitle = title;
				
				title = title.toLowerCase().replace(/[^[a-z0-9\s-_]]*/gi, '');	// Only allow basic slug characters (alphanumeric with -, _ and space)
				title = title.replace(/^\s*|\s*$/g, '');				// Trim the string
				title = title.replace(/\s+/g, '-');						// Convert spaces to -
				
			}
			
			$('input#slug').val(title);
			
			var url = window.location.protocol+'//'+window.location.hostname+'/'+title+'/';
			$('a#preview-slug').attr('href', url).text(url).flashField({times:2,color2:'#E6E6E6',speed:'slow'});
			
			$('span#save-text').text(" (Once saved)");
			
		});
	
	});