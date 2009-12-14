<?php
	
	$removeButton = new Image(current_templates('images/icon_removeWT.gif'));
	$removeButton = $removeButton->get_html('', 'remove-button');
	$TYPE = strtolower(PROFILE_TITLE);
		
	$hidden_fields = '';
	$item = '<p>Views currently used by the '.$TYPE.' are in <span style="color:#c43f30;">red</span>.  
			<br />Names in <em>italics</em> are video groupings.
			<br />To remove this '.$TYPE.' from the view, click the &quot;x&quot; button beside the view name.
			<br />To assign this '.$TYPE.' to a view, click the view name.</p>
	<div id="treecontrol">
		<a title="Collapse the entire tree below" href="#"> Collapse All</a> | 
		<a title="Expand the entire tree below" href="#"> Expand All</a> | 
		<a title="Toggle the tree below, opening closed branches, closing open branches" href="#">Toggle All</a>
	</div>
		<ul id="categories" class="treeview-gray">';
		
		
	
	// Find all the categories associated with the current content type, if this isn't a creation profile action
	if ( isset($_GET['key']) AND $_GET[$_GET['key']] != '' ) {
		$sql = "SELECT category_id FROM categories_to_content WHERE content_type_name = '".$TYPE."' AND table_index = '".$_GET[$_GET['key']]."'";
		$used_cats = array();
		for ( $connected_cat = $db->Execute($sql); !$connected_cat->EOF; $connected_cat->moveNext() ) {
			$used_cats[] = $connected_cat->fields['category_id'];	// Just store the id
		}
	}
		
				
	// Create the overall tree structure of categories from the system.  Will just style the ones that are being used differently	
	$sql = "SELECT * FROM categories WHERE parent_category = '0' ORDER BY order_num ASC";
	for ( $cat = $db->Execute($sql); !$cat->EOF; $cat->moveNext() ) {
	
		$videoGrouping = ( $cat->fields['template_name'] == 'video' OR $cat->fields['template_name'] == 'video_group' ) ? true : false;
		$title = $videoGrouping ? '<em>'.format_title($cat->fields['category_name']).'</em>' : format_title($cat->fields['category_name']);
	
		// Format the title differently and throw in a hidden field if this is a used category
		if ( in_array($cat->fields['category_id'], $used_cats) ) {
			
			$item .= '<li><a class="used" id="cat-'.$cat->fields['category_id'].'-add">'.$title.'</a>';
			$item .= '<a href="#" id="cat-'.$cat->fields['category_id'].'-button" onClick="removeCategory(this);return false;" title="Remove Category">'.$removeButton.'</a>';
			$hidden_fields .= '<input type="hidden" name="cat-'.$cat->fields['category_id'].'" id="cat-'.$cat->fields['category_id'].'" value="true"/>';
			
		} else {
			
			$item .= '<li><a class="cat-add" title="Add this category" id="cat-'.$cat->fields['category_id'].'-add">'.$title.'</span>';
			$item .= '<a href="#" id="cat-'.$cat->fields['category_id'].'-button" onClick="removeCategory(this);return false;" title="Remove Category" style="display:none;">'.$removeButton.'</a>';
			$hidden_fields .= '<input type="hidden" name="cat-'.$cat->fields['category_id'].'" id="cat-'.$cat->fields['category_id'].'" value="false"/>';
			
		}
		
		$sql = "SELECT * FROM categories WHERE parent_category = '".$cat->fields['category_id']."' ORDER BY order_num ASC";
		$f_kid = $db->Execute($sql);
		
		$f_HAS_KIDS = ( !$f_kid->EOF ) ? true : false;
		
		if ( $f_HAS_KIDS ) $item .= '<ul>';
		while ( !$f_kid->EOF ) {
			
			$videoGrouping = ( $f_kid->fields['template_name'] == 'video' OR $f_kid->fields['template_name'] == 'video_group' ) ? true : false;
			$title = $videoGrouping ? '<em>'.format_title($f_kid->fields['category_name']).'</em>' : format_title($f_kid->fields['category_name']);
			
			// Format the title differently and throw in a hidden field if this is a used category
			if ( in_array($f_kid->fields['category_id'], $used_cats) ) {

				$item .= '<li><a class="used" id="cat-'.$f_kid->fields['category_id'].'-add">'.$title.'</a>';
				$item .= '<a href="#" id="cat-'.$f_kid->fields['category_id'].'-button" onClick="removeCategory(this);return false;" title="Remove Category">'.$removeButton.'</a>';
				$hidden_fields .= '<input type="hidden" name="cat-'.$f_kid->fields['category_id'].'" id="cat-'.$f_kid->fields['category_id'].'" value="true"/>';

			} else {

				$item .= '<li><a class="cat-add" title="Add this category" id="cat-'.$f_kid->fields['category_id'].'-add">'.$title.'</a>';
				$item .= '<a href="#" id="cat-'.$f_kid->fields['category_id'].'-button" onClick="removeCategory(this);return false;" title="Remove Category" style="display:none;">'.$removeButton.'</a>';
				$hidden_fields .= '<input type="hidden" name="cat-'.$f_kid->fields['category_id'].'" id="cat-'.$f_kid->fields['category_id'].'" value="false"/>';

			}			
			
			$sql = "SELECT * FROM categories WHERE parent_category = '".$f_kid->fields['category_id']."' ORDER BY order_num ASC";
			$s_kid = $db->Execute($sql);
			
			$s_HAS_KIDS = ( !$s_kid->EOF ) ? true : false;
			
			if ( $s_HAS_KIDS ) $item .= '<ul>';
			while ( !$s_kid->EOF ) {
				
				$videoGrouping = ( $s_kid->fields['template_name'] == 'video' OR $s_kid->fields['template_name'] == 'video_group' ) ? true : false;
				$title = $videoGrouping ? '<em>'.format_title($s_kid->fields['category_name']).'</em>' : format_title($s_kid->fields['category_name']);
				
				
				// Format the title differently and throw in a hidden field if this is a used category
				if ( in_array($s_kid->fields['category_id'], $used_cats) ) {

					$item .= '<li><a class="used" id="cat-'.$s_kid->fields['category_id'].'-add">'.$title.'</a>';
					$item .= '<a href="#" id="cat-'.$s_kid->fields['category_id'].'-button" onClick="removeCategory(this);return false;" title="Remove Category">'.$removeButton.'</a>';
					$hidden_fields .= '<input type="hidden" name="cat-'.$s_kid->fields['category_id'].'" id="cat-'.$s_kid->fields['category_id'].'" value="true"/>';

				} else {

					$item .= '<li><a class="cat-add" title="Add this category" id="cat-'.$s_kid->fields['category_id'].'-add">'.$title.'</a>';
					$item .= '<a href="#" id="cat-'.$s_kid->fields['category_id'].'-button" onClick="removeCategory(this);return false;" title="Remove Category" style="display:none;">'.$removeButton.'</a>';
					$hidden_fields .= '<input type="hidden" name="cat-'.$s_kid->fields['category_id'].'" id="cat-'.$s_kid->fields['category_id'].'" value="false"/>';

				}
								
				$s_kid->moveNext();
			} if ( $s_HAS_KIDS ) $item .= '</ul>';
			$item .= '</li>';
			
			$f_kid->moveNext();
			
		} if ( $f_HAS_KIDS ) $item .= '</ul>'; 
		$item .= '</li>';
		
	} $item .= '</ul>';

	// Last bit of cleanup
	$item = '<div id="associated-categories">'.$hidden_fields.'</div>'.$item;

?>