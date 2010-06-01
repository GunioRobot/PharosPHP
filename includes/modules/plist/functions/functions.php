<?

	function responsePlist($error=false, $reason="") {
	
		$plist = new CFPropertyList();
		$td = new CFTypeDetector();  
		
		$plist->add($dict = new CFDictionary());
		
		$response = array("error" => $error, "reason" => $reason);
		$dict->add("response", $td->toCFType($response));

		return array($plist, $dict);
	
	}
	
	
	function printPlist($plist) {
		$filename = tempnam("/tmp", "xml");
		
		$plist->saveBinary($filename);
		force_download($filename,"response.plist");
		unlink($filename);
		exit;
	}
	
	
	function throwErrorPlist($message) {
		list($plist, $dict) = responsePlist(true,$message);
		printPlist($plist);
	}
	
	function iphone_ready_text($s) {
		
		$s = html_entity_decode(stripslashes($s));
		
		$s = strip_tags($s, "<p><a>");		
		$s = str_replace("<p>","",$s);
		$s = str_replace("</p>","\n\n",$s);
		$s = str_replace("<br />", "\n", $s);
		
		return $s;
		
	}

?>