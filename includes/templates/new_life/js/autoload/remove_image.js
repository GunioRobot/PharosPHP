	
	$(function() {
		
		$('a.removeImage').each(function() {
			$this = $(this);
			if ( $this.prev("a").length == 0 ) {
				$this.hide();
			}
		})
		
		$('a.removeImage').click(function(e) {
			e.preventDefault();
			
			var id = $(this).attr("id");
			if ( $('#'+id+'_remove_image').length == 0 ) {
					$('<input type="hidden" id="'+id+'_remove_image" value="true" name="'+id+'_remove_image"/>').appendTo($('#profile'));
					$(this).hide();
			} else $('#'+id+'_remove_image').val('false');
			
		});
	});