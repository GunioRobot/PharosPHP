<?

	///////////////////////////////////////////////////////////////////////////
	//
	//	Profile.php
	//
	//	Will grab the corresponding caller's 
	//	template file and replace the information
	//
	///////////////////////////////////////////////////////////////////////////
	

	class Profile {
		
		private $fields;
		private $db;
				
		public function __construct($field_array=array()) {
			
			global $db;
			$this->fields = is_array($field_array) ? $field_array : array();
			$this->db =& $db;
			
		}

		public function display($key=false, $id=false, $repost=false) {
						
			$template = get_template(CURRENT_HTML_FILE);
					
			if ( $key && $id ) {
								
				$profile = $this->db->Execute("SELECT * FROM ".PROFILE_TABLE." WHERE $key = '$id' LIMIT 1");

				$template = str_replace('{table title}','Edit '.PROFILE_TITLE, $template);
				$template = str_replace('{button_text}','Save Changes', $template);
				
			} else {
				
				$template = str_replace('{table title}','Create '.PROFILE_TITLE, $template);
				$template = str_replace('{button_text}','Create '.PROFILE_TITLE, $template);
				
			}
			
			$field_list = '';
			foreach($this->fields as $f) {
				
				$tag = $f['name'];
				
				if ( substr($tag, 0, 1) == "{" ) {
					$tag = substr($tag, 1, -1);
					$link = true;
				} else $link = false;
				
				// Only want to add to the field list under the following conditions... (would be parsed by repost_mod and used to update db otherwise)
				if ( (!isset($f['visible']) OR $f['visible'] == 'true') AND !$link AND $f['type'] != 'display' ) {
					
					// Now to check to make sure the tag is even in the template
					$found = ( $link ) ? strpos($template, "{".$tag."}") : strpos($template, "[".$tag."]");
					if ( $found !== false ) {
						$field_list .= "[$tag][".$f['type']."][".$f['max']."][".$f['varx']."],";
					}
					
				}
				
				$value = $profile->fields[$tag];
				$form_data = choose_form_type($f, $id, $value, $i, $link_value);
								
				$template = ( $link ) ? str_replace("{".$tag."}", $form_data, $template) : str_replace("[".$tag."]", $form_data, $template);
				
			} $field_list = substr($field_list, 0, -1);	// Take off the last trailing comma	
		 
			$template = str_replace('{field_list}',$field_list, $template);
			$template = str_replace('{table}', PROFILE_TABLE, $template);
		
			$alert = ( $repost ) ? '<div id="alert">'.PROFILE_TITLE.' has been updated</div>' : '';

			return $alert.$template;
		}
		
	}
	
?>
