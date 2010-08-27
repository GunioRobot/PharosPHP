<?php

	// What we're viewing, based upon given values through $_GET or from the database itself
	$month = get("month", $form_array['month']);
	$year = get("year", $form_array['year']);
	$usesTimeOnDate = ((isset($form_array['uses_time']) && $form_array['uses_time'] == true) || $form_array['uses_time'] == 'true') ? true : false;
	$multipleEventsPerDay = ((isset($form_array['multiple_events_per_day']) && $form_array['usemultiple_events_per_days_time'] == true) || $form_array['multiple_events_per_day'] == 'true') ? true : false;
	
	$type = ($form_array['edit-mode'] != 'false') ? 'edit' : 'view';
	require_once INCLUDES_DIR.'jscripts/calendar-'.$type.'.php';
	
	$dates = explode("::", $value);	
	$dateTimes = $dates;
	foreach($dates as &$d) {
		$d = substr($d,0,10);	// Truncate time information for our display purposes
	}
	
	$hidden_dates = '<input type="hidden" name="original-dates" id="original-dates" value="'.$value.'"/>';
	$hidden_dates .= '<input type="hidden" name="updated-dates" id="updated-dates" value="'.$value.'"/>';
	$hidden_dates .= '<input type="hidden" name="month" value="'.$month.'"/>';
	$hidden_dates .= '<input type="hidden" name="year" value="'.$year.'"/>';
	
	
	/* draw table */
	$calendar = '<div id="calendar" '.($multipleEventsPerDay ? 'style="margin-left:0px;width:100%;"' : '').'><div id="innerCalendar">'.$hidden_dates;
	
	$get_string = "&pid=".get("pid", WELCOME_PID);
	if ( ($key = get("key")) && ($value = get($key)) ) {
		$get_string .= "&key=$key&$key=$value";
	}
	
	$nextMonth = "&year=".date('Y&\m\o\n\t\h=m', mktime(0,0,0,$month+1,1,$year));
	$previousMonth = "&year=".date('Y&\m\o\n\t\h=m', mktime(0,0,0,$month-1,1,$year));
	
	$calendar .= '<div id="next-month"><a href="index.php?'.$get_string.$nextMonth.'" title="See the next month"><img src="'.PUBLIC_URL.'images/go-right-circle.png" /></a></div>';
	$calendar .= '<h1>'.date('F Y', mktime(0,0,0,$month,1,$year)).'</h1>';
	$calendar .= '<div id="previous-month"><a href="index.php?'.$get_string.$previousMonth.'" title="See the previous month"><img src="'.PUBLIC_URL.'images/go-left-circle.png"/></a></div>';
	$calendar .= '<div class="clearBoth"></div>';
	
	
	$calendar .= '<table cellpadding="0" cellspacing="0" class="calendar">';

	/* table headings */
	$headings = array('Sun','Mon','Tue','Wed','Thu','Fri','Sat');
	$calendar.= '<thead><tr><th>'.implode('</th><th>',$headings).'</th></tr></thead>';

	/* days and weeks vars now ... */
	$running_day = date('w',mktime(0,0,0,$month,1,$year));
	$days_in_month = date('t',mktime(0,0,0,$month,1,$year));
	$days_in_this_week = 1;
	$day_counter = 0;
	$dates_array = array();

	/* row for week one */
	$calendar.= '<tbody><tr class="calendar-row">';

	/* print "blank" days until the first of the current week */
	for($x = 0; $x < $running_day; $x++):
		$days_in_this_week++;
	endfor;
	
	if ( $x > 0 ) $calendar .= '<td class="padding" colspan="'.$x.'"></td>';

	/* keep going with days.... */
	for($list_day = 1; $list_day <= $days_in_month; $list_day++):
		
		$timestamp = mktime(0,0,0,$month,$list_day,$year);
		$date = date('Y-m-d', $timestamp);
		
		$calendar.= '<td valign="top" id="'.$timestamp.'" name="'.$date.'" class="calendar-day';
		
		// Mark UI as already using this day
		$hasContent = ($keyIndex = array_search($date, $dates)) !== false;
		if ( $hasContent ) {
		
			$calendar .= ' date_has_event">';
			if ( $usesTimeOnDate && !$multipleEventsPerDay ) {
				$time = new DateTime($dateTimes[$keyIndex]);
				$time = $time->format('h:i A');
				unset($keyIndex);
			} else if ( $multipleEventsPerDay ) {
				

				$times = array_keys($dates, $date);
				foreach($times as $keyIndex) {
					
					$time = new DateTime($dateTimes[$keyIndex]);
					$timeUI = $time->format('h:i A');
					$time = $time->format('H:i:00');
					
					$calendar .= '<input type="hidden" value="'.$time.'" name="'.$timeUI.'" />';
					
					unset($time);
					unset($timeUI);
					
				}
				
			}
			
		} else $calendar .= '">';
		
		/*
		// If today, mark UI as such
		if ( $date == date('Y-m-d') ) {
			$calendar .= ' today';
		}
		*/
		$calendar .= '<div style="margin-top:25px;">';
		$calendar .= $list_day;
		$calendar .= '</div>';
		
		if ( !$multipleEventsPerDay && $usesTimeOnDate AND isset($time) AND $time != '' ) {
			$calendar .= '<div class="time">'.$time.'</div>';
			unset($time);
		} 
		
		if ( $type == 'view' && $hasContent ) {
			
			$calendar .= '<div class="events"><ul>';
			
			$matches = array_keys($dates, $date);			
			for ( $i = 0; $i < count($matches); $i++ ) {
				
				$info = explode('**',substr($dateTimes[$matches[$i]],19));			
				$title = $info[0];
				$subTitle = $info[1];
				$href = $info[2];
				
				$calendar .= '<li>
								<span class="title"><a href="'.$href.'" title="Edit this Show">'.$title.'</a></span>
								<span class="desc">'.$subTitle.'</span>
							</li>';
				
			} $calendar .= '</ul></div>';
		}
			
		$calendar.= '</td>';
		if($running_day == 6):
			$calendar.= '</tr>';
			if(($day_counter+1) != $days_in_month):
				$calendar.= '<tr class="calendar-row">';
			endif;
			$running_day = -1;
			$days_in_this_week = 0;
		endif;
		$days_in_this_week++; $running_day++; $day_counter++;
	endfor;

	/* finish the rest of the days in the week */
	if($days_in_this_week < 8):
		for($x = 1; $x <= (8 - $days_in_this_week); $x++):
		//	$calendar.= '<td class="calendar-day-np"> </td>';
		endfor;
		$calendar .= '<td class="padding" colspan="'.$x.'"></td>';
	endif;

	/* final row */
	$calendar.= '</tr></tbody>';
	
	/* table footer */
	$calendar.= '<tfoot><tr><th>'.implode('</th><th>',$headings).'</th></tr></tfoot>';

	/* end the table */
	$calendar.= '</table></div>';
	
	
	// If using the drawer, insert it here
	if ( $multipleEventsPerDay ) {
		
		$calendar .= '<div id="drawer">';
		$calendar .= '<div id="titleWrapper"><span id="title">Info Drawer</span></div>';
		$calendar .= '<hr>';
		$calendar .= '<div id="toolbar"><div class="inner"><a href="#" id="addTime" title="Add a new Time">Time</a></div><hr /></div>';
		$calendar .= '<div id="drawerContent"><ul id="timeListing"><li>Choose date to get started.</li></ul></div>';		
		$calendar .= '</div>';		
		
	} 
	
	
	// Close off the calendar
	$calendar .= '<div class="clearBoth"></div></div>';
	
	$item = $calendar;

?>