
	$(function() {
				
		// Gets all the "Delete" links in the table and reconfigures to use with jConfirm
		$('.list td.actions a:contains("Publish"), #30-nav').click(function(e) {
			
			// Stop the page from moving
			e.preventDefault();
			
			// Show message
			var message = "Publishing this application will make all changes since last publication active.<br /><br />";
			message += "<strong>User applications will update when they are launched and connected to the internet.</strong><br /><br />";
		
		
			if ( $('#dialog').length ) {
				$("#dialog").remove();
			}

			
			var div = $('<div id="dialog"><p>'+message+'</p></div>').appendTo($('body'));			
			div.dialog({
				bgiframe: true,
				modal: true,
				resizable:false,
				title : 'Publish this Application',
				buttons : {
					"Publish" : function() { 
						
						div.html('<div id="progressbar"></div>');
						var progress = $('#progressbar').progressbar({
							value : 1
						});

						div.dialog('option', 'title', 'Publishing Application');
						div.dialog('option', 'buttons', {});
						div.prevAll(".ui-dialog-titlebar").find(".ui-dialog-titlebar-close").hide();
						
						$.get(e.target.href, '', function(data) {
														
							$('#progressbar div.ui-progressbar-value').animate({width:'100%'}, 1500, "linear", function() {

								div.prevAll(".ui-dialog-titlebar").find(".ui-dialog-titlebar-close").show();
								
								div.dialog('option', 'title', data.title);
								div.html("<p>"+data.message+"</p>");
								div.dialog('option', 'buttons', {
									"Continue" : function() {
										div.dialog('close');
									}
								});
								
							});

						}, "json");

					}, 
					"Cancel" : function() {
						$(this).dialog('close');
					}
				}
			});
						
		});
		
	});
	
