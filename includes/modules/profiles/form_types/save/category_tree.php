<?

	$sql = "SELECT * FROM categories";
	for ( $category = $db->Execute($sql); !$category->EOF; $category->moveNext() ) {
		
		$used = ( $_POST['cat-'.$category->fields['category_id']] == 'true' ) ? true : false;
		
		$sql = "SELECT * FROM categories_to_content WHERE content_type_id = '".$_POST['content_type_id']."' AND table_index = '".$_GET[$_GET['key']]."' AND category_id = '".$category->fields['category_id']."' LIMIT 1";
		$set = $db->Execute($sql);
		if ( $set->EOF AND $used ) {
			$sql = "INSERT INTO categories_to_content (category_id,table_index,content_type_id,content_type_name) VALUES ('".$category->fields['category_id']."','".$_GET[$_GET['key']]."','".$_POST['content_type_id']."','".$_POST['content_type_name']."')";
			$db->Execute($sql);
		} else if ( !$set->EOF AND !$used ) {
			$sql = "DELETE FROM categories_to_content WHERE id = '".$set->fields['id']."' LIMIT 1";
			$db->Execute($sql);
		}
	}
	
	// Don't process
	$data = false;
		
?>