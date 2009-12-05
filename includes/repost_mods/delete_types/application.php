<?

	$key = "app_id";

	if ( ($id = get($key)) ) {
		if ( get("valid") == 'true' ) {

			// Delete the tracking info for this app
			$sql = "DELETE FROM tracking WHERE app_id = '$id'";
			$db->Execute($sql);
			
			// Delete the application to content links
			$sql = "DELETE FROM applications_to_content WHERE app_id = '$id'";
			$db->Execute($sql);

			// Delete category to application links
			$sql = "DELETE FROM applications_to_contacts WHERE app_id = '$id'";
			$db->Execute($sql);

			// Delete the application itself
			$sql = "DELETE FROM applications WHERE app_id = '$id' LIMIT 1";
			$db->Execute($sql);
			
			// Select the default app for safety
			select_app(DEFAULT_APP_ID);

		}


		else {

			// Build get vars
			$get = '';
			foreach($_GET as $key => $g) {
				$get .= '&'.$key.'='.$g;
			}

			echo "[link]".HTTP_SERVER.ADMIN_DIR."repost.php?valid=true".$get."[/link]";
			echo "This will remove the application and all it's tracking information from the system.<br /><br />";
			echo "<strong>This action cannot be undone.</strong><br />";
			exit;

		}

	} else die("Need $key!");
	
?>