<?


	// Recreate the fields from the hidden post field
	$fields = explode(",", post("field_list"));
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
	
	// OWENS CORNING HACK
	$USE_APPS_TO_CONTENT_TABLE = false;
	
	// Now process each of the incoming form fields
	foreach($fields as $index => $input) {
	
		$data = $input['name'];
		$quoteValue = true;
		
		// OWENS CORNING HACK
		if ( $data == 'content_type_id' AND $_POST[$data] != '' ) {
			$USE_APPS_TO_CONTENT_TABLE = true;
			continue;
		}

		// OWENS CORNING HACK
		if ( $data == 'content_type_name' ) {
			continue;
		}
		
		
		// Try to process the form_type, otherwise just log to console and continue
		try {
			$f = process_repost_type($input['type']);
			require $f;
		} catch (Exception $e) {
			Console::log($e->getMessage());
			Console::log("Using default form_type to process (".$input['type'].")");
			require INCLUDES_DIR.'repost_mods/form_types/default.php';
		}
								

		// Update all the sql stuff
		if ( $data ) {
		
			// MySQL command, don't wrap in quotes
			if ( !$quoteValue ) {
				$sqlUpdate .= $data.'='.$_POST[$data].',';
				$sqlFields .= $data.',';
				$sqlValues .= $_POST[$data].',';
			} else { 
				$sqlUpdate .= $data.'="'.$_POST[$data].'",';
				$sqlFields .= $data.',';
				$sqlValues .= '"'.$_POST[$data].'",';
			}
			
		}
		
	}
	
	
	// Strip last commas from all the fields
	$sqlUpdate = substr($sqlUpdate, 0, -1);
	$sqlFields = substr($sqlFields, 0, -1);
	$sqlValues = substr($sqlValues, 0, -1);
	
		
	// Create and execute the lovely MySQL statement
	$table = post("table");
	if ( ($key = get("key")) && ($id = get($key)) && $table ) {
	
		 $sql = "UPDATE $table SET ".$sqlUpdate." WHERE $key = '$id'";
		 $db->Execute($sql);
		
	} else {
	
		$sql = "INSERT INTO $table (".$sqlFields.") VALUES(".$sqlValues.")";
		$db->Execute($sql);
		
		if ( ($tempKey = get("key_temp")) ) {
			$_GET['key'] = $tempKey;
		}
				
		$_GET[get("key")] = $db->insert_ID();
		
	}
	
	
	if ( $table == "company_files" && post('file_path') ) {
		
		require_once CLASSES_DIR.'Rmail/Rmail.php';
		
		$html = '<html><body>';
		$html .= post('file_message');
		$html .= '</body></html>';
		
		$mail = new Rmail();
		$mail->setFrom(SERVER_MAILER);
		$mail->setSubject(post('file_subject'));
		$mail->setPriority('high');
		$mail->setHTML($html);

		$users = array();
		$sql = "SELECT * FROM users WHERE company_id = '".(int)post("company_id")."'";
		for($u = $db->Execute($sql); !$u->EOF; $u->moveNext() ) {
			$users[] = ($u->fields['user_primary_email']!='') ? $u->fields['user_primary_email'] : ($u->fields['user_secondary_email']!=''?$u->fields['user_secondary_email']:'');
		}
					
		$mail->send($users);
		
	}
		
	
	// OWENS CORNING HACK
	if ( $USE_APPS_TO_CONTENT_TABLE AND ($tempKey = get("key_temp")) ) {
		
		$sql = "INSERT INTO applications_to_content ( app_id,table_index,content_type_id,content_type_name ) VALUES ('$CURRENT_APP_ID','".get($tempKey)."','".post('content_type_id')."','".post('content_type_name')."') ";
		$db->Execute($sql);
		
		
		// Applical hack
		if ( get("pid") == 5 && $table == "users" ) {

			require_once CLASSES_DIR.'Rmail/Rmail.php';

			if ( !($password = post("user_password")) ) {
				$password = rand(0,100).chr(rand(65,117)).chr(rand(65,117)).chr(rand(65,117)).'_'.chr(rand(65,117)).rand(50,100).'_'.RESET_PASSWORD_RANDOM_WORD;	
				$db->Execute("UPDATE users SET user_password = '".$password."', last_updated = NOW() WHERE user_id = '".get($tempKey)."' LIMIT 1");
			}

			$html = '<html><body>';
			$html .= '<h2>User Registration</h2>';
			$html .= 'You requested an account with '.HTTP_SERVER.'.  If you did not make the request, please email the system administrator at: <a href="mailto:'.SYS_ADMIN_EMAIL.'?subject=Unwanted Account">'.SYS_ADMIN_EMAIL.'</a>';
			$html .= '<br /><br /><hr>';
			$html .= 'Your username and password is:<br />';
			$html .= '<strong>Username:</strong>'.$_POST['user_username'].'<br />';
			$html .= '<strong>Password:</strong>'.$password.'<br />';
			$html .= 'Login at <a href="'.HTTP_SERVER.ADMIN_DIR.'">'.HTTP_SERVER.ADMIN_DIR.'</a> with your username and password.';
			$html .= '</body></html>';

			$mail = new Rmail();
			$mail->setFrom(SERVER_MAILER);
			$mail->setSubject(SITE_NAME.': User Registration');
			$mail->setPriority('high');
			$mail->setHTML($html);
			
			$mail->send(array($_POST['user_primary_email']));

		}/* else if ( $table == "company_files" ) {
			
			require_once CLASSES_DIR.'Rmail/Rmail.php';
			
			$html = '<html><body>';
			$html .= post('file_message');
			$html .= '</body></html>';
			
			$mail = new Rmail();
			$mail->setFrom(SERVER_MAILER);
			$mail->setSubject(post('file_subject'));
			$mail->setPriority('high');
			$mail->setHTML($html);

			$users = array();
			$sql = "SELECT * FROM users WHERE company_id = '".(int)post("company_id")."'";
			for($u = $db->Execute($sql); !$u->EOF; $u->moveNext() ) {
				$users[] = ($u->fields['user_primary_email']!='') ? $u->fields['user_primary_email'] : ($u->fields['user_secondary_email']!=''?$u->fields['user_secondary_email']:'');
			}
						
			$mail->send($users);
			
		}*/
		
	}
	

	
?>