<?

	////////////////////////////////////////////////////////////////////////////////
	//
	//	array_to_dropdown()
	//
	// Returns HTML for a <select> element from the input array
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function array_to_dropdown($options,$id="",$name="",$style="",$class="") {
		$html = '<select '.($id!==""?'id="'.$id.'" ':'').($name!==""?'name="'.$name.'" ':'').($style!==""?'style="'.$style.'" ':'').($class!==""?'style="'.$style.'"':'').'>';
		foreach($options as $i => $o) {
			$html .= '<option value="'.$i.'">'.$o.'</option>';
		}
		return $html . '</select>';
	}
	
?>