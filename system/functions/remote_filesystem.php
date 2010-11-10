<?

	/**
	 * @file remote_filesystem.php
	 * @brief Functions for working with remote filesytems
	 */

	/**
	 * get_remote_last_modified
	 * Get remote file last modification date (returns unix timestamp)
	 *
	 * @param string $URL
	 *
	 * @return int $timestamp
	 * @author Matt Brewer
	 **/

	function get_remote_last_modified($URL) {
	
	    $unixtime = 0;

	    $fp = @fopen($URL, "r");
	    if( !$fp ) return;

	    $meta = @stream_get_meta_data($fp);
	    foreach($meta['wrapper_data'] as $response) {
	        
			// case: redirection
	        if( substr(strtolower($response), 0, 10) == 'location: ' ) {
	            
				$newUri = substr($response, 10);
	            fclose($fp);
	            return get_remote_last_modified($newUri);
	
	        }
	
	        // case: last-modified
	        else if ( substr(strtolower($response), 0, 15) == 'last-modified: ' ) {
	            $unixtime = strtotime(substr($response, 15));
	            break;
	        }
	
	    }
	
	    fclose($fp);
	    return $unixtime;
	
	}
	
	
	/**
	 * remote_file_exists
	 *
	 * @param string $URL
	 *
	 * @return boolean $exists
	 * @author Matt Brewer
	 **/

	function remote_file_exists($URL) {
		$fp = @fopen($URL, "r");
		$success = $fp !== false ? true : false;
		@fclose($fp);
		return $success;
	}

?>