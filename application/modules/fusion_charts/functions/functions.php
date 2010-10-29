<?

	/**
	 * fusion_chart
	 * Returns URL to the appropriate swf file
	 *
	 * @return void
	 * @author Matt Brewer
	 **/

	function fusion_chart($name='') {
		return module_url(realpath(dirname(__FILE__).'/../')) . DS . 'swf' . DS . $name;
	} 
	
	
	/**
	 * include_fusion_chart_js
	 * Enqueues js file for this library
	 *
	 * @return void
	 * @author Matt Brewer
	 **/

	function include_fusion_chart_js() {
		enqueue_script(module_url(realpath(dirname(__FILE__).'/../')) . 'includes' . DS . 'fusionCharts.js');
	}

?>