<?

	/**
	 * Keypath
	 *
	 * 		A keypath is a string for traversing dictionary contents and retrieving
	 *		a value.  An example would be "system.site.name" which performs two 
	 *		dictionary lookups and returns the value of the last string piece
	 *
	 * @package PharosPHP.Core.Classes
	 * @author Matt Brewer
	 **/
	
	class Keypath {
		
		const VALUE_UNDEFINED = null;
		
		protected $components = array();
		protected $path = "";
		
		public function __construct($keypath=null) {
			$this->set($keypath);
		}
		
		
		/**
		 * set
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
				
				if ( ($path = trim($keypath, ". ")) != "" ) {
				
					$components = explode(".", $path);
					if ( empty($components) ) throw new InvalidKeyPathException("Invalid key path ($path)");
					
					$this->components = $components;
					$this->path = $path;
								
				} else throw new InvalidKeyPathException("Invalid key path ($keypath)");
			
			} 
			
		}
		
		
		/**
		 * retrieve
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
			
				$arr = $dictionary[$components[0]][$components[1]];
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
		
		
		public function __toString() {
			return sprintf("Keypath: (%s)", $this->path);
		}
		
	}

?>