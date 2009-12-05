<?
	
	// Configuration and validation
	require 'includes/app_header.php';

	// Call the repost mod
	if ( ($action = get("action")) ) {
		
		$file = REPOST_DIR.$action.'.php';
		if ( file_exists($file) ) {
			
			require $file;
			
			$options = '';
			$fullOptions = array_merge($_GET, $_POST);
			foreach($fullOptions as $key => $value) {
				$options .= "&$key=$value";
			}
			
			$file = get("alt_file", "index");
			redirect("$file.php?repost=true$options");
				
		} else {
			die("Action not set:".$action);
		}
		
	} else {
		die("Action not provided");
	}
	
?>