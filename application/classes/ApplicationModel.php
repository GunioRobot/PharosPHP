<?

	if ( version_compare(phpversion(), "5.3.0") >= 0 ) {
		require_once dirname(__FILE__) . DS . "ActiveRecord" . DS . "ApplicationModel.php";
	} else {
		
		/**
		 * ApplicationModel
		 *
		 * Parent model for all of you application models (subclass this)
		 *
		 * @package PharosPHP.Application.Classes
		 * @author Matt Brewer
		 **/
		
		Loader::sharedLoader()->klass("Database/PharosModel");
		class ApplicationModel extends PharosModel {

		}
		
	}

?>