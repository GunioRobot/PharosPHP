<?

	$key = "setting_id";
	$table = "general_settings";

	if ( ($id = get($key)) ) {
		if ( get("valid") == 'true' ) {

			// Get the note info
			$sql = "SELECT * FROM $table WHERE $key = '$id' LIMIT 1";
			$vid = $db->Execute($sql);

			// Delete the tracking info for this video
			$sql = "DELETE FROM tracking WHERE content_type_id = ". SETTING_TYPE_ID ." AND table_index = '$id'";
			$db->Execute($sql);

			// Delete the application to content links
			$sql = "DELETE FROM applications_to_content WHERE content_type_id = ". SETTING_TYPE_ID ." AND table_index = '$id'";
			$db->Execute($sql);

			// Delete the note itself
			$sql = "DELETE FROM $table WHERE $key = '$id' LIMIT 1";
			$db->Execute($sql);

		}

		else {

			// Build get vars
			$get = '';
			foreach($_GET as $key => $g) {
				$get .= '&'.$key.'='.$g;
			}


			echo "[link]".HTTP_SERVER.ADMIN_DIR."repost.php?valid=true".$get."[/link]";
			echo "This setting will be permanently removed.<br /><br />";
			echo "<strong>This action cannot be undone.</strong><br />";
			exit;

		}

	} else die("Need $key!");
	
?>