
<script type="text/javascript">

	$(function() {
				
		// Gets all the "Delete" links in the table and reconfigures to use with jConfirm
		$('.list a:contains("Publish")').click(function(e) {
			
			// Stop the page from moving
			e.preventDefault();
						
			// Change the button text
			$.alerts.okButton = "&nbsp;Publish Application&nbsp;";
			
			// Grab the link the publish button would have went to, and then display the message in the window
			var link = '<?=HTTP_SERVER.ADMIN_DIR?>'+$(e.target).attr('href');
			var message = "Publishing this application will make all changes since last publication active.<br /><br />";
			message += "<strong>User applications will update when they are launched and connected to the internet.</strong><br /><br />";
			
			jConfirm(message, "Publish this Application", function(r) {
				if ( r ) window.location.replace(link);
			});
			
		});
		
	});
	
</script>