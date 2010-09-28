<?

	class DD_Belated {
		
		private static $items = array("img");
		
		private function __construct() {}
		private function __clone() {}

		public static function write() {
			
			$tags = implode(",", self::$items);
			$src  = Template::site_link(APP_DIR."/modules/dd_belated/pngfix.js");
			
			echo <<< JS
			<!--[if IE 6]>
			<script type="text/javascript" src="{$src}"></script>
			<script type="text/javascript">
				DD_belatedPNG.fix('{$tags}');
			</script>
			<![endif]-->
JS;
		}
		
		public static function add($str) {
			self::$items[] = strval($str);
		}
				
	}

?>