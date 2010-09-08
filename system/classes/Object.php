<?
	
	/**
	 * Object
	 *
	 * @package PharosPHP.Core.Classes
	 * @author Matt Brewer
	 **/

	class Object {
		
		
		/**
		* Connection to the database
		*
		* @var Database
		*/
		protected $db = null;
		
		
		public function __construct() {
			
			global $db;
			$this->db =& $db;
			
		}
		
	}

?>