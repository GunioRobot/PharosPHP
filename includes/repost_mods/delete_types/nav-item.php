<?
	$key = "id";

	if ( ($id = get($key)) ) {
		if ( get("valid") == 'true' ) {

			// Delete the nav item itself
			$sql = "DELETE FROM admin_nav WHERE id = '$id' LIMIT 1";
			$db->Execute($sql);

		}


		else {

			// Build get vars
			$get = '';
			foreach($_GET as $key => $g) {
				$get .= '&'.$key.'='.$g;
			}

			echo "[link]".HTTP_SERVER.ADMIN_DIR."repost.php?valid=true".$get."[/link]";
			echo "Are you sure you want to permanently remove this navigation item from the system?<br /><br />";
			echo "<strong>This action cannot be undone.</strong><br />";
			exit;

		}

	} else die("Need $key!");
	
?>