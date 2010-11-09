<?

	/**
	 * RSS
	 *
	 * @package PharosPHP.Core.Classes
	 * @author Matt Brewer
	 **/

	class RSS {
		
		public $title;
		public $description; 
		public $link;
		public $language;
		
		protected $data;
		protected $dom;
		protected $channel;
		
		
		/**
		 * __construct
		 *
		 * @param string (optional) $title
		 * @param string (optional) $description
		 * @param array (optional) $data
		 * @param string (optional) $link
		 * @param string (optional) $language
		 * @return Object
		 * @author Matt Brewer
		 **/

		public function __construct($title="", $description="", $data=array(), $link=ROOT_URL, $language="English") {
			
			$this->dom = new DOMDocument('1.0', 'UTF-8');
			
			$root = $dom->createElement("rss");
			$root->setAttribute("version", "2.0");
			$this->dom->appendChild($root);	
			
			$this->set_data($data);
			
		}
		
		
		/**
		 * set_data
		 *
		 * @param array $data
		 * @return void
		 * @author Matt Brewer
		 **/

		public function set_data($data) {
			$this->data = clean_object($data);	
		}
		
		
		/**
		 * compile_header
		 *
		 * @return void
		 * @author Matt Brewer
		 **/

		protected function compile_header() {
			
			$this->channel = $dom->createElement("channel");
			$root->appendChild($this->channel);
			
			$title = $dom->createElement("title");
			$title->appendChild($dom->createCDATASection($this->title));
			$this->channel->appendChild($title);
		
			$link = $dom->createElement("link");
			$link->appendChild($dom->createCDATASection($this->link));
			$this->channel->appendChild($link);	
			
			$description = $dom->createElement("description");
			$description->appendChild($dom->createCDATASection($this->description));
			$this->channel->appendChild($description);
			
			$language = $dom->createElement("language");
			$language->appendChild($dom->createCDATASection($this->language));
			$this->channel->appendChild($language);
			
		}
		
		
		/**
		 * compile_body
		 *
		 * @return void
		 * @author Matt Brewer
		 **/
		
		protected function compile_body() {
			
			foreach($this->data as $d) {

				$item = $dom->createElement("item");
					
				$title = $dom->createElement("title");
				$link = $dom->createElement("link");
				$description = $dom->createElement("description");
				
				$title->appendChild($dom->createCDATASection($d->title));
				$link->appendChild($dom->createCDATASection($d->link));
				$description->appendChild($dom->createCDATASection($d->description));
				
				$item->appendChild($title);
				$item->appendChild($link);
				$item->appendChild($description);
				
				$this->channel->appendChild($item);
			}
			
		}
		
		
		
		/**
		 * feed
		 *
		 * @return string(XML) $RSS 
		 * @author Matt Brewer
		 **/

		public function feed() {
			
			try {
				
				$this->dom->getElementsByTagName("rss")->get(0)->removeChild($this->child);
				
			} catch(Exception $e) {} 	// Harmless, so just ignore
			
			$this->compile_header();
			$this->compile_body();		
			
			return $this->dom->saveXML();
			
		}
		
		
		/**
		 * printFeed
		 * NOTE: This method stops the execution of the current script
		 *
		 * @return void
		 * @author Matt Brewer
		 **/

		public function printFeed() {
			printXML($this->feed());
		}
	

	}

?>
