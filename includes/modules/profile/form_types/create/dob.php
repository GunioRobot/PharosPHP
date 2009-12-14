<?
if(isset($_GET[$form_array['name']])){
	$value = $_GET[$form_array['name']];
}
$dt=explode(' ',$value);
$current_date=$dt[0];
$tag=$form_array['name'];
// ====================== (Choose date) =============================
if($current_date == '')
{
	$da= getdate();
	$dd = date ("d", mktime (0,0,0,$da['mon'],$da['mday'],$da['year']));
	$mm = date ("m", mktime (0,0,0,$da['mon'],$da['mday'],$da['year']));
	$yy = date ("Y", mktime (0,0,0,$da['mon'],$da['mday'],$da['year']));
}
else
{
	$yy = Substr($current_date,0,4);
	$mm = Substr($current_date,5,2);
	$dd = Substr($current_date,8,2);
}
// ==================================================================
// ========================= (Month) ================================
	$item = '<select name="'.$tag.'_month" style="margin-right:10px;">';
		for($i=1; $i<=12; $i++){
			$check='';if($i == $mm){$check='selected';}
			$item .= '<OPTION value="'.$i.'" '.$check.'>'.date ("F", mktime (0,0,0,$i+1,00,0000)).'</OPTION>';
		}
	$item .= '</select>';
// ==================================================================
// ========================== (Day) =================================
	$item .= '<select name="'.$tag.'_day" style="margin-right:10px;">';
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
	$item .= '<select name="'.$tag.'_year">';
	for($i=80; $i>=0; $i--){
		$check='';if($year == $yy){$check='selected';}
		$item .= '<OPTION value="'.$year.'" '.$check.'>'.$year.'</OPTION>';
		$year--;
	}
	$item .= '</select>';	
// ==================================================================
?>