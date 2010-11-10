<?

	
	/**
	 * responseXML
	 * Creates the standardized response XML used in API communications
	 *
	 * @param boolean $status
	 * @param string $reason
	 * @param DOMDocument &$dom
	 * @param DOMElement &$root
	 *
	 * @return DOMElement $response
	 * @author Matt Brewer
	 **/

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
	
	
	/**
	 * throwErrorXML
	 * Provides error XML with the provided message as the response
	 * NOTE: Ends script execution
	 *
	 * @param string $message
	 *
	 * @return void
	 * @author Matt Brewer
	 **/

	function throwErrorXML($message) {
		responseXML("true", $message, $dom);
		printXML($dom->saveXML());
	}
	
	
	/**
	 * printXML
	 * Sends the XML to the browser with the appropriate HTTP headers
	 * NOTE: Ends script execution
	 *
	 * @param string $XML
	 *
	 * @return void
	 * @author Matt Brewer
	 **/

	function printXML($XML) {
		header('Content-type: text/xml');
		print $XML;
		exit;
	}
	
	
	/**
	 * xml_data
	 * Converts known HTML idiosycracies to flash loveable characters
	 *
	 * @param string $s
	 * @param string $color_for_hyperlink
	 * 
	 * @uses Hooks::FILTER_XML_FLASH_CDATA
	 *
	 * @return string $filtered
	 * @author Matt Brewer
	 **/

	function xml_data($s, $color="#f09bc2") {
		
		$s = str_replace('<strong>', '<font color="#000000">', $s);
		$s = str_replace('</strong>', '</font>', $s);
		$s = str_replace('<em>', '<i>', $s);
		$s = str_replace('</em>', '</i>', $s);

		$s = str_replace(array('&amp;trade;','&trade;','™'), '&#8482;', $s);
		$s = str_replace(array('&amp;copy;','&copy;',"©"), '&#169;', $s);
		$s = str_replace(array('&amp;reg;','&reg;','®'), '&#174;', $s);
		$s = str_replace(array('&rsquo;','&apos;','&rlquo;'), "'", $s);
		$s = str_replace(array("&amp;ldquo;", "&ldquo;", "&amp;rdquo;", "&rdquo;"), '"', $s);
		$s = str_replace("&bull;", "&#149;", $s);
		$s = str_replace(array("&amp;hellip;", "&hellip;"), "...", $s);
		$s = str_replace(array("&amp;ndash;", "&ndash;", "&amp;mdash;", "&mdash;"), "-", $s);

		$value = preg_replace('/<a([^>]*)>([^<]*)<\/a>/i', '<a$1><u><font color="'.$color.'">$2</font></u></a>', $s);
		
		return Hooks::execute(Hooks::FILTER_XML_FLASH_CDATA, compact("value", "color"));
		
	}
	
	
	/**
	 * flash_tlf_format_str
	 * Formats the string for use in a TLF field (ActionScript 3)
	 *
	 * @param string $str
	 * 
	 * @uses Hooks::FILTER_XML_FLASH_TLF_FORMAT
	 *
	 * @return string $filtered
	 * @author Matt Brewer
	 **/

	function flash_tlf_format_str($str) {
		
		// Format bold/italic text
		$str = str_replace(array("<strong>", "</strong>"), array('<span fontWeight="bold">', "</span>"), $str);
		$str = str_replace(array("<em>", "</em>"), array('<span fontStyle="italic">', '</span>'), $str);

		// Replace invalid HTML character codes with Hex equivalents
		$str = str_replace(array('&amp;trade;','&trade;','™'), '&#8482;', $str);
		$str = str_replace(array('&amp;copy;','&copy;',"©"), '&#169;', $str);
		$str = str_replace(array('&amp;reg;','&reg;','®'), '&#174;', $str);
		$str = str_replace(array('&rsquo;','&apos;','&rlquo;'), "'", $str);
		$str = str_replace(array("&amp;ldquo;", "&ldquo;", "&amp;rdquo;", "&rdquo;"), '"', $str);
		$str = str_replace("&bull;", "&#149;", $str);
		$str = str_replace(array("&amp;hellip;", "&hellip;"), "...", $str);
		$str = str_replace(array("&amp;ndash;", "&ndash;", "&amp;mdash;", "&mdash;"), "-", $str);
		
		return Hooks::execute(Hooks::FILTER_XML_FLASH_TLF_FORMAT, array("value" => $str));
		
	}


	/**
	 * fix_string_encoding
	 * Converts string to UTF-8 encoding if not already
	 * 
	 * @param string $str
	 *
	 * @return string $utf8_string
	 * @author Matt Brewer
	 **/

	function fix_string_encoding($str) {
 		
		$cur_encoding = mb_detect_encoding($str);
		if ( $cur_encoding == "UTF-8" && mb_check_encoding($str, "UTF-8") ) {
			return $str;
		} else {
		    return utf8_encode($str);
		}
	}
	
	
	/**
	 * write_xml_with_xml
	 * Writes the given XML to the appropriate files on the server, creating an archive of the previous XML as well
	 *
	 * @param string $app_folder
	 * @param string $archive_folder
	 * @param string $xml
	 * @param string $name
	 *
	 * @return stdClass (error, message)
	 * @author Matt Brewer
	 **/
	
	function write_xml_with_xml($app_folder, $archive_folder, $xml, $name) {
		
		// Object we return
		$ret->error = false;
		$ret->message = '';
		
		if ( strlen($xml) > 0 ) {

			// Write to a temporary file
			$temp = $app_folder.date('U').'_'.$name.'.xml';
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
							if ( rename($temp, $current) === FALSE ) {

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