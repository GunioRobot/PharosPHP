<?

	$key = "user_id";
			
	if ( ($id = get($key)) ) {
		if ( get("valid") == 'true' ) {

			// Remove user
			$sql = "DELETE FROM users WHERE user_id = '$id' LIMIT 1";
			$db->Execute($sql);
		
			// Delete the app to contact link
			$sql = "DELETE FROM applications_to_content WHERE content_type_id = '".USER_TYPE_ID."' and table_index = '$id' LIMIT 1";
			$db->Execute($sql);
		
			// Remove the tracking information 
			$sql = "DELETE FROM tracking WHERE user_id = '$id'";
			$db->Execute($sql);
		
		}
	
	
		else {
	
			// Build get vars
			$get = '';
			foreach($_GET as $key => $g) {
				$get .= '&'.$key.'='.$g;
			}

			// Change the text a bit if deleting an admin user
			if ( is_admin($id) ) {
				echo "[link]".HTTP_SERVER.ADMIN_DIR."repost.php?valid=true".$get."[/link]";
				echo "The administrator will no longer have access to the CMS area.<br /><br />";
				echo "<strong>This action cannot be undone.</strong><br />";
				exit;
			} else {
				echo "[link]".HTTP_SERVER.ADMIN_DIR."repost.php?valid=true".$get."[/link]";
				echo "Deleting a user removes user behavior/tracking information.<br />";
				echo "The user will no longer be able to login to ANY application.<br /><br />";
				echo "<strong>This action cannot be undone.</strong><br />";
				exit;
			}
		
		}

	} else die("Need $key!");
	
?>