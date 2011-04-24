<?

	/**
	 * @file actions.php
	 * @brief Functions for working with the actions API for tracking information
	 */

	// Register a callback to define several PHP Constants for us from the database
	NotificationCenter::register_callback(NotificationCenter::APPLICATION_CORE_LOADED_NOTIFICATION, "register_actions");
	function register_actions() {
		foreach(get_actions() as $action) {
			$def = strtoupper(str_replace(" ", "_", $action->title) . "_ACTION");
			if ( !defined($def) ) {
				define($def, $action->id);
			}
		}
	}
	
	// HTML dropdown of action chooser
	function action_item_dropdown($active=null) {
		return array_to_dropdown(action_titles(), $active, (object)array(
			"id" => "content_type_dropdown",
			"name" => "action_id"
		));
	}
	
	// Array of action objects, visible & hidden
	function get_actions() {
		global $db;
		static $actions = null;
		if ( is_null($actions) ) {
			for ( $info = $db->Execute("SELECT * FROM `actions` ORDER BY `title` ASC"); !$info->EOF; $info->moveNext() ) {
				$actions[$info->fields['id']] = (object)array("id" => $info->fields['id'], "title" => $info->fields['title'], "visible" => $info->fields['visible'] == "true" ? true : false);
			}
		} return $actions;
	}
	
	// Array of action objects, only visible ones
	function visible_actions() {
		static $actions = null;
		if ( is_null($actions) ) {
			foreach(get_actions() as $action) {
				if ( $action->visible ) {
					$actions[$action->id] = $action;
				}
			}
		} return $actions;
	}
	
	// Array of action titles (visible only)
	function action_titles() {
		static $actions = null;
		if ( is_null($actions) ) {
			foreach(visible_actions() as $action) {
				$actions[$action->id] = $action->title;
			}
		} return $actions;
	}

?>