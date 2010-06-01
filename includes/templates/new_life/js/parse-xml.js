
	function beginParsing(file) {

		var dialog = $("#dialog");
		var html = '<table id="file-status" style="margin-top:5px;" cellpadding="5" cellspacing="0" align="left"><tr><td valid="middle"><img src="'+CMSLite.TEMPLATE_SERVER+'images/icons/icon_excel.png" border="0" alt="XLS" /></td><td valign="middle">'+file.name+'</td></tr></table>';

		dialog.dialog('option', 'buttons', {

			"Ok" : function() {
				$("input#upload_completed").val("true");
				dialog.dialog("destroy");
				
				$(html).insertAfter("#SWFUpload_0").hide();
				$("#SWFUpload_0").fadeOut('fast', function() {
					
					$(this).remove();
					$("#file-status").fadeIn("fast");
					$("a#parse-xml").removeClass("disabled").click(parse_window);
					
				});
			}

		});

	}
	
	
	
	function parse_window(e) {

		e.preventDefault();
	
		// Show message
		var message = "You will be able to review all changes and make corrections after this procedure.<br /><br />";
		message += "<strong>This operation will take several minutes. Please wait patiently.</strong>";

		var div = $('<div id="dialog"><p>'+message+'</p></div>').appendTo($('body'));			
		div.dialog({
			bgiframe: true,
			modal: true,
			resizable:false,
			title : 'Import Excel Database',
			buttons : {
				"Import" : function() { 
									
					div.html('<br /><div id="progressbar"</div><br /><p id="import-content-area"><strong>Importing tons of data...</strong></p>');
					var progress = $('#progressbar').progressbar({
						value : 1
					});

					div.dialog('option', 'title', 'Importing Excel Database...');
					div.dialog('option', 'buttons', {});
					div.prevAll(".ui-dialog-titlebar").find(".ui-dialog-titlebar-close").hide();
				
					var timer = setInterval(function() {
						var p = progress.progressbar('option','value') + 1;
						if ( p <= 96 ) {
							
							progress.progressbar('option','value',p);
							progress.progressbar('widget').find("div.ui-progressbar-value").animate({width:(++p)+'%'}, 1000);
							
						} else {
							
							clearInterval(timer);
							timer = null;
							$("p#import-content-area").fadeOut("fast", function() {
								 progress.progressbar('widget').find("div.ui-progressbar-value").animate({width:'99%'}, 500, "linear", function() {
									progress.progressbar('option', 'value', '100');
								}).addClass("ui-progressbar-value-animated");
								
								$(this).text("Still crunching and verifying, please wait...").fadeIn("slow");
								
								timer = setTimeout(function() {
									$("p#import-content-area").fadeOut("fast", function() {
										$(this).text("This is a good time to take a break.").fadeIn("slow");
										clearTimeout(timer);
										
										timer = setTimeout(function() {
											
											clearTimeout(timer);
											timer = null;
											
											div.fadeOut("slow", function() {
												$(this).html("<p>Oops! Looks like we had issues parsing the XML.<br /><br />Please try again to resume and finish parsing the rest of the file.</p>").fadeIn("slow");
												div.dialog('option', 'buttons', {
													"Try Again" : function() {
														$(this).dialog('destroy');
														// window.location.href = CMSLite.HTTP_SERVER+"xml-parser/";
													}
												});
											});
											
										}, 1000 * 60 * 10);	// 10 minutes out
										
									});
										
								}, 5000);
								
							});
							
						}
					}, 1500);
				
					// Start the upload progress
					$.get($(e.target).attr("href"), {}, function(data) {
						
						if ( timer ) clearInterval(timer);
						
						progress.progressbar('widget').find("div.ui-progressbar-value").animate({width:'99%'}, 1000, "linear", function() {
							
							progress.progressbar('option', 'value', '100');
							div.prevAll(".ui-dialog-titlebar").find(".ui-dialog-titlebar-close").show()
							
							div.dialog('option', 'title', data.title);
							div.css("height", "50px").html(data.message);
							div.dialog('option', 'buttons', {
								"Continue" : function() {
									div.dialog('destroy');
									window.location.href = CMSLite.HTTP_SERVER+"xml-parser/parse-results/";
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
	}

	