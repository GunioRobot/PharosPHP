<script type="text/javascript">

	dateUsesTime = "<?=$usesTimeOnDate?>" == "1" ? true : false;
	multipleEventsPerDay = "<?=$multipleEventsPerDay?>" == "1" ? true : false;
	selectedClass = "date_has_event";
	currentDayClass = "today";
	currentDay = '';
	
	function formatTime(d) {
		
		var a_p = "";
	
		var curr_hour = d.getHours();
		if (curr_hour < 12) a_p = "AM";
		else a_p = "PM";
	
		if (curr_hour == 0) curr_hour = 12;								
		else if (curr_hour > 12) curr_hour = curr_hour - 12;
		
		if ( curr_hour < 10 ) curr_hour = "0"+curr_hour;

		var curr_min = d.getMinutes();
		curr_min = curr_min + "";
		if (curr_min.length == 1) curr_min = "0" + curr_min;
	
		return curr_hour+":"+curr_min+" "+a_p;
		
	}
	
	
	function formatDate(d) {
		return (d.getMonth()+1) + '/' + d.getDate() + '/' +d.getFullYear();
	}
	
	
	function dateUsed(date) {
		return indexOfDate(date) > -1;
	}
	
	function indexOfDate(date) {
		
		// Position of date we're removing in the hidden input field
		var pos = $('#updated-dates').val().indexOf(date);
		var dateLength = dateUsesTime ? (date+" 12:00:00").length : date.length;
		
		return pos;
		
	}
	
	
	function addTimeToDay(timeString) {
			
		// The actual dateTime object
		var month = ( timeString.substr(5,2) > 0 ) ? timeString.substr(5,2) - 1 : 0;
		var dateTime = new Date(currentDay.substr(0,4),month,currentDay.substr(8,2),timeString.substr(0,2),timeString.substr(3,2),timeString.substr(6,2));
		
		// If this date wasn't already in use
		if ( !dateUsed(currentDay+' '+timeString) ) {
			
			var dates = $('#updated-dates').val();
			
			// Add to the huge hidden input :: separated array
			if ( dates.length > 0 ) {
				$('#updated-dates').val(dates+"::"+currentDay+' '+timeString);
			} else $('#updated-dates').val(dateTime);
			
			var prettyTimeString = formatTime(dateTime);
			
			// Add to the specific day as a hidden input
			$('td[name='+currentDay+']').append('<input type="hidden" name="'+prettyTimeString+'" value="'+timeString+'" />');
			
			loadTimesForDate(currentDay);
			
			$('td[name='+currentDay+']').addClass(selectedClass);
			
		}	
	
	}

	
	function removeDateTimeFromDates(dateTime) {
		
		var hidden_dates = $('#updated-dates');
		var dates = hidden_dates.val();
		var dateLength = dateUsesTime ? dateTime.length : (dateTime+" 12:00:00").length;
		
		
		// If was in the list, remove it
		var pos = indexOfDate(dateTime);
		if ( pos > -1 ) {
		
			// Removing the last one from the list is a little different
			if ( (pos + dateLength) ==  dates.length ) {
		
				hidden_dates.val(dates.substring(0,(pos-2)));
		
			} else {
		
				var part1 = dates.substring(0,pos);
				var part2 = dates.substring(pos+dateLength+2);
		
				hidden_dates.val(part1+part2);
		
			}
			
		}
		
	}
	
	
	function loadTimesForDate(date) {
				
		// Holds all the times
		var drawerContent = $('ul#timeListing');
		
		// Clear the content 
		drawerContent.empty();
		drawerContent.hide();
		
		// All the times (hidden input fields), sorted in ASC order
		var times = $.makeArray($('td[name='+date+'] input[type=hidden]'));
		times = times.sort(function(a,b) {
			return $(a).attr('name') < $(b).attr('name');
		});

		if ( times.length > 0 ) {
			
			$.each(times, function(index, el) {
			
				var timeUI = $(this).attr('name');
				var time = $(this).val();

				// Add the time to the list
				drawerContent.prepend('<li name="'+time+'"><span class="display">'+timeUI+'</span><a href="#" class="removeTime" title="Remove">&nbsp;</a><div class="clearBoth"></div></li>');
				
			});
			
		} else drawerContent.html('<li id="no-times">No Times.</li>');
						
	}

	
	
	
	// Called after the page has finished loading to attach all events, etc
	$(function() {
		
		
		// Hidden input field containing :: separated string of dates, as Y-m-d each
		var hidden_dates = $('#updated-dates');
		
		
		// Resize the drawer, if shown
		if ( $('#calendar div#drawer').length > 0  ) {
		
			// Calculate the height of the drawer
			var numWeeks = $('table.calendar tr').length - 2;
			var height = numWeeks * 81;
			height -= 4;	// Subtract the CSS border
			$('#calendar div#drawer').css('height', (height+'px'));
			
			// Hide the toolbar to start with
			$('div#drawer div#toolbar').hide();
			
			// Assign the "+ Time button"
			$('a#addTime').click(function(e) {
			
				e.preventDefault();
				
				// Ask for a time now
				var values = new TimeList();
				jPromptSelect('Show Time:', values, 'Pick a Show Time', function(r) {
					if ( r ) {
												
						addTimeToDay(r);
						
						var drawerContent = $('ul#timeListing');
						var listItem = $('li[name='+r+'] span.display');
						listItem.css("background-color", "#FF9900");
						drawerContent.fadeIn('fast', function() {
							listItem.animate({
								backgroundColor: "#FFFFFF"
							}, 'slow');
						});
						
					}
				});
				
			});
			
			
			// Bind the remove click handler to all a.removeTime present AND future
			$('ul#timeListing li a.removeTime').live('click', function(e) {
				
				e.preventDefault();

				var listItem = $(e.target).closest("li");	// Grabs the closest parent "li" element
				var time = listItem.attr('name');
				var dateTime = currentDay+" "+time;
				
				// Remove from main list as well as the single hidden input
				removeDateTimeFromDates(dateTime);
				$('td[name='+currentDay+'] input[value='+time+']').remove();
				
				// Now remove from the UI
				listItem.children('span')
				.css('color', '#ffffff')
				.animate({
					backgroundColor: "#FF9900"
				}, "fast", '', function() {
					listItem.slideUp('fast', function() {
						
						listItem.remove();
						
						// If just removed the very last one... (:parent would not match and would have length of 0 instead of 1)
						if ( $('ul#timeListing :parent').length == 0 ) {
							
							// Unhighlight the day itself
							$('td[name='+currentDay+']').removeClass(selectedClass);
							
							// Add the placeholder text of "No Times."
							$('ul#timeListing').prepend('<li id="no-times">No Times.</li>');
							
						}
						
					});
				});
										
			});
			
		}
		
	
		// Assign a click listener to all active date cells
		$('#calendar td:not(.padding)').click(function(e) {
			
			var cell = $(this);
			var date = cell.attr('name');
			var alreadyAssigned = cell.hasClass(selectedClass);
			var dates = hidden_dates.val();
			
			// Want this behavior when only the date, or dateTime is per day
			if ( !multipleEventsPerDay ) {
			
				// Need to remove from list
				if ( alreadyAssigned ) {	
					
					var pos = indexOfDate(date);
					if ( pos > -1 ) { 	
										
						// Remove from the one big hidden input
						removeDateTimeFromDates(date);
					
						// Update UI
						if ( dateUsesTime && !multipleEventsPerDay ) {
							$('#'+cell.attr('id')+" .time").remove();
						}
					
						cell.removeClass(selectedClass);
					
					}
				
				} else {
				
					// Behavior with time stored on date
					if ( dateUsesTime ) {
				
						var values = new TimeList();
							
						// Prompt for the time value
						jPromptSelect('Show Time:', values, 'Pick a Show Time', function(r) {
						    if ( r ) {
						
								date += " "+r;
							
								// Just tack on the date to the list
								if ( dates.length > 0 ) {
									hidden_dates.val(dates+"::"+date);
								} else hidden_dates.val(date);

								// Update UI
								if ( dateUsesTime ) {
									var timeString = formatTime(new Date(2009,9,9,r.substr(0,2),r.substr(3,2),r.substr(6,2)));
									cell.append($('<div class="time">'+timeString+'</div>'));
								}
								
								cell.addClass(selectedClass);
								
							}
						});
					
					} 
								
				
					// Behavior without storing time on date
					else {
				
						// Just tack on the date to the list
						if ( dates.length > 0 ) {
							hidden_dates.val(dates+"::"+date);
						} else hidden_dates.val(date);
				
						// Update UI
						cell.addClass(selectedClass);
					
					}
				
				}			

			}


			// Drawer - based view for multiple times per day
			else {
				
				var drawer = $('#calendar div#drawer');
				var drawerTitle = $('#calendar #title');
				var drawerContent = $('#calendar div#drawer div#drawerContent ul#timeListing');
				
				// Fade in the toolbar if it was hidden
				if ( drawer.children('#toolbar:hidden').length > 0 ) {
					drawer.children('#toolbar').fadeIn('fast');
				}
				
				// Set the title to this date
				drawerTitle.hide();
				var month = ( date.substr(5,2) > 0 ) ? date.substr(5,2) - 1 : 0;
				drawerTitle.html(formatDate(new Date(date.substr(0,4),month,date.substr(8,2),0,0,0)));
				drawerTitle.fadeIn('slow');
				
				$('td[name='+currentDay+']').removeClass(currentDayClass);	// Remove class from previous one
				currentDay = date;	// Set the global var for current selection
				cell.addClass(currentDayClass);	// Add class to new one
				
				// Reload the times into the drawer
				loadTimesForDate(currentDay);
				drawerContent.fadeIn('fast');	// Hides the drawer, just want a simple fade in tho
			
			}


		});
	
	});
	
</script>