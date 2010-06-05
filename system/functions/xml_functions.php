<?

	////////////////////////////////////////////////////////////////////////////////
	//
	//	responseXML()
	//
	//	Creates the standard <response></response> node used when communicating
	//	with our flash apps.  Returns DOMNode for $response
	//
	//	USAGE:
	//
	//		responseXML("false", "", $dom, $root);
	//
	//		$dom is now a DOMDocument ($dom->saveXML(), etc)
	//		$root is the <root></root> DOMNode to append stuff to
	//
	////////////////////////////////////////////////////////////////////////////////

	function responseXML($status, $reason, &$dom=null, &$root=null) {

		$dom = !is_null($dom) ? $dom : new DOMDocument('1.0', 'iso-8859-1');
		$root = !is_null($root) ? $root : $dom->createElement('root');
		$dom->appendChild($root);
		
		$response = $dom->createElement('response');
		$name = $dom->createElement('name');
		$error = $dom->createElement('error');
		$error->appendChild($dom->createTextNode(($status==="true"||$status===true)?"true":"false"));
		$name->appendChild($dom->createTextNode($reason));
		$response->appendChild($name);
		$response->appendChild($error);
		$root->appendChild($response);
						
		return $response;
	}
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	throwErrorXML($reason)
	//
	// 	Takes a string as an error reason and pushes response XML to the browser.
	//	Mainly used when communicating with our flash apps.
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function throwErrorXML($reason) {
		responseXML("true", $reason, $dom);
		printXML($dom->saveXML());
	}
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	printXML($string) 
	//
	//	Pushes the string to the browser with correct content type
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function printXML($XML) {
		header('Content-type: text/xml');
		print $XML;
		exit;
	}
	
	

	////////////////////////////////////////////////////////////////////////////////
	//
	//	xml_data($s)
	//
	//	Legacy Hack
	//
	////////////////////////////////////////////////////////////////////////////////

	function xml_data($s) {
		return flash_xml_data($s);
	}



	////////////////////////////////////////////////////////////////////////////////
	//
	//	flash_xml_data($string, $color)
	//
	//	Converts known HTML idiosycracies to flash loveable characters. Color is 
	//	hyperlink color.
	//
	////////////////////////////////////////////////////////////////////////////////

	function flash_xml_data($s, $color="#f09bc2") {
						
		$s = str_replace('<strong>', '<font color="#000000">', $s);
		$s = str_replace('</strong>', '</font>', $s);
		$s = str_replace('<em>', '<i>', $s);
		$s = str_replace('</em>', '</i>', $s);

		$s = str_replace(array('&amp;trade;','&trade;','™'), '&#8482;', $s);
		$s = str_replace(array('&amp;copy;','&copy;',"©"), '&#169;', $s);
		$s = str_replace(array('&amp;reg;','&reg;','®'), '&#174;', $s);
		$s = str_replace(array('&rsquo;','&apos;','&rlquo;'), "'", $s);

		return preg_replace('/<a([^>]*)>([^<]*)<\/a>/i', '<a$1><u><font color="'.$color.'">$2</font></u></a>', $s);
		
	}
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	fixStringEncoding($string)
	//
	//	Converts input string to the proper encoding 
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function fixStringEncoding($in_str) {
 		
		$cur_encoding = mb_detect_encoding($in_str);
		if ( $cur_encoding == "UTF-8" && mb_check_encoding($in_str,"UTF-8") ) {
			return $in_str;
		} else {
		    return utf8_encode($in_str);
		}
	}
	
?>