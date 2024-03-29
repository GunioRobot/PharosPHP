<?

	/**
	 * @file collections.php
	 * @brief Functions for retrieving common collections
	 */

	/**
	 * states_array
	 * Array of uses states
	 * 
	 * @return array $states
	 * @author Matt Brewer
	 **/
	function states_array() {
	
		static $states = array();
	
		$states['Alabama'] = 'AL';
		$states['Alaska'] = 'AK';
		$states['Arizona'] = 'AZ';
		$states['Arkansas'] = 'AR';
		$states['California'] = 'CA';
		$states['Colorado'] = 'CO';
		$states['Connecticut'] = 'CT';
		$states['Delaware'] = 'DE';
		$states['Florida'] = 'FL';
		$states['Georgia'] = 'GA';
		$states['Hawaii'] = 'HI';
		$states['Idaho'] = 'ID';
		$states['Illinois'] = 'IL';
		$states['Indiana'] = 'IN';
		$states['Iowa'] = 'IA';
		$states['Kansas'] = 'KS';
		$states['Kentucky'] = 'KY';
		$states['Louisiana'] = 'LA';
		$states['Maine'] = 'ME';
		$states['Maryland'] = 'MD';
		$states['Massachusetts'] = 'MA';
		$states['Michigan'] = 'MI';
		$states['Minnesota'] = 'MN';
		$states['Mississippi'] = 'MS';
		$states['Missouri'] = 'MO';
		$states['Montana'] = 'MT';
		$states['Nebraska'] = 'NE';
		$states['Nevada'] = 'NV';
		$states['New Hampshire'] = 'NH';
		$states['New Jersey'] = 'NJ';
		$states['New Mexico'] = 'NM';
		$states['New York'] = 'NY';
		$states['North Carolina'] = 'NC';
		$states['North Dakota'] = 'ND';
		$states['Ohio'] = 'OH';
		$states['Oklahoma'] = 'OK';
		$states['Oregon'] = 'OR';
		$states['Pennsylvania'] = 'PA';
		$states['Rhode Island'] = 'RI';
		$states['South Carolina'] = 'SC';
		$states['South Dakota'] = 'SD';
		$states['Tennessee'] = 'TN';
		$states['Texas'] = 'TX';
		$states['Utah'] = 'UT';
		$states['Vermont'] = 'VT';
		$states['Virginia'] = 'VA';
		$states['Washington'] = 'WA';
		$states['West Virginia'] = 'WV';
		$states['Wisconsin'] = 'WI';
		$states['Wyoming'] = 'WY';
		
		return $states;
	}
	

	/**
	 * user_levels_array
	 * Returns array of user levels based on security level given
	 *
	 * @param int $max_level
	 *
	 * @uses Database
	 *
	 * @return array $levels
	 * @author Matt Brewer
	 **/
	function user_levels_array($maxLevel) {
		
		global $db;
		$ret = array();
		
		$sql = "SELECT * FROM user_levels WHERE user_level_id <= '$maxLevel' ORDER BY user_level_id ASC";
		for ( $levels = $db->Execute($sql); !$levels->EOF; $levels->moveNext() ) {
			$ret[$levels->fields['user_level_id']] = format_title($levels->fields['user_level_name']);
		} return $ret;
		
	}

?>