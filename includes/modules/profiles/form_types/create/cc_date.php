<?
if(isset($_GET['date'])){
	$value = $_GET['date'];
}
$dt=explode(' ',$value);
$current_date=$dt[0];
$tag=$form_array['name'];
// ====================== (Choose date) =============================
if($current_date == '' && isset($_GET[$tag])){
	$current_date = $_GET[$tag];
}
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
	$item = '<select name="'.$tag.'_month">';
		for($i=1; $i<=12; $i++){
			$check='';if($i == $mm){$check='selected';}
			$item .= '<OPTION value="'.$i.'" '.$check.'>'.date ("F", mktime (0,0,0,$i+1,00,0000)).'</OPTION>';
		}
	$item .= '</select>';
// ==================================================================
// ========================== (Year) ================================
	$da= getdate();
	$year = date ("Y", mktime (0,0,0,$da['mon'],$da['mday'],$da['year']));
	$item .= '<select name="'.$tag.'_year">';
	for($i=1; $i<=10; $i++){
		$check='';if($year == $yy){$check='selected';}
		$item .= '<OPTION value="'.$year.'" '.$check.'>'.$year.'</OPTION>';
		$year++;
	}
	$item .= '</select>';	
// ==================================================================
?>