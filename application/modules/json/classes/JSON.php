<?

	class JSON {
	
		static public function objectFromJSON($name="data") {
			return json_decode(stripslashes(Input::post($name)));
		}
	
	}

?>