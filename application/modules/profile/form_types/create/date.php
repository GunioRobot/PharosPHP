<?

	if ( get("date") ) $value = get("date");
	
	$dt = explode(' ', $value);
	$current_date = $dt[0];
	$tag = $form_array['name'];
	
	// ==================================================================
	// ====================== (Choose date) =============================
	if ( $current_date == '' && get($tag) ) {
		$current_date = get($tag);
	}
	
	if($current_date == '') {
		$dd = date("d");
		$mm = date("m");
		$yy = date("Y");
	} else {
		$yy = Substr($current_date, 0, 4);
		$mm = Substr($current_date, 5, 2);
		$dd = Substr($current_date, 8, 2);
	}

	// ==================================================================
	// ========================= (Month) ================================
		$item = '<select name="'.$tag.'_month" id="'.$tag.'_month">';
			for($i=1; $i<=12; $i++){
				$check='';if($i == $mm){$check='selected';}
				$item .= '<OPTION value="'.$i.'" '.$check.'>'.date ("F", mktime (0,0,0,$i+1,00,0000)).'</OPTION>';
			}
		$item .= '</select>';
		
		
	// ==================================================================
	// ========================== (Day) =================================
		$item .= '<select name="'.$tag.'_day" id="'.$tag.'_day" style="margin-left:10px;">';
		for($i=1; $i<=31; $i++){
			$num=$i;if($i < 10){$num='0'.$i;}
			$check='';if($num == $dd){$check='selected';}
			$item .= '<OPTION value="'.$num.'" '.$check.'>'.$num.'</OPTION>';
		}
		$item .= '</select>';
		
		
	// ==================================================================
	// ========================== (Year) ================================
		$da= getdate();
		$year = date ("Y", mktime (0,0,0,$da['mon'],$da['mday'],$da['year']));
		$item .= '<select name="'.$tag.'_year" id="'.$tag.'_year" style="margin-left:10px;">';
		for($i=1; $i<=3; $i++){
			$check='';if($year == $yy){$check='selected';}
			$item .= '<OPTION value="'.$year.'" '.$check.'>'.$year.'</OPTION>';
			$year++;
		}
		$item .= '</select>';	

?>