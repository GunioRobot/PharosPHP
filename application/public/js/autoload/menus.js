
	$(function() {
	
		// When clicking on a top level nav item, show the kids
		$("a.topNav").click(function(e) {
						
			e.preventDefault();
			
			var $this = $(this);
			var collection = $(".subNav:visible");
			var id = $(this).attr("id");
			
			$("#"+id+"Sub").stop();	// Stop any current animation on this object
			
			$('#pointer').animate({
				left: Math.floor($this.position().left + ($this.width()/2)) - 15
			}, 1000, function() {
			
				if ( collection.length > 0 ) {
					collection.fadeOut("slow", function() {
						$("#"+id+"Sub").fadeIn("slow");
					});
				} else {
					$("#"+id+"Sub").fadeIn("slow");
				}
				
			});			
			
		});
		
		
		
		// Select the correct nav as the page loads
		var current = $("a.topNav.current");
		if ( current.length == 0 ) {
			$("a.topNav:first").click();
		} else {
			current.click();
		}
		
	
	});