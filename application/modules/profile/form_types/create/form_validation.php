<?
$vldte = rand(1,4);
$t=getdate(); 
$str=md5('apple'.date('Y-m-d',$t[0]).$vldte);
$item = '<table><tr><td><img src="/images/spot'.$vldte.'.gif" />';
$item .= '<input name="'.$form_array['name'].'_vldte" type="hidden" value="'.$str.'" /></td> <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>';
$item .= '<td><input name="'.$form_array['name'].'" type="text" value=" " /></td></tr></table>';
?>