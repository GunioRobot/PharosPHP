<?
	
	define('HOOK_TABLE_ADDITIONS_HOVER_CELL_FORMAT', 'hook_table_additions_hover_cell_format');
	Hooks::define(HOOK_TABLE_ADDITIONS_HOVER_CELL_FORMAT);
	
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
		$value = render_view("table-hover-cell.php", dirname(__FILE__) . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR, compact("line", "hovers"), false);
		return Hooks::execute(HOOK_TABLE_ADDITIONS_HOVER_CELL_FORMAT, compact("value", "line", "hovers"));
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
			enqueue_style(module_url(dirname(__FILE__)) . 'assets' . DIRECTORY_SEPARATOR . 'style.css');
			enqueue_script(module_url(dirname(__FILE__)) . 'assets' . DIRECTORY_SEPARATOR . 'table-cell-hover.js');
		}
	}

?>