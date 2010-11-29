<?

	/**
	 * @file table-additions/include.php
	 * @brief Provides several helper functions for creating output for use with TableController
	 */
	
	define('HOOK_TABLE_ADDITIONS_HOVER_CELL_FORMAT', 'hook_table_additions_hover_cell_format');
	NotificationCenter::define(HOOK_TABLE_ADDITIONS_HOVER_CELL_FORMAT);
	
	
	/**
	 * table_hover_cell
	 * Creates a block of HTML for use in the Table class as a cell
	 *
	 * @param string $line - main body content of the cell
	 * @param array $hovers - array of hover objects for creating anchor tags
	 *
	 * @return string $html
	 * @author Matt Brewer
	 **/
	function table_hover_cell($line, array $hovers=array()) {
		register_table_additions_script_and_styles();
		$value = render_view("table-hover-cell.php", dirname(__FILE__) . DS . 'views' . DS, compact("line", "hovers"), false);
		return NotificationCenter::execute(HOOK_TABLE_ADDITIONS_HOVER_CELL_FORMAT, compact("value", "line", "hovers"));
	}
	
	
	/**
	 * table_hover_cell_edit_delete_options
	 * Function to help create the hovers for the standard edit/delete options (for the active controller object)
	 *
	 * @return array $options
	 * @author Matt Brewer
	 **/
	function table_hover_cell_edit_delete_options($id) {
		if ( ($controller = Application::controller()) !== null ) {
			$class = get_class($controller);
			$hovers = array();					
			$hovers[] = (object)array("name" => "Edit", "href" => Template::edit($class, $id), "title" => "Edit this ".$controller->type, "class" => "edit-link");
			$hovers[] = (object)array("name" => "Delete", "href" => Template::delete($class, $id), "title" => "Delete this ".$controller->type, "class" => "delete-link confirm-with-popup");
			return $hovers;
		}
	}
	
	
	/**
	 * register_table_additions_script_and_styles
	 * Registers javascript & css when using this module
	 *
	 * @return void
	 * @author Matt Brewer
	 **/
	function register_table_additions_script_and_styles() {
		static $registered = false;
		if ( !$registered ) {
			enqueue_style(module_url(dirname(__FILE__)) . 'assets' . DS . 'style.css');
			enqueue_script(module_url(dirname(__FILE__)) . 'assets' . DS . 'table-cell-hover.js');
		}
	}

?>