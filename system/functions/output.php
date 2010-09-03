<?

	/**
	 * array_to_dropdown
	 *
	 * @param array $options
	 * @param (string|String|int) $selected=""
	 * @param (string|String) $id=""
	 * @param (string|String) $name=""
	 * @param (string|String) $style=""
	 * @param (string|String) $class=""
	 *
	 * @return String $html
	 * @author Matt Brewer
	 **/

	function array_to_dropdown($options,$default="",$id="",$name="",$style="",$class="") {
		$html = '<select '.($id!==""?'id="'.$id.'" ':'').($name!==""?'name="'.$name.'" ':'').($style!==""?'style="'.$style.'" ':'').($class!==""?'style="'.$style.'"':'').'>';
		foreach($options as $i => $o) {
			$html .= '<option value="'.$i.'" '.($default==$i?'selected="selected"':'').'>'.$o.'</option>';
		}
		return new String($html.'</select>');
	}
	
	
	/**
	 * enqueue_script
	 *
	 * @param (string|String|array) $path(s)
	 *
	 * @return void
	 * @author Matt Brewer
	 **/
	function enqueue_script($scripts) {
		if ( is_array($scripts) ) {
			foreach($scripts as $script) {
				Application::controller()->output->javascript($script)
			}
		} else {
			Application::controller()->output->javascript(strval($scripts));
		}
	}
	
?>