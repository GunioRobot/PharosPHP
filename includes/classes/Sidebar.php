<?

	class Sidebar {
	
	
		function find_parent($nav_id) {
		
			global $db;
			$parent = '';
			$child = '';
			$subchild = '';
				
			$find = $db->Execute('SELECT * FROM admin_nav WHERE id ="'.$nav_id.'"');
		
			// Top level nav
			if ( $find->fields['parent_id'] == 0 ) {
				$parent = $find->fields['id'];
			} 
		
			// Has a parent
			else {
				$child = $find->fields['id'];
				$find = $db->Execute('SELECT * FROM admin_nav WHERE id ="'.$find->fields['parent_id'].'"');
			
				if ( $find->fields['parent_id'] == 0 ) {
					$parent = $find->fields['id'];
				} 
			
				else if ( $find->fields['parent_id'] != 0 ) {
					$subchild = $child;
					$child = $find->fields['id'];
					$parent = $find->fields['parent_id'];
				}
			}
			return array('parent_id' => $parent, 'child_id' => $child, 'subchild_id' => $subchild);
		}
	
	
	
		function show($nav_id) {
		
			global $db;
		
			$id_set = $this->find_parent($nav_id);
			$item ='';
			$first_item = true;
			
			$sql = "SELECT * FROM admin_nav WHERE parent_id = 0 AND display != 'hidden' AND device_type != 'iphone' AND ".SECURITY_LVL." >= min_lvl AND ".SECURITY_LVL." <= max_lvl ORDER BY order_num ASC";		
			for ( $db_nav = $db->Execute($sql); !$db_nav->EOF; $db_nav->moveNext() ) {
						
				// If top level with no link, just show the name as a header
				if ( $db_nav->fields['parent_id'] == 0 AND $db_nav->fields['page'] == '' ) {
			
					if ( $first_item ) {
						$first_item = false;
					} else $item .= '<br /><br />';
				
					$name = substr($db_nav->fields['name'],0,2) == '%%' ? eval(substr($db_nav->fields['name'],2)) : $db_nav->fields['name'];
					$item .= $name.'<br />';	
				}
		
				// Otherwise, show as button
				else {
		
					// If button with no title, give extra padding
					if ( $db_nav->fields['parent_id'] == 0 ) $item .= '<br /><br />';
			
					$name = substr($db_nav->fields['name'],0,2) == '%%' ? eval(substr($db_nav->fields['name'],2)) : $db_nav->fields['name'];
					$item .= '<div class="lNavButton" align="center"><a id="'.$db_nav->fields['id'].'-nav" class="buttons" href="index.php?pid='.$db_nav->fields['id'].'">'.$name.'</a></div>';						
				}
		
				$sql = "SELECT * FROM admin_nav WHERE parent_id = '".$db_nav->fields['id']."' AND display != 'hidden' AND device_type != 'iphone'  AND ".SECURITY_LVL." >= min_lvl AND ".SECURITY_LVL." <= max_lvl ORDER BY order_num ASC";
				for ( $kids = $db->Execute($sql); !$kids->EOF; $kids->moveNext() ) {
							
					if ( $kids->fields['min_lvl'] <= SECURITY_LVL && $kids->fields['max_lvl'] >= SECURITY_LVL ) {
						$name = substr($kids->fields['name'],0,2) == '%%' ? eval(substr($kids->fields['name'],2)) : $kids->fields['name'];
						$item .= '<div class="lNavButton" align="center"><a id="'.$kids->fields['id'].'-nav" class="buttons" href="index.php?pid='.$kids->fields['id'].'">'.$name.'</a></div>';		
					}			
						
				} 

			}
			return $item;
		}
	
	
	
		function info($pid){
		
			global $db;
			
			$sql = "SELECT * FROM admin_nav WHERE id = '$pid' LIMIT 1";		
			for ( $db_nav = $db->Execute($sql); !$db_nav->EOF; $db_nav->moveNext() ) {
			
				$name = substr($db_nav->fields['name'],0,2) == '%%' ? eval(substr($db_nav->fields['name'],2)) : $db_nav->fields['name'];
				$item =  array(
						'parent_id' => $db_nav->fields['parent_id'],
						'id' => $db_nav->fields['id'],
						'name' => $name,
						'pg' =>  html_entity_decode(preg_replace('/<[^>]*>/', '', $db_nav->fields['page']))
						);

			} return $item;
			
		}
	}
	
?>