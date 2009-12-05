
<script type="text/javascript">

	$(function() {
				
		// Gets all the "Delete" links in the table and reconfigures to use with jConfirm
		$('.list a:contains("Delete")').click(function(e) {
			
			// Stop the page from moving
			e.preventDefault();
			
			// Get the type we are deleting
			var words = $(e.target).attr('title').split(' ');
			var type = words[words.length - 1];
			
			// Change the button text
			$.alerts.okButton = "&nbsp;Delete "+type+"&nbsp;";
			
			// Grab the link the delete button would have went to, and then display the message in the window
			var link = '<?=HTTP_SERVER.ADMIN_DIR?>'+$(e.target).attr('href');
			$.get(link, '', function(message) {
				
				var okButtonLink = message.split('[/link]');
				message = okButtonLink[1];
				
				okButtonLink = okButtonLink[0].substring(6);
												
				jConfirm(message, "Delete this "+type, function(r) {
					if ( r ) window.location.replace(okButtonLink);
				});
			});
			
		});
		
		
		
		
		
		// Gets all the "Remove" links in the table and reconfigures to use with jConfirm
		$('.list a:contains("Remove")').click(function(e) {
			
			// Stop the page from moving
			e.preventDefault();
			
			// Get the type we are deleting
			var words = $(e.target).attr('title').split(' ');
			var type = words[2];
			
			// Change the button text
			$.alerts.okButton = "&nbsp;Remove "+type+" from Application&nbsp;";
			
			// Grab the link the remove button would have went to, and then display the message in the window
			var link = '<?=HTTP_SERVER.ADMIN_DIR?>'+$(e.target).attr('href');
			$.get(link, '', function(message) {
				
				var okButtonLink = message.split('[/link]');
				message = okButtonLink[1];
				
				okButtonLink = okButtonLink[0].substring(6);
												
				jConfirm(message, "Remove "+type+" from Application", function(r) {
					if ( r ) window.location.replace(okButtonLink);
				});
			});
			
		});
		
		
		// Gets all the "Delete" links in the table and reconfigures to use with jConfirm
		$('.list a:contains("Archive")').click(function(e) {

			// Stop the page from moving
			e.preventDefault();

			// Change the button text
			$.alerts.okButton = "&nbsp;Archive this Company&nbsp;";

			// Grab the link the publish button would have went to, and then display the message in the window
			var link = e.target.href;
			var message = "Would you like to archive this company?<br /><br />";
			message += "<strong>You can access the archived company under the &quot;Archived Companies&quot; area.<br />This action can be undone.</strong><br /><br />";

			jConfirm(message, "Archive this Company", function(r) {
				if ( r ) window.location.replace(link);
			});
			
		});
	
	
		// Gets all the "Delete" links in the table and reconfigures to use with jConfirm
		$('.list a:contains("Unarchive")').click(function(e) {

			// Stop the page from moving
			e.preventDefault();

			// Change the button text
			$.alerts.okButton = "&nbsp;Unarchive this Company&nbsp;";

			// Grab the link the publish button would have went to, and then display the message in the window
			var link = e.target.href;
			var message = "Would you like to unarchive this company?<br /><br />";
			message += "<strong>You can access the active company under the &quot;Active Companies&quot; area.<br />This action can be undone.</strong><br /><br />";

			jConfirm(message, "Unarchive this Company", function(r) {
				if ( r ) window.location.replace(link);
			});

		});
		
	});
	
</script>