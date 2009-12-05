<?

	require_once '../includes/app_header.php';

	if ( ($table = request("table")) && ($key = request("key")) && ($id = request($key)) ) {

		foreach($_FILES as $name => $meta){
		
			try {
				$filename = save_uploaded_file($name);
			} catch (Exception $e) {
				Console::log($e->getMessage());
			}
			
			if ( $filename != '' ) {
			
				$filesize = !request("store_filesize") ? "" : ", $name"."_file_size = '".filesize($filename)."' ";			
				$sql = "UPDATE $table SET $name = '".$db->prepare_input($filename)."' $filesize, last_updated = NOW() WHERE $key = '".(int)$id."' LIMIT 1";
				$db->Execute($sql);
			
				// PROJECT SPECIFIC, EMAIL SYSTEM ADMIN FOR NOTIFICATION
				$info = $db->Execute("SELECT company_name FROM companies WHERE company_id = '".post('company_id')."' LIMIT 1");
				$companyName = htmlentities(format_title($info->fields['company_name']));

				$html = '<html><body>';
				$html .= '<h3>New File Posted for Company: &quot;'.$companyName.'&quot;</h3>';
				$html .= '<p>'.request('username').' just uploaded &quot;'.$filename.'&quot; on '.date('F jS, Y \a\t g:i A').'.</p>';
				$html .= '<p>Download the '.format_filesize(filesize($filename)).' file now: <a href="'.download_link_href($filename).'">Click Here to Download</a>.</p>';
				$html .= '</body></html>';

				require_once CLASSES_DIR.'Rmail/Rmail.php';

				$mail = new Rmail();
				$mail->setFrom(SERVER_MAILER);
				$mail->setSubject("New File Posted on: ".SITE_NAME);
				$mail->setPriority('high');
				$mail->setHTML($html);

				$users = array();
				$sql = "SELECT * FROM users WHERE company_id = '".(int)request("company_id")."'";
				for($u = $db->Execute($sql); !$u->EOF; $u->moveNext() ) {
					$users[] = ($u->fields['user_primary_email']!='') ? $u->fields['user_primary_email'] : ($u->fields['user_secondary_email']!=''?$u->fields['user_secondary_email']:'');
				}

				$mail->send($users);
			
				echo download_link_href($filename);
				
			} else echo 'false';
		
		}
		
	} else echo 'false';

	
?>