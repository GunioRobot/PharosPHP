<?

	/**
	 * csv_download
	 * Pushes the content as a CSV download through the browser
	 *
	 * @param string $filename
	 * @param string $content
	 *
	 * @return void
	 * @author Matt Brewer
	 **/
	function csv_download($filename, $content) {
	
		// Create a temp file from the string
		$tmpfile = tempnam("/tmp", "random_file_");
		file_put_contents($tmpfile, $content);
		
		// Make sure we have the extension there
		if ( strrpos($filename, ".csv") === false ) {
			$filename .= ".csv";
		}

		// Push headers to start the download
	    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		header('Content-Type: application/octet-stream');
		header('Content-Description: File Transfer');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: '.filesize($tmpfile));
	    header('Pragma: public');
		header('Expires: 0');

		// Push file through to browser
		ob_end_clean();
		readfile($tmpfile);
	
	}
	
	
	/**
	 * force_download
	 * Pushes the content as a generic download through the browser. 
	 * If $content is provided, will send the $content instead of contents of $filename
	 *
	 * @param string $filename
	 * @param (string|null) $content
	 *
	 * @return boolean $success
	 * @author Matt Brewer
	 **/
	function force_download($filename, $content=null) {
		
		if ( !is_null($content) ) {
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Content-Disposition: attachment; filename="'.basename($filename).'"');
			header("Content-Type: application/octet-stream");
			header('Content-Description: File Transfer');
			header('Content-Transfer-Encoding: binary');
			header('Content-Length: '.strlen($content));
			header('Pragma: public');
		    header('Expires: 0');
			echo $content;
			return true;
		} else if ( $filename && file_exists($filename) ) {
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Content-Disposition: attachment; filename="'.basename($filename).'"');
			header("Content-Type: application/octet-stream");
			header('Content-Description: File Transfer');
			header('Content-Transfer-Encoding: binary');
			header('Content-Length: '.filesize($filename));
			header('Pragma: public');
		    header('Expires: 0');
			readfile($filename);
			return true;
		} else return false;
	
	}
	

	/**
	 * csv_data
	 * Sanitizes a string for use as data in a CSV cell
	 *
	 * @param string $content
	 *
	 * @return string $safe_content
	 * @author Matt Brewer
	 **/
	function csv_data($string) {
		return '"'.str_replace('"', '""', stripslashes($string)).'"';
	}

	
	
?>