<?


	// Recreate the fields from the hidden post field
	$fields = explode(",", Input::post("field_list"));
	foreach($fields as $f) {
		$vars = explode("][", $f);
		$chr = array('[', ']');
		if ( $vars[0] != '[' && $vars[0] != '' ) {
			$temp[] = array('name' => str_replace($chr, '', $vars[0]), 'type' => str_replace($chr, '', $vars[1]), 'max' => str_replace($chr, '', $vars[2]), 'varx' => str_replace($chr, '', $vars[3]));
		}
	} $fields = $temp; unset($temp);

	// Will contain SQL at the end of it all
	$sqlUpdate = '';
	$sqlFields = '';
	$sqlValues = '';
		
	NotificationCenter::execute(EXTERNAL_HOOK_PROFILE_MODULE_PRE_PROCESSED, $fields);
	
	// Now process each of the incoming form fields
	foreach($fields as $index => $input) {
	
		$data = $input['name'];
		$quoteValue = true;
		
		// Try to process the form_type, otherwise just log to console and continue
		try {
			$f = process_form_type($input['type']);
			require $f;
		} catch (Exception $e) {
			Console::log($e->getMessage());
			Console::log("Using default form_type to process (".$input['type'].")");
			require FORM_TYPE_DIR.'save/default.php';
		}
								

		// Update all the sql stuff
		if ( $data ) {
		
			// MySQL command, don't wrap in quotes
			if ( !$quoteValue ) {
				$sqlUpdate .= $data.'='.$_POST[$data].',';
				$sqlFields .= $data.',';
				$sqlValues .= $_POST[$data].',';
			} else { 
				$sqlUpdate .= $data.'="'.$db->prepare_input($_POST[$data]).'",';
				$sqlFields .= $data.',';
				$sqlValues .= '"'.$db->prepare_input($_POST[$data]).'",';
			}
			
		}
		
	}	
	
	// Strip last commas from all the fields
	$sqlUpdate = substr($sqlUpdate, 0, -1);
	$sqlFields = substr($sqlFields, 0, -1);
	$sqlValues = substr($sqlValues, 0, -1);
	
		
	// Create and execute the lovely MySQL statement
	$table = Input::post("table");
	if ( $id > 0 && $table && ($key = Input::post("data_key")) ) {
	
		// Update db
		$sql = "UPDATE $table SET ".$sqlUpdate." WHERE $key = '$id'";
		$db->Execute($sql);
		
	} else {
	
		// Insert into db
		$sql = "INSERT INTO $table (".$sqlFields.") VALUES(".$sqlValues.")";
		$db->Execute($sql);
				
		// Store newly created id		
		$id = $db->insert_ID();
		
	}
	
	NotificationCenter::execute(EXTERNAL_HOOK_PROFILE_MODULE_POST_PROCESSED, $fields, $id);
	
?>