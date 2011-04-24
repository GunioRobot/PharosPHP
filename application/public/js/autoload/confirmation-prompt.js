
	$(function() {
				
		// Gets all the links in the table and reconfigures to use with jConfirm
		$('a.confirm-with-popup').click(function(e) {
			
			// Stop the page from moving
			e.preventDefault();
			
			// Get the type we are deleting
			var words = $(e.target).attr('title').split(' ');
			var type = words[words.length - 1];
			var action = $(this).text();
			
			// Change the button text
			$.alerts.okButton = "&nbsp; "+action+" "+type+"&nbsp;";
			
			// Grab the link the delete button would have went to, and then display the message in the window
			var link =$(e.target).attr('href');
			$.get(link, '', function(message) {
				
				var okButtonLink = message.split('[/link]');
				message = okButtonLink[1];
				
				okButtonLink = okButtonLink[0].substring(6);
												
				jConfirm(message, action+" this "+type, function(r) {
					if ( r ) {
						$.post(okButtonLink, {confirmed:"true"},function(data) {
							
							if ( data.error ) {
								
								if ( typeof console == "object" ) {
									console.log("Error "+action+" "+type+"!");
								}
								
								var _retMessage = typeof data.message != "undefined" ? data.message : "There was an error performing: " + action + " " + type + ".";
								var _retDebug = typeof data.debug != "undefined" ? data.debug : null;
								
								$.alerts.okButton = "&nbsp;Continue&nbsp;";
								jAlert(_retMessage);
							
								if ( _retDebug && typeof console == "object" ) {
									console.log(_retDebug);
								}
								
							} else {
								window.location.replace(data.redirect);
							}
							
						}, "json");
					}
				});
			});
			
		});	
		
	});
	
	