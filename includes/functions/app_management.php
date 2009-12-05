<?php

	define('DEFAULT_APP_ID', 1);
	$CURRENT_APP_ID = DEFAULT_APP_ID;
	$CURRENT_APP_NAME = "Some App";
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	app_bootstrap()
	//
	//	Loads the current application into the system
	//
	////////////////////////////////////////////////////////////////////////////////
	function app_bootstrap() {
		
		global $db, $CURRENT_APP_ID, $CURRENT_APP_NAME;
						
		$CURRENT_APP_ID = session("app_id", DEFAULT_APP_ID);
				
		$title = $db->Execute("SELECT app_name FROM applications WHERE app_id = '$CURRENT_APP_ID' LIMIT 1");
		$CURRENT_APP_NAME = format_title($title->fields['app_name']);
				
	}	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	load_content_types() 
	//
	//	defines "USER_TYPE_ID" etc from the entries in the content_types_table
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function load_content_types() {
		
		global $db;
		
		$sql = "SELECT * FROM content_types ORDER BY type_id DESC";
		for ( $info = $db->Execute($sql); !$info->EOF; $info->moveNext() ) {
			@define(strtoupper($info->fields['type_name']).'_TYPE_ID', $info->fields['type_id']);
		}
		
	}
	


	////////////////////////////////////////////////////////////////////////////////
	//
	//	select_app($id)
	//
	//	Sets the id for the current application.  Must refresh page to call 
	//	app_bootstrap() after this
	//
	////////////////////////////////////////////////////////////////////////////////
	function select_app($id) {
				
		$CURRENT_APP_ID = $id;
		$_SESSION['app_id'] = $CURRENT_APP_ID;
						
	}
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	list_applications($selected=1)
	//
	//	<select></select> HTML for the available applications (user-level-access)
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function list_applications($selected=1) {
						
		global $db;

		$select = '';
		if ( is_super() ) { 
			$sql = "SELECT * FROM applications ORDER BY app_name ASC";
		} else if ( is_admin() ) $sql = "SELECT * FROM applications WHERE active = 'true' ORDER BY app_name ASC";
		
		for ( $apps = $db->Execute($sql); !$apps->EOF; $apps->moveNext() ) {

			$id = $apps->fields['app_id'];

			$select .= '<option id="app_'.$id.'" value="'.$id.'"';
			if ( $selected == $id ) $select .= ' selected="selected"';
			$select .= '>'.format_title($apps->fields['app_name']).'</option>';
		}

		return $select;

	}
	



	////////////////////////////////////////////////////////////////////////////////
	//
	//	write_xml($app)
	//		$app = array('app_id' => int)
	//
	//	Writes the xml to the XML_DIR, with versioning, etc
	//
	////////////////////////////////////////////////////////////////////////////////

	function write_xml($app) {


		// Object we return
		$ret->error = false;
		$ret->message = '';



		/* 

			Check all the folders that are required for this application and it's xml.

		*/

		// Make sure there is an output folder ready
		$xmlFolder = XML_DIR;
		if ( !is_dir($xmlFolder) ) {
			if ( !mkdir($xmlFolder) ) {
				$ret->error = true;
				$ret->message = "There was an error attempting to create the folder containing the XML.";
			}
		}


		// Now make sure the application folder exists
		$appFolder = $xmlFolder.$app['app_id'].'/';	
		if ( !is_dir($appFolder) ) {
			if ( !mkdir($appFolder) ) {
				$ret->error = true;
				$ret->message .= "<br />There was an error attempting to create the application folder.";
			}
		}


		// Now make sure there is an archive folder inside the application folder
		$archiveFolder = $appFolder.'archives/';
		if ( !is_dir($archiveFolder) ) {
			if ( !mkdir($archiveFolder) ) {
				$ret->error = true;
				$ret->message .= "<br />There was an error attempting to create the archive folder.";
			}
		}


		// If an error so far, don't bother creating the xml as we don't have a place to put it
		if ( $ret->error ) return $ret;






		/*

			Write the new XML to temp file, read back in and compare to what was supposed to be written.
			Then move old xml to archive folder and rename temp to current.

		*/



		/*

			Write the XML for the content - current.xml

		*/
		$xml = content_xml($app);
		if ( strlen($xml) > 0 ) {

			// Write to a temporary file
			$tempFile = $appFolder.'temp'.rand(1,99).'.xml';
			$f = @fopen($tempFile, 'w');
			if ( $f ) {

				// Immediately change permissions on the temp file
				chmod($tempFile, 0755);

				// Write to file, check for errors
				if ( @fwrite($f, $xml) === FALSE ) {
					$ret->error = true;
					$ret->message .= "<br />There was an error attempting to write the XML.";
				} else {

					// Read in the contents of the file and compare to the original string
					$writtenXML = @file_get_contents($tempFile);
					if ( $writtenXML != $xml ) {
						$ret->error = true;
						$ret->messgae .= "<br />There was an error attempting to write the XML.  Contents of temporary file did not match.";
					} else {

						// Move the previous current xml to the archive folder (if there was a previous current)
						$current = $appFolder.'current.xml';
						if ( @file_exists($current) ) {

							$archivedXML = $archiveFolder.date('Y-m-d G:i').'.xml';
							if ( @rename($current, $archivedXML ) === FALSE ) {
								$ret->error = true;
								$ret->message .= "<br />There was an error copying the current xml to the archive folder.";
							} else {

								// Current is now in the archive folder, so rename the temp to current
								if ( @rename($tempFile, $current) === FALSE ) {

									$ret->error = true;
									$ret->message .= "<br />There was an error making the new XML active.  Rolling back to last archived XML.";

									if ( @rename($archivedXML, $current) === FALSE ) {
										$ret->error = true;
										$ret->message .= "<br />There was an error rolling back to the archived XML.";
									}

								} 

							}

						} else {

							// Current doesn't exist, so just go ahead and rename temp to current
							if ( @rename($tempFile, $current) === FALSE ) {

								$ret->error = true;
								$ret->message .= "<br />There was an error making the new XML active.  Rolling back to last archived XML.";

								if ( @rename($archivedXML, $current) === FALSE ) {
									$ret->error = true;
									$ret->message .= "<br />There was an error rolling back to the archived XML.";
								}

							}

						}

					}

				} fclose($f);

			} else {
				$ret->error = true;
				$ret->message .= "<br />There was an error attempting to write the XML.";
			}


		} else {
			$ret->error = true;
			$ret->message .= "<br />There was an error creating the XML.";
		}








		/*

			Write the XML for the application versioning - version.xml

		*/
		$xml = app_xml($app);
		if ( strlen($xml) > 0 ) {

			// Write to a temporary file
			$tempFile = $appFolder.'app'.rand(1,99).'.xml';
			$f = @fopen($tempFile, 'w');
			if ( $f ) {

				// Immediately change permissions on the temp file
				chmod($tempFile, 0755);

				// Write to file, check for errors
				if ( @fwrite($f, $xml) === FALSE ) {
					$ret->error = true;
					$ret->message .= "<br />There was an error attempting to write the XML.";
				} else {

					// Read in the contents of the file and compare to the original string
					$writtenXML = @file_get_contents($tempFile);
					if ( $writtenXML != $xml ) {
						$ret->error = true;
						$ret->messgae .= "<br />There was an error attempting to write the XML.  Contents of temporary file did not match.";
					} else {

						// Delete the current version.xml
						$current = $appFolder.'version.xml';
						@unlink($current);

						// Rename the temp to version.xml
						if ( @rename($tempFile, $current) === FALSE ) {

							$ret->error = true;
							$ret->message .= "<br />There was an error making the new XML active.  Serious Error.";

						}

					}

				} fclose($f);

			} else {
				$ret->error = true;
				$ret->message .= "<br />There was an error attempting to write the XML.";
			}


		} else {
			$ret->error = true;
			$ret->message .= "<br />There was an error creating the XML.";
		}


		// Give all the error messages, if any, to calling function
		return $ret;

	}







	////////////////////////////////////////////////////////////////////////////////
	//
	//	content_xml($app)
	//		$app = array('app_id' => int)
	//
	//	Returns string of the application content xml
	//
	////////////////////////////////////////////////////////////////////////////////

	function content_xml($app) {

	//	$pathPrefix = UPLOAD_SERVER;
		$pathPrefix = '';

		global $db;
		responseXML("false", "", $dom, $root);	// A new DOMDocument for dom, and root element, error set to false

		return $dom->saveXML();


	}





	////////////////////////////////////////////////////////////////////////////////
	//
	//	app_xml($app)
	//		$app = array('app_id', int)
	//
	//	Returns string of XML for the application itself and content xml versioning
	//
	////////////////////////////////////////////////////////////////////////////////

	function app_xml($app) {

		$xml = '<?xml version="1.0" encoding="UTF-8" ?>'."\n".'<root>'."\n";
		$xml .= '	<version date="'.date('m/d/y').'" vnumber="'.$app['xml_version'].'" appnumber="'.$app['app_version'].'" xmlpath="current.xml" appPath=""></version>'."\n";
		$xml .= '</root>';

		return $xml;
	}




?>