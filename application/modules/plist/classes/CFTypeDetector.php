<?php

 /**
  * CFTypeDetector
  * Interface for converting native PHP data structures to CFPropertyList objects.
  * @author Rodney Rehm <rodney.rehm@medialize.de>
  * @author Christian Kruse <cjk@wwwtech.de>
  * @package plist
  * @subpackage plist.types
  * @example example-create-02.php Using {@link CFTypeDetector}
  * @example example-create-03.php Using {@link CFTypeDetector} with {@link CFDate} and {@link CFData}
  */
class CFTypeDetector {
  
  /**
   * flag stating if all arrays should automatically be converted to CFDictionary
   * @var boolean
   */
  protected $autoDictionary = false;
  
  /**
   * Create new CFTypeDetector
   * @param boolean $autoDicitionary if set to true all arrays will be converted to CFDictionary
   */
  public function __construct($autoDicitionary=false){
    $this->autoDicitionary = $autoDicitionary;
  }
  
  /**
   * Determine if an array is associative or numerical.
   * Numerical Arrays have incrementing index-numbers that don't contain gaps.
   * @param array $value Array to check indexes of
   * @return boolean true if array is associative, false if array has numeric indexes
   */
  protected function isAssociativeArray($value){
    $numericKeys = true;
    $previousKey = null;
    foreach($value as $key => $v) {
      if(!is_numeric($key) || ($previousKey !== null && $previousKey != $key-1)) {
        $numericKeys = false;
        break;
      }

      $previousKey = $key;
    }
    return !$numericKeys;
  }
  
  /**
   * Create CFType-structure by guessing the data-types.
   * {@link CFArray}, {@link CFDictionary}, {@link CFBoolean}, {@link CFNumber} and {@link CFString} can be created, {@link CFDate} and {@link CFData} cannot.
   * <br /><b>Note:</b>Distinguishing between {@link CFArray} and {@link CFDictionary} is done by examining the keys. 
   * Keys must be strictly incrementing integers to evaluate to a {@link CFArray}. 
   * Since PHP does not offer a function to test for associative arrays, 
   * this test causes the input array to be walked twice and thus work rather slow on large collections. 
   * If you work with large arrays and can live with all arrays evaluating to {@link CFDictionary}, 
   * feel free to set the appropriate flag.
   * <br /><b>Note:</b> If $value is an instance of CFType it is simply returned.
   * <br /><b>Note:</b> If $value is neither a CFType, array, numeric, boolean nor string, it is omitted.
   * @param mixed $value Value to convert to CFType
   * @param boolean $autoDictionary if true {@link CFArray}-detection is bypassed and arrays will be returned as {@link CFDictionary}.
   * @return CFType CFType based on guessed type
   * @uses isAssociativeArray() to check if an array only has numeric indexes
   */
  public function toCFType($value){
    switch(true) {
      case $value instanceof CFType:
        return $value;
      break;
      case is_array($value):
        // test if $value is simple or associative array
        if(!$this->autoDictionary) {
          if(!$this->isAssociativeArray($value)) {
            $t = new CFArray();
            foreach($value as $v) $t->add($this->toCFType($v));
            return $t;
          }
        }

        $t = new CFDictionary();
        foreach($value as $k => $v) $t->add($k, $this->toCFType($v));

        return $t;
        break;

	/*
      case is_numeric($value):
        return new CFNumber($value);
        break;
*/
      case is_bool($value):
        return new CFBoolean($value);
        break;

      case is_string($value):
	  default:
        return new CFString($value);
        break;
    }
  }

}

?>