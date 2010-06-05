<?
	
	$tag=$form_array['name'];
	$date = ($value === "" || $value === "0000-00-00 00:00:00") ? new DateTime() : new DateTime($value);
	
//	var_dump($value,$date->format("m-d-Y \a\t H:i"));exit;
	
	$dd = $date->format('d');
	$mm = $date->format('m');
	$yy = $date->format('Y');


	// Month Chooser
	$item = '<select name="'.$tag.'_month">';
	for ( $i = 1; $i <= 12; $i++ ) {
		$item .= '<option value="'.$i.'" '.($i==$mm?"selected":"").'>'.date ("F", mktime (0,0,0,$i+1,00,0000)).'</option>';
	} $item .= '</select>';

	
	// Day Chooser
	$item .= '<select name="'.$tag.'_day" style="margin-left:10px;">';
	for ( $i = 1; $i <= 31; $i++ ) {
		$num = $i < 10 ? '0'.$i : $i;
		$item .= '<option value="'.$num.'" '.($num==$dd?"selected":"").'>'.$num.'</option>';
	} $item .= '</select>';


	// Year Chooser
	$_5_years_ago = new DateTime();
	$_5_years_ago->modify('-5 year');
	$year = $_5_years_ago->format('Y');
	
	$item .= '<select name="'.$tag.'_year" style="margin-left:10px;">';
	for ( $i = 1; $i <= 9; $i++, $year++ ) {
		$item .= '<option value="'.$year.'" '.($year==$yy?"selected":"").'>'.$year.'</option>';
	} $item .= '</select>&nbsp;&nbsp;&nbsp;&nbsp;at ';	


	// Make sure time is multiple of 15
	$minutes = $date->format("i");
	if ( $minutes % 15 > 0 ) {
		$min = 15 - ($minutes % 15);
		$date->modify('+ '.$min." minutes");
		$mm = $date->format('m');
	}

	// Time chooser
	$item .= '<select name="'.$tag.'_time" style="margin-left:10px;">';
	for ( $i = 0; $i < 24; $i++ ) {
		
		$ctime = $date->format("H:i");
	
		$ftime = date("H:i", mktime ($i,0,0,$mm,$dd,$yy));
		$time = date("h:i A", mktime ($i,0,0,$mm,$dd,$yy));
		$item .= '<option value="'.$ftime.'" '.($ctime==$ftime?"selected":"").'>'.$time.'</option>';
		
		$time = date ("h:i A", mktime ($i,15,0,$mm,$dd,$yy));
		$ftime = date ("H:i", mktime ($i,15,0,$mm,$dd,$yy));
		$item .= '<option value="'.$ftime.'" '.($ctime==$ftime?"selected":"").'>'.$time.'</option>';
		
		$time = date ("h:i A", mktime ($i,30,0,$mm,$dd,$yy));
		$ftime = date ("H:i", mktime ($i,30,0,$mm,$dd,$yy));
		$item .= '<option value="'.$ftime.'" '.($ctime==$ftime?"selected":"").'>'.$time.'</option>';
		
		$time = date ("h:i A", mktime ($i,45,0,$mm,$dd,$yy));
		$ftime = date ("H:i", mktime ($i,45,0,$mm,$dd,$yy));
		$item .= '<option value="'.$ftime.'" '.($ctime==$ftime?"selected":"").'>'.$time.'</option>';
		
	} $item .= '</select>';

?>