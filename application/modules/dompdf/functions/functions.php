<?
	
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
		$orientation = is_null($orientation) ? 'portrait' : $orientation;
		$html = ($html == '') ? '<html><body></body></html>' : $html;
	
		// Append pdf extension if not present
		if ( stristr($filename,'.pdf') == '' ) {
			$filename .= '.pdf';
		}
	
		$tmpfile = tempnam("/tmp", "dompdf_".rand());
		file_put_contents($tmpfile, $html); 
		
		$url = "dompdf.php?input_file=" . rawurlencode($tmpfile) . 
		       "&paper=".$paper."&orientation=".$orientation."&output_file=" . rawurlencode($filename);

		header('Location: ' . MODULES_SERVER.'dompdf/library/'.$url);

	}

?>