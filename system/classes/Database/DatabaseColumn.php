<?

	/**
	 * DatabaseColumn
	 * This class represents a column in a MySQL database. A column may have attributes, 
	 * such as autoupdate for a timestamp or that the value is to be non-escaped on insertion.
	 * Additionally, a column has a type, derived from the table schema, as well as a value from the 
	 * table - including whether or not the value has been saved to the table, or modified in memory.
	 *
	 * This class is mainly used as a supporting class for the PharosModel class
	 *
	 * @package PharosPHP.System.Classes.Database
	 * @author Matt Brewer
	 **/
	
	class DatabaseColumn extends Object {
		
		const TYPE_INT = "int";
		const TYPE_VARCHAR = "string";
		const TYPE_BOOL = "bool";
		const TYPE_TEXT = "string";
		const TYPE_DATE = "date";
		const TYPE_DATETIME = "datetime";
		const TYPE_TIMESTAMP = "timestamp";
		const TYPE_ENUM = "enum";
		
		const ATTRIBUTE_NOW = "attribute_now";
		const ATTRIBUTE_ESCAPE = "attribute_escape";
		const ATTRIBUTE_UNESCAPE = "attribute_unescape";
		const ATTRIBUTE_CALLBACK = "attribute_callback";
		
		/**
		 * Represented value
		 *
		 * @var string
		 **/
		
		protected $value = null;
		
		/**
		 * Name of the database table being represented
		 *
		 * @var string
		 **/
		
		protected $table = "";
		
		/**
		 * Column in the database schema
		 *
		 * @var string
		 **/
		
		protected $column = "";
		
		/**
		 * Flag is true if the value has been modified in memory
		 *
		 * @var bool
		 **/
		
		protected $dirty = false;
		
		/**
		 * Array of attributes used when generating SQL
		 *
		 * @var array
		 **/
		
		protected $attributes = array(self::ATTRIBUTE_ESCAPE);
		
		/**
		 * Class variable storing array of tables for caching & minimizing database calls
		 *
		 * @var string
		 **/
		
		static protected $tables = array();
		
		/**
		 * __construct
		 * Initializes a new DatabaseColumn object
		 * 
		 * @param string $table
		 * @param string $column (optional)
		 *
		 * @return void
		 * @author Matt Brewer
		 **/
		
		public function __construct($table, $column="") {
			parent::__construct();
			$this->retrieve($table, $column);
		}
		
		
		/**
		 * sql
		 * Returns the SQL value for this column, applying appropriate attributes
		 *
		 * @throws PharosBaseException
		 *
		 * @return string $sql
		 * @author Matt Brewer
		 **/
		
		public function sql() {
			
			if ( !$this->column ) {
				throw new PharosBaseException("DatabaseColumn requires column to be set before requesting sql()");
			}
			
			if ( array_key_exists(self::ATTRIBUTE_CALLBACK, $this->attributes) && $this->attributes[self::ATTRIBUTE_CALLBACK] ) {
				return call_user_func_array($this->attributes[self::ATTRIBUTE_CALLBACK], array($this));
			}

			$value = array_key_exists(self::ATTRIBUTE_UNESCAPE, $this->attributes) ? $this->value : "'" . $this->db->prepare_input($this->value) . "'";
			switch($this->type) {
			
				case self::TYPE_DATETIME:
				case self::TYPE_TIMESTAMP:
					return array_key_exists(self::ATTRIBUTE_NOW, $this->attributes) ? 'NOW()' : $value;
					break;
					
				case self::TYPE_DATE:
					return array_key_exists(self::ATTRIBUTE_NOW, $this->attributes) ? 'DATE()' : $value;
					break;	
										
				default:
					return $value;
					break;
			}
			
		}
		
		
		/**
		 * addAttribute
		 * Adds the specified attribute, does nothing if the attribute already exists
		 *
		 * @param string $attribute
		 * @param mixed $options (optional)
		 *
		 * @return void
		 * @author Matt Brewer
		 **/
		
		public function addAttribute($att, $opt=null) {
			$this->attributes[$att] = $opt;
		}
		
		
		/**
		 * removeAttribute
		 * Removes the specified attribute, does nothing if the attribute does not exist
		 * 
		 * @param string $attribute
		 *
		 * @return void
		 * @author Matt Brewer
		 **/
		
		public function removeAttribute($att) {
			unset($this->attributes[$att]);
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
			if ( !$this->column ) return false;
			switch($key) {
				
				case "type":
					return self::$tables[$this->table][$this->column]->type;
					break;
				case "length":
					return self::$tables[$this->table][$this->column]->max_length;
					break;

				default:
					return $this->{$key};
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
		 * @throws InvalidArgumentException
		 * @throws ReadOnlyPropertyException
		 *
		 * @return void
		 * @author Matt Brewer
		 **/
		
		public function __set($key, $value) {
			switch($key) {
				
				case "table":
					try {
						$this->retrieve($value, $this->column);
					} catch (InvalidArgumentException $e) {
						$this->column = "";
					}
					break;
					
				case "column":
					if ( !array_key_exists($value, self::$tables[$this->table]) ) {
						throw new InvalidArgumentException(sprintf("column does not exist [%s in %s]", $this->table, $value));
					} else {
						$this->column = $value;
					}
					break;
					
				case "type":
					self::$tables[$this->table][$this->column]->type = $value;
					break;
					
				case "value":
					if ( $this->value != $value ) {
						$this->value = $value;
						$this->dirty = true;
					}
					break;
					
				default:
					throw new ReadOnlyPropertyException();
					break;
					
			}
		}
		
		
		/**
		 * __toString
		 * Returns string representation of the object
		 *
		 * @return string $representation
		 * @author Matt Brewer
		 **/
		
		public function __toString() {
			return sprintf("<%s count=\"%d\" value=\"%s\" />", $this->table, count(self::$tables[$this->table]), $this->value);
		}
		
		
		/**
		 * retrieve
		 * Retrieves information on a specific column
		 * 
		 * @param string $table
		 * @param string $column (optional)
		 *
		 * @return void
		 * @author Matt Brewer
		 **/
		
		protected function retrieve($table, $column="") {
			$this->table = strtolower($table);
			if ( !array_key_exists($table, self::$tables) ) {
				self::$tables[$this->table] = $this->db->metaColumns($this->table);
			}
			$this->column = strtoupper($column);
		}
		
		
		/**
		 * columns
		 * Retrieves column information for a table
		 * 
		 * @param string $table
		 *
		 * @return array $columns
		 * @author Matt Brewer
		 **/
		
		public static function columns($table) {
			global $db;
			$table = strtolower($table);
			if ( !array_key_exists($table, self::$tables) ) {
				self::$tables[$table] = $db->metaColumns($table);
			}
			
			$ret = array();
			foreach(self::$tables[$table] as $key => $value) {
				$ret[strtolower($key)] = new DatabaseColumn($table, $key);
			}
			return $ret;
		}
		
	}

?>