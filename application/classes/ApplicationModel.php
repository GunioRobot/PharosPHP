<?

	if ( version_compare(phpversion(), "5.3.0") >= 0 ) {

		/**
		 * ApplicationModel
		 *
		 * Parent model for all of you application models (subclass this)
		 *
		 * @package PharosPHP.Application.Classes
		 * @author Matt Brewer
		 **/
		class ApplicationModel extends ActiveRecord\Model {
			
		} 
		
	}

?>