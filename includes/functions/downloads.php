<?

	////////////////////////////////////////////////////////////////////////////////
	//
	//	csv_download($filename, $content)
	//
	//	Pushes the content as a CSV download through the broswer (with $filename)
	//
	////////////////////////////////////////////////////////////////////////////////

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
		ob_clean();
		flush();
		readfile($tmpfile);
	
	}
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	force_download($filename)
	//
	//	Pushes the content as a generic download through the broswer (with $filename)
	//
	////////////////////////////////////////////////////////////////////////////////

	function force_download($filename) {
			
		if ( $filename && file_exists($filename) ) {
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Content-Disposition: attachment; filename="'.basename($filename).'"');
			header("Content-Type: application/octet-stream");
			header('Content-Description: File Transfer');
			header('Content-Transfer-Encoding: binary');
			header('Content-Length: '.filesize($filename));
			header('Pragma: public');
		    header('Expires: 0');
			
			$fp = fopen($filename, 'rb');
			fpassthru($fp);
		}
	
	}
	
	
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	csv_data($string)
	//
	//	Returns sanitized string for use as data in a CSV cell
	//	Cell can correctly contain quotes, commas, etc
	//
	//	ie, 'Something, I really "really" like' becomes
	//		'"Something, I really ""really"" like"'
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function csv_data($string) {
		return '"'.str_replace('"', '""', stripslashes($string)).'"';
	}

	
	
?>