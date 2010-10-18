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
				Application::controller()->output->javascript(strval($script));
			}
		} else {
			Application::controller()->output->javascript(strval($scripts));
		}
	}
	
	
	/**
	 * enqueue_style
	 *
	 * @param (string|String|array) $path(s)
	 *
	 * @return void
	 * @author Matt Brewer
	 **/
	function enqueue_style($scripts) {
		if ( is_array($scripts) ) {
			foreach($scripts as $script) {
				Application::controller()->output->css(strval($script));
			}
		} else {
			Application::controller()->output->css(strval($scripts));
		}
	}
	
	
	/**
	 * render_view
	 *
	 * @param string $view - path to view, or static string
	 * @param array $params - values to be made available inside the view
	 * @param boolean $controller - true if should be rendered on the main controller, false if just want to render the view and up to you to use the returned value
	 *
	 * @return string $view
	 * @author Matt Brewer
	 **/
	function render_view($view, array $params=array(), $controller=true) {
		
		$output = $controller === true ? Application::controller()->output : new Output();
		foreach($params as $key => $value) {
			$output->set($key, $value);
		}
		
		return $output->view($view);
		
	}
	
?>