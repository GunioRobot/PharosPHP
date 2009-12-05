
<script type="text/javascript">
	$(function() {
		
		$('#30-nav').click(function(e) {
			
			// Stop the page from moving
			e.preventDefault();
			
			// Change the button text
			$.alerts.okButton = "&nbsp;Publish Application&nbsp;";

			// Grab the link the publish button would have went to, and then display the message in the window
			var link = '<?=HTTP_SERVER.ADMIN_DIR?>repost.php?pid=<?=$_GET['pid']?>&action=publish_app&app_id=<?=$CURRENT_APP_ID?>';
			var message = "Publishing this application will make all changes since last publication active.<br /><br />";
			message += "<strong>User applications will update when they are launched and connected to the internet.</strong><br /><br />";

			jConfirm(message, "Publish this Application", function(r) {
				if ( r ) window.location.replace(link);
			});
		});
		
	});
	
</script>