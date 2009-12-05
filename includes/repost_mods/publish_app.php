<?php

	if ( ($appID = request("app_id")) ) {
			
		// App info
		$sql = "SELECT * FROM applications WHERE app_id = '$appID' LIMIT 1";
		$app = $db->Execute($sql);
		
		// Just as a precaution, ignore if the app_id doesn't match anything in the system
		if ( !$app->EOF ) {
			
			// Update the xml version and write a new xml file
			$newVersion = floatval($app->fields['xml_version']) + 0.1;
			$app->fields['xml_version'] = $newVersion;
			
			$status = write_xml($app->fields);
			if ( !$status->error ) {
				
				// Place new version in the db
				$sql = "UPDATE applications SET xml_version = '$newVersion' WHERE app_id = '$appID' LIMIT 1";
				$db->Execute($sql);
				
				// No error, but let user see there wasn't
				$_SESSION['errorTitle'] = "Application Successfully Published";
				$_SESSION['errorMessage'] = "Your application was successfully published.<br /><br />";
				$_SESSION['errorMessage'] .= "Users will see the changes the next time they launch their application and are connected to the internet.";
				
				$_SESSION['useLeftLink'] = 'false';
				$_SESSION['linkText'] = "Continue";
				$_SESSION['linkSrc'] = "index.php";
				
				
			} else {
				
				// Was an error, so show that error to user
				$_SESSION['errorTitle'] = "Error Publishing App";
				$_SESSION['errorMessage'] = "<strong>There were errors publishing changes to the application:</strong><br /><br />";
				$_SESSION['errorMessage'] .= $status->message;
				$_SESSION['errorMessage'] .= "<strong>Your application has not been updated!</strong>";
				
				$_SESSION['useLeftLink'] = 'false';
				$_SESSION['linkText'] = "Continue";
				$_SESSION['linkSrc'] = "index.php";
				
			}
			
			// Show the message, error or not
			redirect('index.php?function=error');
						
		}
		
	}


?>