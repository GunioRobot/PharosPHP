<?

	$updatedValues = post("updated-dates", "");
	$originalValues = post("original-dates", "");
	
	if ( ($month = post("month")) && ($year = post("year")) ) {
		
		// Two arrays we want to compare
		$updatedValues = explode("::", $updatedValues);
		$originalValues = explode("::", $originalValues);
		
		// Know we are storing everything from the current month
		$toStore = $updatedValues;
		
		// Go through everything that wasn't in our new "current month" array
		// If it was any other month and any other year, keep it.  If it was in our month,
		// the user had deselected it (b/c it would have shown up in $updatedValues if they hadn't),
		// so we should skip over it, essentially removing it by not storing it back in the db
		$differences = array_diff($updatedValues, $originalValues);
		foreach($differences as $diff) {
			if ( substr($diff,0,4) ==  $year && substr($diff, 5, 2) == $month ) {
				continue;
			} else {
				if ( $diff != "" ) $toStore[] = $diff;
			}
		}
						
		// SQL value to store as the "::" separated date field
		$_POST[$data] = ( count($toStore) > 0 && $toStore[0] != "" ) ? implode("::",$toStore) : "";
		
	}
		
?>