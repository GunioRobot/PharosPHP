<?php
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	save_uploaded_file(	$uploadName, 					// $_FILES[$uploadName]
	//						$dir=UPLOAD_DIR, 				// Where to save
	//						$supported_filetypes=array(), 	// if empty, ignores
	//						$isImage=false )				// To use image class
	//
	//	Saves the uploaded file to the uploads directory and returns the used name
	//
	//	$supported_filetypes can be filename extensions (strings)
	//	--- OR --- if $isImage=true, IMAGETYPE_GIF, IMAGETYPE_JPG, etc 
	//
	// 	USAGE:
	//		try {
	//			$filename = save_uploaded_file("input-name", UPLOAD_DIR, array(".flv"));
	//		} catch (Exception e) {
	//			echo "Error processing the file - ".$e->getMessage();
	//		}
	//
	////////////////////////////////////////////////////////////////////////////////

	function save_uploaded_file($uploadName, $dir=UPLOAD_PATH, $supported_filetypes = array(), $isImage=false, $resize=array('width' => false, 'height' => false)) {
		
		global $db;
		
		if ( isset($_FILES[$uploadName]) && $_FILES[$uploadName]['tmp_name'] ) {
						
			// Want to remove old file is updating existing entry - whether coming from swfupload module or from "file" form_type in profile module
			if ( 	( ($method = request("arg2")) === "save" && ($id = request("arg3")) > 0 && ($key = request("data_key")) && ($table = request("table")) ) ||
			 		( ($key = request("key")) && ($id = request($key)) && ($table = request("table")) ) 	) {
				
				$existing = $db->Execute("SELECT $uploadName FROM $table WHERE $key = '$id' LIMIT 1");
				if ( defined('DELETE_OLD_WHEN_UPLOADING_NEW') && DELETE_OLD_WHEN_UPLOADING_NEW === true ) {
					if ( $existing->fields[$uploadName] != '' ) @unlink($dir.$existing->fields[$uploadName]);
				} else {
					if ( $existing->fields[$uploadName] != '' ) remove_file($existing->fields[$uploadName]);	// Places in "trashed_files" table
				}
				
			}
						
			// If it's just a file, do extension checking
			if ( !$isImage ) {
							
				if ( ($locationOfDot = strrpos($_FILES[$uploadName]['name'], '.')) === false ) {
					throw new Exception("Unknown filetype - extension not found!");
				} else {
					$extension = strtolower(substr($_FILES[$uploadName]['name'], $locationOfDot + 1));
				}
				
				// If checking for filetypes	
				if ( count($supported_filetypes) > 0 ) {
				
					// If not the right file type, display error page
					if ( !in_array($extension,$supported_filetypes) ) {
						throw new Exception("Unsupported filetype of ($extension)!");
					}
				}
				
			} else { // Since it's an image, do image type checking
				
				if ( !class_exists("Image") ) {
					require_once CLASSES_PATH.'Image.php';
				}
				
				try {
					
					if ( $resize['width'] && $resize['height'] ) {
						$image = new Image($_FILES[$uploadName]['tmp_name'], $resize['width'], $resize['height']);
					} else $image = new Image($_FILES[$uploadName]['tmp_name']);
					
					$image->save_img($_FILES[$uploadName]['tmp_name']);
							
				} catch ( Exception $e ) {
					unset($image);	// Cleanup
					throw new Exception($e->getMessage());
				}
				
			}


			$item_name = String::clean_filename($_FILES[$uploadName]['name']);
			$location = $dir.'/'.$item_name;
						
			// If already exists, create a new destination
			if( file_exists($location) ) {
				
				$old = $item_name;
				
				$locationOfDot = strrpos($_FILES[$uploadName]['name'], '.');
				$item_name = substr($_FILES[$uploadName]['name'], 0, $locationOfDot);
				$extension = substr($_FILES[$uploadName]['name'], $locationOfDot + 1);
				
				$item_name = String::clean_filename($item_name);
				$item_name = $item_name.'_'.rand(0,99).'.'.$extension;
				
				$location = $dir.$item_name;
			}
				
			// Save the thumbnail (size previously set) in final destination (original hasn't been moved)
			move_uploaded_file($_FILES[$uploadName]['tmp_name'], $dir.$item_name);
			
			return $item_name;
		} else throw new Exception('$_FILES['.$uploadName.'] was not set.');
		
	}
