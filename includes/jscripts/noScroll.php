<script type="text/javascript">
	function noScroll(iframe) {
		
		var padding = 40;
		
		var h = iframe.contentWindow.document.body.scrollHeight;
		if ( h < 250 ) h = 250;
				
		$('#'+iframe.id).css('height', (h+padding)+"px");
		
	}
</script>