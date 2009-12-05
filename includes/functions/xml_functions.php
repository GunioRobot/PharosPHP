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
		$error->appendChild($dom->createTextNode($status));
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

		$s = str_replace('&trade;', '&#8482;', $s);
		$s = str_replace('&amp;trade;', '&#8482;', $s);

		$s = str_replace('&copy;', '&#169;', $s);
		$s = str_replace('&amp;copy;', '&#169;', $s);

		$s = str_replace('&reg;', '&#174;', $s);
		$s = str_replace('&amp;reg;', '&#174;', $s);

		$s = str_replace('&rsquo;', "'", $s);
		$s = str_replace('&rlquo;', "'", $s);
		$s = str_replace('&apos;', "'", $s);

		$search = '<a href=';
		$cons = '<font color="'.$color.'"><u>';

		$ppos = strpos($s, $search);
		$pos = 0;
		while ( $ppos !== FALSE ) {

			$s = insert_substr($s, $ppos, $cons);
			$pos = $ppos+1+strlen($cons);

			$ppos = strpos($s, $search, $pos);
		}

		$search = '</a>';
		$cons = '</font></u>';

		$ppos = strpos($s, $search);
		$pos = 0;
		while ( $ppos !== FALSE ) {

			$s = insert_substr($s, $ppos+strlen($search), $cons);
			$pos = $ppos+1+strlen($cons);

			$ppos = strpos($s, $search, $pos);
		}

		return $s;
	}
	
?>