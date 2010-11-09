<?
	
	/**
	 * Object
	 * The base class for most system classes in PharosPHP
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
		
		
		/**
		 * __construct
		 *
		 * @return void
		 * @author Matt Brewer
		 **/

		public function __construct() {
			
			global $db;
			$this->db =& $db;
			
		}
		
	}

?>