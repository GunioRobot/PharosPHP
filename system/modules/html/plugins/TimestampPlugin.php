<?

	/**
	 * TimestampPlugin
	 *
	 * @since 1.3
	 * @package PharosPHP.Modules.HTML.Plugins
	 * @author Matt Brewer
	 **/
	
	class TimestampPlugin extends HTMLPlugin {
		
		
		/**
		 * html
		 * Returns generated HTML from this plugin
		 *
		 * @return string $html
		 * @author Matt Brewer
		 **/
		
		public function html($name, $value="", $id="", $display=false) {
			parent::html($name, $id);
			$this->value = $value;
			$ret = '';
			if ( $display ) {
				$ret .= '<p>' . format_date($this->value) . '</p>';
			} return $ret .= '<input type="hidden" name="' . $this->name . '" id="' . $this->id . '" value="' . $this->value . '" />';
		}
		
		
		/**
		 * save
		 * Processes the form request and provides values to processor
		 * 
		 * @param string $method
		 *
		 * @return HTMLPluginResponse $response
		 * @author Matt Brewer
		 **/
		
		public function save($method="post") {
			if ( ($timestamp = Input::post($this->name)) !== false ) {
				return new HTMLPluginResponse($this->name, );
			}
		}
		
	} 

?>