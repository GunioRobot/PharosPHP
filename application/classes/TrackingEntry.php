<?

	class TrackingEntry {
		
		public $index, $type, $user, $app, $timestamp, $ip_address;
		
		public function __construct($node) {
			$this->index = $node->getElementsByTagName('table_index')->item(0)->nodeValue;
			$this->type = $node->getElementsByTagName('content_type_id')->item(0)->nodeValue;
			$this->user = $node->getElementsByTagName('user')->item(0)->nodeValue;
			$this->app = $node->getElementsByTagName('app_id')->item(0)->nodeValue;
			$this->timestamp = $node->getElementsByTagName('timestamp')->item(0)->nodeValue;
			$this->ip_address = Input::server("REMOTE_ADDR","");
			
			if ( $this->timestamp == "" ) {
				$this->timestamp = 'NOW()';
			} else $this->timestamp = "'".$this->timestamp."'";
			
		}
		
		public function insert() {
			
			global $db;
			
			$user_id = self::user_reverse_lookup($this->user);
			if ( $user_id > 0 && $this->app > 0 ) {
				$sql = sprintf("INSERT INTO tracking (`content_type_id`,`table_index`,`user_id`,`app_id`,`ip_address`,`timestamp`) VALUES('%d','%d','%d','%d','%s',%s)", $this->type, $this->index, $user_id, $this->app, $this->ip_address, $this->timestamp);
				$db->Execute($sql);
			}
			
		}
		
		public static function user_reverse_lookup($user_identifier) {
			
			global $db;
			static $users = array();
			
			if ( !is_numeric($user_identifier) ) {
				
				if ( !in_array($user_identifer, array_keys($users)) ) {
				
					$sql = sprintf("SELECT * FROM `users` WHERE (`user_username` = '%s' OR `user_primary_email` = '%s') AND `user_level` = '%d' LIMIT 1", $user_identifier, $user_identifier, Settings::get("application.users.levels.basic"));
					$info = $db->Execute($sql);
					$id = !$info->EOF ? $info->fields['user_id'] : 0;
				
					$users[$user_identifier] = $id;
					return $id;
					
				} else return $users[$user_identifer];
				
			} else return $user_identifier;
						
		}
		
		
		public static function sample_xml() {
			
			global $db;
			responseXML(false,"",$dom,$root);
			
			$sql = sprintf("SELECT * FROM content_types ORDER BY type_id");
			for ( $info = $db->Execute($sql); !$info->EOF; $info->moveNext() ) {
				$el = $dom->createElement("content_type");
				$el->setAttribute("id", $info->fields['type_id']);
				$el->appendChild($dom->createCDATASection($info->fields['type_name']));
				$root->appendChild($el);
			}

			for ( $i = 1; $i <= 15; $i++ ) {
				$el = $dom->createElement("track");

				$id = $dom->createElement("table_index");
				$id->appendChild($dom->createCDATASection(rand(1,99)*$i));
				$el->appendChild($id);

				$type = $dom->createElement("content_type_id");
				$type->appendChild($dom->createCDATASection(rand(1,8)));
				$el->appendChild($type);

				$user = $dom->createElement("user");
				$user->appendChild($dom->createCDATASection((rand(0,1)==1?"robert@gigmark.com":"jlanders@dmgx.com")));
				$el->appendChild($user);

				$app = $dom->createElement("app_id");
				$app->appendChild($dom->createCDATASection(1));
				$el->appendChild($app);

				$time = $dom->createElement("timestamp");
				$time->appendChild($dom->createCDATASection(date('Y-m-d H:i:s')));
				$el->appendChild($time);

				$root->appendChild($el);
			}
		
			return $dom->saveXML();
			
		}
		
		
	}
	
?>