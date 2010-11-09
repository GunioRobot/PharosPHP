<?

	/**
	 * Keypath
	 *
	 * 		A keypath is a string for traversing dictionary contents and retrieving
	 *		a value.  An example would be "application.system.site.name" which performs two 
	 *		dictionary lookups and returns the value of the last string piece
	 *
	 * @package PharosPHP.Core.Classes
	 * @author Matt Brewer
	 **/
	
	final class Keypath extends Object {
		
		const VALUE_UNDEFINED = null;
		
		protected $separator = ".";
		protected $components = array();
		protected $path = "";
		
		
		/**
		 * __construct
		 * Creates a new Keypath object
		 *
		 * @param string $keypath
		 * @param string $separator
		 *
		 * @return Keypath $obj
		 * @author Matt Brewer
		 **/

		public function __construct($keypath=null, $separator=null) {
			
			if ( ($separator = strval($separator)) != "" ) {
				$this->separator = $separator;
			}
			
			$this->set($keypath);
			
		}
		
		
		/**
		 * set
		 * Updates the keypath representation
		 *
		 * @param string $keypath
		 *
		 * @throws InvalidKeyPathException
		 *
		 * @return void
		 * @author Matt Brewer
		 **/

		public function set($keypath) {
		
			if ( !is_null($keypath) ) {
				
				if ( ($path = trim($keypath, $this->separator." ")) != "" ) {
				
					$components = explode($this->separator, $path);
					if ( empty($components) ) throw new InvalidKeyPathException("Invalid key path ($path)");
					
					$this->components = $components;
					$this->path = $path;
								
				} else throw new InvalidKeyPathException("Invalid key path ($keypath)");
			
			} 
			
		}
		
		
		/**
		 * retrieve
		 * Attempts to retrieve the value from the provided $dictionary according to the Keypath representation, 
		 * returning Keypath::VALUE_UNDEFINED if unable to retrieve.
		 *
		 * @param mixed $dictionary
		 * 
		 * @throws InvalidKeyPathException
		 *
		 * @return mixed $value (if found), Keypath::VALUE_UNDEFINED if undefined
		 * @author Matt Brewer
		 **/

		public function retrieve($dictionary) {
			
			if ( empty($this->components) ) throw new InvalidKeyPathException(sprintf("Keypath not initialized (%s)", $this->path));
			
			if ( count($this->components) == 1 ) {
				
				return $dictionary[$this->components[0]];
				
			} else if ( count($this->components) == 2 ) {
				
				return $dictionary[$this->components[0]][$this->components[1]];
				
			} else {
			
				$arr = $dictionary[$this->components[0]][$this->components[1]];
				$components = array_slice($this->components,2);
				
				$count = 0;
				if ( !empty($components) ) {
					
					foreach($components as $c) {
						
						if ( isset($arr[$c]) ) {
							$arr = $arr[$c];
							$count++;
						} else {
							if ( $count == count($components) ) return $arr;
							else return self::VALUE_UNDEFINED;
						}
						
					} return $arr;
					
				} else return $arr;
				
			}
			
		}
		
		
		/**
		 * separator
		 * Change the separator
		 *
		 * @param (string|String) $separator
		 *
		 * @return string $separator
		 * @author Matt Brewer
		 **/

		public function separator($separator=null) {
			if ( ($separator = strval($separator)) != "" ) {
				return $this->separator = $separator;
			} else return $this->separator;
		}
		
		
		/**
		 * components
		 * Retrieve array of the keypath components
		 *
		 * @return array $components
		 * @author Matt Brewer
		 **/

		public function components() {
			return $this->components;
		}
		
		
		/**
		 * length
		 * Retrieve the the number of components in the keypath
		 * 
		 * @return int $length
		 * @author Matt Brewer
		 **/

		public function length() {
			return count($this->components);
		}
		
		
		/**
		 * item
		 * Indexer to retrieve a particular component from the keypath 
		 *
		 * @param int $i
		 * 
		 * @throws OutOfBoundsException
		 *
		 * @return string $keypath_component
		 * @author Matt Brewer
		 **/

		public function item($i) {
			
			if ( $i < 0 || $i >= $this->length() ) {
				throw new OutOfBoundsException(sprintf("Keypath->item(%d) is out of bounds: [%d,%d]", $i, 0, ($this->length()-1)));
			}
			
			return $this->components[$i];
		}
		
		
		/**
		 * __toString
		 * Returns string representation of the object (implicitly called)
		 *
		 * @return string $string_representation
		 * @author Matt Brewer
		 **/

		public function __toString() {
			return sprintf("Keypath: (%s)", $this->path);
		}
		
	}

?>