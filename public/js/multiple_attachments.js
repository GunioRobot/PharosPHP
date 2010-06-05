
(function($) {

	$.fn.multipleAttachments = function(options) {
	   
	 	var settings = $.extend({
	        maximum: 10,
			typeSingular: "document",
			typePlural: "documents"
	    }, options);
	
		return this.each(function() {

			var showing = 0;
			var $holder = $(this);
			
			$holder.find("div.file-wrapper:has(a[title=Preview File])").each(function() {
				$(this).show();
				showing++;
			});

			if ( showing == 0 ) {
				$holder.find("div.file-wrapper:hidden:first").show();
				showing++;
			} 

			if ( showing > 0 && showing < settings.maximum ) {
				$holder.find("div.file-wrapper:visible:last").after("<p><strong><a href=\"#\" class=\"next-file-wrapper-button\" title=\"Add another "+settings.typeSingular+"\">Add another "+settings.typeSingular+"</a>");
			} else {
				$holder.find("div.file-wrapper:last").after("<p><strong>No more than "+settings.maximum+" "+settings.typePlural+" allowed.</strong></p>");
			}

			$holder.find("a.next-file-wrapper-button").click(function(e) {

				e.preventDefault();
				$aButton = $(this);

				if ( showing <= settings.maximum ) {
					$holder.find("div.file-wrapper:hidden:first").after($aButton).slideDown("slow", function() {
													
						if ( ++showing == settings.maximum ) {
							$aButton.flashField({times:2,color2:"#E6E6E6"}, function() {
								$aButton.slideUp("slow", function() {
									$holder.find("div.file-wrapper:last").after("<p><strong>No more than "+settings.maximum+" "+settings.typePlural+" allowed.</strong></p>");
								});
							});
						} else {
							$aButton.flashField({times:2,color2:"#E6E6E6"});
						}
					});
				} 

			});

		});
		
	}
	
})(jQuery);

	