<?

	////////////////////////////////////////////////////////////////////////////////
	//
	//	swf_upload_path()
	//
	//	Returns the path to the swf object
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function swf_upload_path() {
		return MODULES_SERVER.'swfupload/swf/swfupload.swf';
	}
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	Just returns the array to use when calling the javascript file
	//
	/////////////////////////////////////////////////////////////////////////////////
	
	function swf_data_array($id, $key, $table, $postName, $storeFileSize=true, $storeFileType=true) {
		return array("visible" => ($id>0), "key" => $key, "id" => $id, "table" => $table, "file_post_name" => $postName, "store_filesize" => ($storeFileSize?"true":"false"), "store_filetype" => ($storeFileType)?"true":"false");
	}

?>