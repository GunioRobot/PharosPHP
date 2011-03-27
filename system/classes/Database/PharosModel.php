<? 

	/**
	 * PharosModel
	 *
	 * Parent model for all of you application models (subclass this).
	 *
	 * DatabaseColumns with a title of "date_added", "last_updated", "created_at", or "modified_at" are 
	 * automatically updated through insert & save operations performed on the record, as part
	 * of the naming convention.
	 *
	 * @package PharosPHP.System.Classes.Database
	 * @author Matt Brewer
	 **/
	
	Loader::load_class("Database/DatabaseColumn");
	class PharosModel extends Object {

		/**
		 * Array of DatabaseColumn objects representing table schema
		 *
		 * @var array $fields
		 **/
		
		protected $fields = array();
		
		/**
		 * Boolean indicating if the record has been modified in memory
		 *
		 * @var bool $dirty
		 **/
		
		protected $dirty = false;
		
		/**
		 * Boolean indicating if the record has been saved to permanent storage, ever
		 *
		 * @var string
		 **/
		
		protected $saved = false;
		
		/**
		 * The table name
		 *
		 * @var string
		 **/
		
		protected $table = "";
		
		/**
		 * Primary key
		 *
		 * @var string
		 **/
		
		protected $key = "";
		
		/**
		 * __construct
		 * Initializes a Model for a specified database table
		 *
		 * @throws PharosModelInvalidLoadException
		 *
		 * @param string $table
		 * @param string $primary_key
		 * @param mixed $options (optional)
		 *
		 * @return void
		 * @author Matt Brewer
		 **/

		public function __construct($table, $key, $opt=null) {
			
			parent::__construct();
			$this->table = $table;
			$this->key = $key;
			
			if ( is_array($opt) ) {		// Array of DatabaseColumn objects, or just of raw record data
				
				foreach(DatabaseColumn::columns($table) as $name => $value) {
					
					// Skip the primary key
					if ( ($lower = strtolower($name)) == strtolower($this->key) ) {
						$opt[$lower] = null;
					}
					
					// Store the DatabaseColumn object (by creating one if necessary) for the column being processed
					if ( ($opt[$lower] instanceof DatabaseColumn) ) {
						$this->fields[$lower] = $opt[$lower];
					} else {
						$this->fields[$lower] = $value;
						$this->fields[$lower]->value = $opt[$lower];
					}
				}
				
			} else if ( intval($opt) > 0 ) {	// Integer value for primary key lookup, performing database read to populate object data
				
				$this->{$this->key} = intval($opt);
				$this->reset();
				
			} else {	// Populate object with empty data, not from storage (null field values)
				
				foreach(DatabaseColumn::columns($table) as $name => $value) {
					$this->fields[strtolower($name)] = $value;
				}
				
			}
			
		}

		/**
		 * __get
		 * Dynamic accessor method, do not call directly
		 *
		 * @param string $key
		 *
		 * @return mixed $value
		 * @author Matt Brewer
		 **/

		public function __get($key) {
			switch($key) {
				
				case "table":
				case "key":
				case "saved":
					return $this->{$key};
					break;
				
				default:
					return $this->fields[$key]->value;
					break;
			}
		}

		/**
		 * __set
		 * Dynamic mutator method, do not call directly
		 *
		 * @param string $key
		 * @param mixed $value
		 *
		 * @throws ReadOnlyPropertyException
		 * 
		 * @return void
		 * @author Matt Brewer
		 **/

		public function __set($key, $value) {
			switch($key) {
				
				case "table":
				case "key":
				case "saved":
					throw new ReadOnlyPropertyException($key);
					break;
					
				default:
					if ( $this->fields[$key]->value != $value ) {
						$this->fields[$key]->value = $value;
						$this->dirty = true;
					} 
					break;
			}
		}
		
		/**
		 * __toString
		 * Returns a string representation of the object
		 *
		 * @return string $representation
		 * @author Matt Brewer
		 **/
		
		public function __toString() {
			return sprintf('<%s saved="%d" dirty="%d" />', $this->table, $this->saved, $this->dirty);
		}
	
		/**
		 * insert
		 * Inserts the record into permanent storage. If the record already existed, does nothing.
		 *
		 * @return void
		 * @author Matt Brewer
		 **/

		public function insert() {
			if ( !$this->saved ) {
				$this->sql($this->_insertSQL());
				$this->saved = true;
				$this->dirty = false;
			}
		}
	
		/**
		 * reset
		 * Reads the record from the database. This will "reset" any modifications in memory to those in storage.
		 *
		 * @throws PharosModelInvalidLoadException
		 *
		 * @return bool $success
		 * @author Matt Brewer
		 **/
	
		public function reset() {
			$info = $this->db->Execute(sprintf("SELECT * FROM `%s` WHERE `%s` = %d LIMIT 1", $this->table, $this->key, $this->{$this->key}));
			if ( !$info->EOF ) {
				$this->saved = true;
				$this->dirty = false;
				$this->fields = array();
				foreach(DatabaseColumn::columns($this->table) as $col => $meta) {
					$lower = strtolower($col);
					$meta->value = $info->fields[$lower];
					$this->fields[$lower] = $meta;
				}
				return true;
			} else return false;
		}
	
		/**
		 * sql
		 * Executes the provided SQL on the database, can be arbitrary SQL
		 *
		 * @param string $sql
		 *
		 * @return mixed $result
		 * @author Matt Brewer
		 **/
	
		public function sql($sql) {
			return $this->db->Execute($sql);
		}
	
		/**
		 * save
		 * Saves the record to database if there are changes to be recorded. If the record has never been saved, performs an insert
		 *
		 * @return void
		 * @author Matt Brewer
		 **/
	
		public function save() {
			if ( $this->dirty ) {
				if ( $this->saved ) {
					$this->sql($this->_updateSQL());
					$this->dirty = false;
				} else {
					$this->insert();
				}
			}
		}
		
		/**
		 * _insertSQL
		 * Returns the SQL for performing an insertion of the object
		 *
		 * @return string $sql
		 * @author Matt Brewer
		 **/
		
		protected function _insertSQL() {
			static $auto_update = array("date_added", "last_updated", "created_at", "modified_at");
			$update = array();
			foreach($this->fields as $key => $info) {
				if ( in_array($key, $auto_update) ) {
					$info->addAttribute(DatabaseColumn::ATTRIBUTE_NOW);
				} $update[] = $info->sql();
			} return 'INSERT INTO `' . $this->table . '` (`' . implode("`,`", array_keys($this->fields)) . '`) VALUES(`' . implode('`,`', $update) . '`)';
		}
	
		/**
		 * _updateSQL
		 * Returns the SQL for performing an update of the object
		 *
		 * @return string $sql
		 * @author Matt Brewer
		 **/
	
		protected function _updateSQL() {
			static $auto_update = array("last_updated", "modified_at");
			$update = array();
			foreach($this->fields as $key => $info) {
				if ( in_array($key, $auto_update) ) {
					$info->addAttribute(DatabaseColumn::ATTRIBUTE_NOW);
				} $update[] = "`$key` = " . $info->sql();
			} return 'UPDATE `' . $this->table . '` SET ' . implode(", ", $update) . ' WHERE `' . $this->key . '` = ' . $this->{$this->key} . ' LIMIT 1';
		}

	}

?>