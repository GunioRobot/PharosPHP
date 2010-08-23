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
	
	
	function write_xml_with_xml($app_folder, $archive_folder, $xml, $name) {
		
		// Object we return
		$ret->error = false;
		$ret->message = '';
		
		if ( strlen($xml) > 0 ) {

			// Write to a temporary file
			$temp = tempnam($app_folder, "xml");
			if ( ($f = @fopen($temp, 'w')) !== false ) {

				// Immediately change permissions on the temp file
				chmod($temp, 0755);

				// Write to file, check for errors
				$write_status = @fwrite($f, $xml);
				@fclose($f);
				
				if ( $write_status === false ) {
					$ret->error = true;
					$ret->message .= "There was an error attempting to write the XML.<br />";
				} else {

					// Read in the contents of the file and compare to the original string
					$writtenXML = @file_get_contents($temp);
					if ( $writtenXML != $xml ) {
						$ret->error = true;
						$ret->message .= "There was an error attempting to write the XML.  Contents of temporary file did not match.<br />";
					} else {

						// Move the previous current xml to the archive folder (if there was a previous current)
						$current = $app_folder.$name.'.xml';
						if ( @file_exists($current) ) {

							$archivedXML = $archive_folder.$name.'_'.date('Y-m-d_G:i').'.xml';
							if ( @rename($current, $archivedXML ) === FALSE ) {
								$ret->error = true;
								$ret->message .= "There was an error copying the current xml to the archive folder.<br />";
							} else {

								// Current is now in the archive folder, so rename the temp to current
								if ( @rename($temp, $current) === FALSE ) {

									$ret->error = true;
									$ret->message .= "There was an error making the new XML active.  Rolling back to last archived XML.<br />";

									if ( @rename($archivedXML, $current) === FALSE ) {
										$ret->error = true;
										$ret->message .= "There was an error rolling back to the archived XML.<br />";
									}

								} 

							}

						} else {

							// Current doesn't exist, so just go ahead and rename temp to current
							if ( @rename($temp, $current) === FALSE ) {

								$ret->error = true;
								$ret->message .= "There was an error making the new XML active.  Rolling back to last archived XML.<br />";

								if ( @rename($archivedXML, $current) === FALSE ) {
									$ret->error = true;
									$ret->message .= "There was an error rolling back to the archived XML.<br />";
								}

							}

						}

					}

				} 

			} else {
				$ret->error = true;
				$ret->message .= "There was an error attempting to write the XML.<br />";
			}


		} else {
			$ret->error = true;
			$ret->message .= "There was an error creating the XML.<br />";
		}
		
		return $ret;
		
	}
	
?>