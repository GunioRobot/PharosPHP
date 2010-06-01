
	this.imagePreview = function(){	
	
		if ( typeof(attempts) != 'undefined' && typeof(attempt) != 'undefined' && attempt < attempts && $('#'+table_id+' > tbody > tr > td > a.imagePreview').length == 0 ) {
			image_timeout = setTimeout( function() { imagePreview(); attempt++; }, 3000);
		} else if ( typeof(image_timeout) != 'undefined' )  clearTimeout(image_timeout);
		
		xOffset = 0;
		yOffset = 20;
		$(".imagePreview").hover(
			function(e){
				var src = ( typeof(this.name) != 'undefined' && this.name != '' ) ? this.name : this.id;
				this.t = this.title;
				this.title = "";	
				$("body").append("<div id='preview'><img src='"+CMSLite.TEMPLATE_SERVER+"images/prev_top.png' /><div id='preview2'><img src='"+ src +"' width='200' alt='Image preview' /></div><img src='"+CMSLite.TEMPLATE_SERVER+"images/prev_bot.png' /></div>");
				$("#preview")
					.css("top",(e.pageY - xOffset) + "px")
					.css("left",(e.pageX + yOffset) + "px")
					.fadeIn("fast");						
	    	}, function(){
				this.title = this.t;	
				$("#preview").remove();
	    });	
		
		$(".imagePreview").mousemove(function(e){
			$("#preview")
				.css("top",(e.pageY - xOffset) + "px")
				.css("left",(e.pageX + yOffset) + "px");
		});	
				
	};

	
	$(function(){
		imagePreview();
	});
