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
		header('Content-Disposition: attachment; filename='.$filename);
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
	//	create_pdf($html, $filename, $orientation, $paper) 
	//
	//	Takes:
	//		- raw html as string
	//		- filename as string (named for user download)
	//		- orientation as string (portrait or landscape)
	//		- paper as string (legal, letter)
	//
	//	Will prompt the user to download the html -> PDF converted data
	//
	////////////////////////////////////////////////////////////////////////////////		

	function create_pdf($html, $filename, $orientation=null, $paper=null) {
	
		// Check for empty inputs
		$paper = is_null($paper) ? 'letter' : $paper;
		$orientation = is_null($orientation) ? 'potrait' : $orientation;
		$html = ($html == '') ? '<html><body></body></html>' : $html;
	
		// Append pdf extension if not present
		if ( stristr($filename,'.pdf') == '' ) {
			$filename .= '.pdf';
		}
	
		$tmpfile = tempnam("/tmp", "dompdf_".rand());
		file_put_contents($tmpfile, $html); 
		
		$url = "dompdf.php?input_file=" . rawurlencode($tmpfile) . 
		       "&paper=".$paper."&orientation=".$orientation."&output_file=" . rawurlencode($filename);

		header('Location: ' . INCLUDES_SERVER.'modules/dompdf/'.$url);

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