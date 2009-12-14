<?

	function fusion_chart($name='') {
		return MODULES_SERVER.'fusion_charts/swf/'.$name;
	} 
	
	function include_fusion_chart_js() {
		return '<script type="text/javascript" src="'.MODULES_SERVER.'fusion_charts/includes/fusionCharts.js"></script>';
	}

?>