<?

	function fusion_chart($name='') {
		return MODULES_URL.'fusion_charts/swf/'.$name;
	} 
	
	function include_fusion_chart_js() {
		return '<script type="text/javascript" src="'.MODULES_URL.'fusion_charts/includes/fusionCharts.js"></script>';
	}

?>