<?php

	/**
	 * @file system.php
	 * @brief Functions for common system actions
	 */
	
	/**
	 * load_content_types
	 * Defines constants from the content_types table in the database
	 *
	 * @return void
	 * @author Matt Brewer
	 **/

	function load_content_types() {
		
		global $db;
		
		$sql = "SELECT * FROM content_types ORDER BY type_id DESC";
		for ( $info = $db->Execute($sql); !$info->EOF; $info->moveNext() ) {
			@define(strtoupper(str_replace(" ", "_", $info->fields['type_name'])).'_TYPE_ID', $info->fields['type_id']);
		}
		
	}
	

	/**
	 * select_app
	 * Sets the ID for the current application
	 *
	 * @return void
	 * @author Matt Brewer
	 **/

	function select_app($id) {
				
		global $CURRENT_APP_ID;		
		$CURRENT_APP_ID = $id;
		$_SESSION['app_id'] = $CURRENT_APP_ID;
						
	}
	
?>