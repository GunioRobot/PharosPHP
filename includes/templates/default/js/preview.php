<script type="text/javascript">

	this.imagePreview = function(){	
	
		if ( typeof(attempts) != 'undefined' && typeof(attempt) != 'undefined' && attempt < attempts && $('#'+table_id+' > tbody > tr > td > a.imagePreview').length == 0 ) {
			image_timeout = setTimeout( function() { imagePreview(); attempt++; }, 3000);
		} else if ( typeof(image_timeout) != 'undefined' )  clearTimeout(image_timeout);
		
		xOffset = 0;
		yOffset = 20;
		$(".imagePreview").hover(function(e){
			var src = ( typeof(this.name) != 'undefined' && this.name != '' ) ? this.name : this.id;
			this.t = this.title;
			this.title = "";	
			$("body").append("<div id='preview'><img src='<?=current_templates('images/prev_top.png')?>' /><div id='preview2'><img src='"+ src +"' width='200' alt='Image preview' /></div><img src='<?=current_templates('images/prev_bot.png')?>' /></div>");
			$("#preview")
				.css("top",(e.pageY - xOffset) + "px")
				.css("left",(e.pageX + yOffset) + "px")
				.fadeIn("fast");						
	    },
		function(){
			this.title = this.t;	
			$("#preview").remove();
	    });	
		$(".imagePreview").mousemove(function(e){
			$("#preview")
				.css("top",(e.pageY - xOffset) + "px")
				.css("left",(e.pageX + yOffset) + "px");
		});			
	};
	
	
	
	this.questionPreview = function(){
		
		if ( typeof(attempts) != 'undefined' && typeof(attempt) != 'undefined' && attempt < attempts && $('#'+table_id+' > tbody > tr > td > a.questionPreview').length == 0 ) {
			question_timeout = setTimeout( function() { questionPreview(); attempt++; }, 3000);
		} else if ( typeof(question_timeout) != 'undefined') clearTimeout(question_timeout);
		
		x2Offset = 0;
		y2Offset = 30;
		$(".questionPreview").hover(function(e){
			var src = ( typeof(this.name) != 'undefined' && this.name != '' ) ? this.name : this.id;
			this.t = this.title;
			this.title = "";	
			var c = (this.t != "") ? "<br/>" + this.t : "";
			$("body").append("<div id='preview'><img src='<?=current_templates('images/prev_top.png')?>' /><div id='preview2'><div id='previewQuestion'>"+ src +"</div></div><img src='<?=current_templates('images/prev_bot.png')?>' /></div>");
			$("#preview")
				.css("top",(e.pageY - x2Offset) + "px")
				.css("left",(e.pageX + y2Offset) + "px")
				.fadeIn("fast");						
	    },
		function(){
			this.title = this.t;	
			$("#preview").remove();
	    });	
		$(".questionPreview").mousemove(function(e){
			$("#preview")
				.css("top",(e.pageY - x2Offset) + "px")
				.css("left",(e.pageX + y2Offset) + "px");
		});			
	};
	
	$(document).ready(function(){
		imagePreview();
		questionPreview();
	});

</script>