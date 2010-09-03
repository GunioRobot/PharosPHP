<?

	/**
	 * String
	 *
	 * Provides a convenient and consistent wrapper to the scalar string type & functions
	 *
	 * USAGE:
	 *		$str = new String("Hello World, I love %s!");
	 * 		echo $str->format("Pharos PHP");						// Prints "Hello World, I love Pharos PHP!"
	 * 		echo $str->value;										// Prints "Hello World, I love %s!"
	 *		echo $str->length;										// Prints "23"
	 *		$str->length = 4;										// Throw RuntimeException, protected read-only property
	 *		$str->value = "New Phrase";								// No Exception, valid
	 * 		echo $str;												// Prints "New Phrase"
	 *		$str->contains("phrase", String::CASE_INSENSITIVE);		// Returns true
	 *		echo $str . " Here";									// Prints "New Phrase Here"
	 *		echo substr($str, 1);									// Prints "ew Phrase" - notice that the String object has toll-free bridging with native scalar string type
	 *
	 *
	 * @package PharosPHP.Core.Classes
	 * @author Matt Brewer
	 **/

	class String {
		
		const CASE_SENSITIVE 	= 0x00001;	// 1
		const CASE_INSENSITIVE 	= 0x00010;	// 2
		const DIRECTION_LEFT 	= 0x00100;	// 4
		const DIRECTION_RIGHT 	= 0x01000;	// 8
		const DIRECTION_BOTH 	= 0x10000; 	// 16
		
		const HASH_MD5 = 1;
		const HASH_SHA1 = 2;
		const HASH_BASE_64_ENCODE = 3;
		const HASH_BASE_64_DECODE = 4;
		
		protected $value = "";
		protected $length = 0;
		
				
		public function __construct($value="") {
			$this->setVal($value);
		}
		
		
		public function __get($key) {
			if ( $key == "value" ) return $this->value;
			else if ( $key == "length") return $this->length;
		}
		
		public function __set($key, $value) {
			if ( $key == "value" ) $this->setVal($value);
			if ( $key == "length" ) throw new RuntimeException("Cannot assign value to read-only property length");
		}
		
		
		public function __toString() {
			return $this->value;
		}
		
		
		/**
		 * clean_filename
		 *
		 * @param (string|String) $string_to_clean 
		 *
		 * @return String
		 * @author Matt Brewer
		 **/

		public static function clean_filename($str) {
			if ( $str !instanceof String ) {
				$str = new String($str);
			} return $str->sanitize_filename();
		}
		
		
		/**
		 * charAt
		 *
		 * @param int $index
		 *
		 * @throws OutOfBoundsException
		 *
		 * @return char
		 * @author Matt Brewer
		 **/

		public function charAt($index) {
			
			if ( $index >= $this->length || $index < 0 ) {
				throw new OutOfBoundsException();
			}
			
			return $this->value[$index];
			
		}	
		
		
		/**
		 * contains
		 *
		 * @param (string|String) $search
		 * @param int $flag (CASE_SENSITIVE|CASE_INSENSITIVE)
		 *
		 * @return boolean
		 * @author Matt Brewer
		 **/

		public function contains($str, $flag=self::CASE_SENSITIVE) {
			
			if ( $flag == self::CASE_SENSITIVE ) {
				return strpos($this->value, $str) !== false;
			} else return stripos($this->value, $str) !== false;
		}
		
		
		/**
		 * distance
		 *
		 * @param (string|String) $from
		 *
		 * @return int $distance
		 * @author Matt Brewer
		 **/

		public function distance($from) {
			
			if ( is_array($from) ) {
				
				$ret = array_values($from);
				foreach($from as $str) {
					$ret[$str] = levenshtein($this->value, $str);
				}
				return $ret;
				
			} else return levenshtein($this->value, $from);
			
		}
		
		
		/**
		 * format
		 * sprintf() wrapper
		 *
		 * @param var_args $list....
		 *
		 * @return String $result
		 * @author Matt Brewer
		 **/

		public function format() {			
			$params = func_get_args();
			return $this->vformat($params);
		}
		
		
		/**
		 * hash
		 *
		 * @param $flag (HASH_BASE_64_ENCODE|HASH_BASE_64_DECODE|HASH_SHA1|HASH_MD5)
		 *
		 * @return String $string_object
		 * @author Matt Brewer
		 **/

		public function hash($flag=self::HASH_MD5) {
			switch($flag) {

				case self::HASH_BASE_64_ENCODE:
				return new String(base64_encode($this->value));
				break;

				case self::HASH_BASE_64_DECODE:
				return new String(base64_decode($this->value));
				break;

				case self::HASH_SHA1:
				return new String(sha1($this->value));
				break;				

				case self::HASH_MD5:
				default:
				return new String(md5($this->value));
				break;
			}
		}
		
		
		/**
		 * lowercase
		 *
		 * @return String $lowercase
		 * @author Matt Brewer
		 **/

		public function lowercase() {
			return new String(strtolower($this->value));
		}


		/**
		 * matches
		 *
		 * @param (string|String) $pattern to match
		 * @param array $matches will be filled with the results of preg_match_all()
		 *
		 * @return (false|int) number of matches, or false on error
		 * @author Matt Brewer
		 **/

		public function matches($pattern, &$matches) {
			return preg_match_all($pattern, $this->value, $matches);
		}
		
		
		/**
		 * parse
		 *
		 * @return array $parsed_values
		 * @author Matt Brewer
		 **/

		public function parse() {
			$arr = array();
			parse_str($this->value, &$arr);
			return $arr;
		}
		
		
		/**
		 * pos
		 *
		 * @param (string|String) $search
		 * @param int $offset=0
		 * @param int $flags=CASE_SENSITIVE|DIRECTION_LEFT
		 *
		 * @return (false|int) position of $search if found, false if not
		 * @author Matt Brewer
		 **/

		public function pos($str, $offset=0, $flags=null) {
			
			if ( is_null($flags) ) {
				$flags = self::CASE_SENSITIVE | self::DIRECTION_LEFT;
			}
			
			if ( $flags & self::CASE_SENSITIVE ) {
				
				
				if ( $flags & self::DIRECTION_LEFT ) {
					return strpos($this->value, $str, $offset);
				} else return strrpos($this->value, $str, $offset);
				
			} else {
				
				if ( $flags & self::DIRECTION_LEFT ) {
					return stripos($this->value, $str, $offset);
				} else return strripos($this->value, $str, $offset);
				
			}
		
		}
		
		
		/**
		 * regex
		 * 
		 * @param (string|String) $pattern
		 * @param (string|String) $value
		 * @param (string|function) $callback
		 *
		 * @return (false|String) String object result, or false on error
		 * @author Matt Brewer
		 **/

		public function regex($pattern, $value, $callback=null) {
			
			if ( !is_null($callback) ) {
				$val = preg_replace_callback($pattern, $callback, $this->value);
				return ( is_null($val) ) ? false : new String($val);
			} else {
				$val = preg_replace($pattern, $value, $this->value);
				return $val !== false ? new String($val) : false;
			}
		}
		
		
		/**
		 * replace
		 *
		 * @param (string|String) $search
		 * @param (string|String) $value
		 * @param int $flag (CASE_SENSITIVE|CASE_INSENSITIVE)
		 *
		 * @return String $result
		 * @author Matt Brewer
		 **/

		public function replace($search, $value, $flag=self::CASE_SENSITIVE) {
			if ( $flag == self::CASE_SENSITIVE ) {
				return new String(str_replace($search, $value, $this->value));
			} else return new String(str_ireplace($search, $value, $this->value));
		}


		/**
		 * reverse
		 *
		 * @return String
		 * @author Matt Brewer
		 **/

		public function reverse() {
			return new String(strrev($this->value));
		}
		
		
		/**
		 * sanitize
		 *
		 * @return String
		 * @author Matt Brewer
		 **/

		public function sanitize() {
			return new String(addslashes($this->value));
		}
		
		
		/**
		 * sanitize_filename
		 * Sanitizes a string for use as a filename
		 *
		 * @return String
		 * @author Matt Brewer
		 **/

		public function sanitize_filename() {
			return new String(str_replace(' ', '_', preg_replace('/[^[A-Za-z0-9_\s\.-]]*/', '', $this->value)));
		}	
		
		
		/**
		 * setVal
		 *
		 * @param (string|String) $value
		 *
		 * @return void
		 * @author Matt Brewer
		 **/
		public function setVal($val) {
			$this->value = strval($val);
			$this->length = strlen($this->value);
		}
		
		
		/**
		 * split
		 *
		 * @param (string|String) $separator
		 *
		 * @return array $results
		 * @author Matt Brewer
		 **/

		public function split($sep) {
			return explode($sep, $this->value);
		}
		
		
		/**
		 * strstr
		 *
		 * @param (string|String) $search
		 *
		 * @return void
		 * @author Matt Brewer
		 **/
		public function strstr($search) {
			$ret = strstr($this->value, $search);
			return $ret !== false ? new String($ret) : false;
		}
		
		
		/**
		 * substr
		 *
		 * @param int $offset=0
		 * @param int $length=null
		 *
		 * @throws OutOfBoundsException
		 *
		 * @return (false|String) String object on success, false on failure
		 * @author Matt Brewer
		 **/

		public function substr($offset=0, $length=null) {

			if ( $offset < 0 || $offset >= $this->length ) {
				throw new OutOfBoundsException();
			}

			$length = is_null($length) ? $this->length : $length;
			$length = min($length, $this->length);
			$ret = substr($this->value, $offset, $length);
			return $ret !== false ? new String($ret) : false;

		}
		
		
		/**
		 * titleCase
		 *
		 * @return String
		 * @author Matt Brewer
		 **/

		public function titleCase() {
			return new String(ucwords($this->value));
		}
		
		
		/**
		 * trim
		 *
		 * @param (string|String|array) $allowed_characters=DEFAULT trim() behavior
		 *
		 * @link {http://php.net/manual/en/function.trim.php} PHP trim() docs
		 * 
		 * @return void
		 * @author Matt Brewer
		 **/

		public function trim($chars=null, $flag=self::DIRECTION_BOTH) {
			
			if ( is_array($chars) ) $chars = implode("", $chars);

			switch($flag) {

				case self::DIRECTION_LEFT:
				return new String(is_null($chars) ? ltrim($this->value) : ltrim($this->value, $chars));
				break;

				case self::DIRECTION_RIGHT:
				return new String(is_null($chars) ? rtrim($this->value) : rtrim($this->value, $chars));
				break;

				default:
				case self::DIRECTION_BOTH:
				return new String(is_null($chars) ? trim($this->value) : trim($this->value, $chars));
				break;

			}

		}
		
		
		/**
		 * uppercase
		 *
		 * @return String
		 * @author Matt Brewer
		 **/
		
		public function uppercase() {
			return new String(strtoupper($this->value));
		}
		
		
		/**
		 * utf8
		 *
		 * @return String
		 * @author Matt Brewer
		 **/

		public function utf8() {
			return new String(utf8_encode($this->value));
		}
		
		
		/**
		 * vformat
		 *
		 * @param array $params
		 *
		 * @return String
		 * @author Matt Brewer
		 **/

		public function vformat($params) {
			return new String(vsprintf($this->value, $params));
		}
				
	}

?>