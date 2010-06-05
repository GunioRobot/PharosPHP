<script type="text/javascript">

	$(function() {
		
		// Ask user to choose date range for export and give them CSV file afterwards
		$('a#export-users').click(function(e) {
	
			// Stop the page from moving
			e.preventDefault();
	
			<?
			
				// Build array of months per year
				$dates = array();
				$years = array(2009,2010,2011,2012,2013,2014,2015,2016);
				$length = count($years);

				for ( $i = 0; $i < $length; $i++ ) {
					
					$sql = "SELECT DISTINCT MONTH(date_added) as month FROM users WHERE user_level = '".(int)Settings::get( 'users.levels.basic')."' AND YEAR(date_added) = '".$years[$i]."' ORDER BY month ASC";
					for ( $info = $db->Execute($sql); !$info->EOF; $info->moveNext() ) {
						$month = $info->fields['month'] < 10 ? "0".$info->fields['month'] : $info->fields['month'];
						$dates[] = new DateTime($years[$i]."-".$month."-01");
					}
					
				}
				
				$out = '<option value=\"all-data\">All Data</option>';
				$length = count($dates);
				foreach($dates as $i => $d) {
					$out .= '<option value=\"'.$d->format('Y-m-d').'\"'.($length == ($i+1) ? ' selected=\"selected\"':'').'>'.$d->format("F Y").'</option>';
				}
			
			?>
	
			// Show message
			var message = "Please choose a month to export all users added during that month or the &quot;All Data&quot; to export all data in the system.<br /><br />";
			message += "<select name=\"date\" id=\"date\"><?=$out?></select><br /><br />";


			if ( $('#dialog').length ) {
				$("#dialog").remove();
			}
	
			var div = $('<div id="dialog"><p>'+message+'</p></div>').appendTo($('body'));			
			div.dialog({
				bgiframe: true,
				modal: true,
				resizable:false,
				title : 'Export Users',
				buttons : {
					"Download Spreadsheet" : function() { 
				
						var dateVal = $("#date").val();
				
						div.html('<div id="progressbar"</div>');
						var progress = $('#progressbar').progressbar({
							value : 1
						});

						div.dialog('option', 'title', 'Creating Spreadsheet');
						div.dialog('option', 'buttons', {});

						$.download($(e.target).attr('href'), 'date='+dateVal, 'post');
						$('#progressbar div.ui-progressbar-value').animate({width:'100%'}, 1000, "linear", function() {

							div.dialog('option', 'title', "Successfully Exported Users");
							div.html("<p>Your browser should prompt you to save the file or may have automatically started the download.</p>");
							div.dialog('option', 'buttons', {
								"Continue" : function() {
									div.dialog('close');
								}
							});
					
						});


					}, 
					"Cancel" : function() {
						$(this).dialog('close');
					}
				}
			});
				
		});

	});

</script>