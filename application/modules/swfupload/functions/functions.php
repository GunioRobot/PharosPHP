<?

	/**
	 * swf_upload_path
	 * Returns the path to the swf
	 *
	 * @return string $URL
	 * @author Matt Brewer
	 **/

	function swf_upload_path() {
		return module_url(realpath("../")) . 'swfupload/swf/swfupload.swf';
	}
	
	
	/**
	 * swf_data_array
	 * Returns the array to use when calling the javascript file
	 * 
	 * @param int $id
	 * @param string $key
	 * @param string $table_name
	 * @param string $post_name
	 * @param boolean $store_file_size
	 * @param boolean $store_file_type
	 * @param string $file_size_limit
	 * @param boolean $is_image
	 * @param int $width
	 * @param int $height
	 *
	 * @return array $params
	 * @author Matt Brewer
	 **/

	function swf_data_array($id, $key, $table, $postName, $storeFileSize=true, $storeFileType=true, $filesizeLimit="5 MB", $isImage=false, $width=false, $height=false) {
		return array(
			"visible" => ($id>0), 
			"key" => $key, 
			"id" => $id, 
			"table" => $table, 
			"file_post_name" => $postName, 
			"store_filesize" => ($storeFileSize?"true":"false"), 
			"store_filetype" => ($storeFileType)?"true":"false", 
			"filesize_limit" => $filesizeLimit,
			"save_as_image" => $isImage, 
			"resize_image" => (($width!==false&&$height!==false)?"true":"false"), 
			"image_width" => $width, 
			"image_height" => $height
		);
	}

?>