<?

	/**
	 * DD_Belated
	 * Class to easily add a PNG fix to the site
	 * 
	 *
	 * @package PharosPHP.Core.Modules
	 * @author Matt Brewer
	 **/

	class DD_Belated {
		
		private static $items = array("img");
		
		private function __construct() {}
		private function __clone() {}


		/**
		 * write
		 * Renders the javascript for the PNGFix where called
		 *
		 * @return void
		 * @author Matt Brewer
		 **/

		public static function write() {
			
			$tags = implode(",", self::$items);
			$src = module_url(__FILE__) . "pngfix.js";
			
			echo <<< JS
			<!--[if IE 6]>
			<script type="text/javascript" src="{$src}"></script>
			<script type="text/javascript">
				DD_belatedPNG.fix('{$tags}');
			</script>
			<![endif]-->
JS;
		}
		
		
		/**
		 * add
		 * Add a selector to be fixed with the PNGfix
		 *
		 * @param string $selector
		 *
		 * @return array $items
		 * @author Matt Brewer
		 **/

		public static function add($str) {
			return self::$items[] = strval($str);
		}
				
	}

?>