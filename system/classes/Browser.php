<?

	/**
	 * Browser
	 *
	 * Usage:
	 * 		Browser::isBrowser(Browser::BROWSER_SAFARI) // to determine if the browser is safari
	 *
	 * @package PharosPHP.Core.Classes
	 * @author Matt Brewer
	 **/

	class Browser {
		
		protected static $_agent = '';
		protected static $_browser_name = '';
		protected static $_version = '';
		protected static $_platform = '';
		protected static $_os = '';
		protected static $_is_aol = false;
        protected static $_is_mobile = false;
        protected static $_is_robot = false;
		protected static $_aol_version = '';
		
		const BROWSER_UNKNOWN = 'unknown';
		const VERSION_UNKNOWN = 'unknown';
		
		const BROWSER_OPERA = 'Opera';                            // http://www.opera.com/
        const BROWSER_OPERA_MINI = 'Opera Mini';                  // http://www.opera.com/mini/
		const BROWSER_WEBTV = 'WebTV';                            // http://www.webtv.net/pc/
		const BROWSER_IE = 'Internet Explorer';                   // http://www.microsoft.com/ie/
		const BROWSER_POCKET_IE = 'Pocket Internet Explorer';     // http://en.wikipedia.org/wiki/Internet_Explorer_Mobile
		const BROWSER_KONQUEROR = 'Konqueror';                    // http://www.konqueror.org/
		const BROWSER_ICAB = 'iCab';                              // http://www.icab.de/
		const BROWSER_OMNIWEB = 'OmniWeb';                        // http://www.omnigroup.com/applications/omniweb/
		const BROWSER_FIREBIRD = 'Firebird';                      // http://www.ibphoenix.com/
		const BROWSER_FIREFOX = 'Firefox';                        // http://www.mozilla.com/en-US/firefox/firefox.html
		const BROWSER_SHIRETOKO = 'Shiretoko';                    // http://wiki.mozilla.org/Projects/shiretoko
		const BROWSER_MOZILLA = 'Mozilla';                        // http://www.mozilla.com/en-US/
		const BROWSER_AMAYA = 'Amaya';                            // http://www.w3.org/Amaya/
		const BROWSER_LYNX = 'Lynx';                              // http://en.wikipedia.org/wiki/Lynx
		const BROWSER_SAFARI = 'Safari';                          // http://apple.com
		const BROWSER_IPHONE = 'iPhone';                          // http://apple.com
        const BROWSER_IPOD = 'iPod';                              // http://apple.com 
		const BROWSER_CHROME = 'Chrome';                          // http://www.google.com/chrome
        const BROWSER_ANDROID = 'Android';                        // http://www.android.com/
        const BROWSER_GOOGLEBOT = 'GoogleBot';                    // http://en.wikipedia.org/wiki/Googlebot
        const BROWSER_SLURP = 'Yahoo! Slurp';                     // http://en.wikipedia.org/wiki/Yahoo!_Slurp
        const BROWSER_W3CVALIDATOR = 'W3C Validator';             // http://validator.w3.org/
        const BROWSER_BLACKBERRY = 'BlackBerry';                  // http://www.blackberry.com/
        const BROWSER_ICECAT = 'IceCat';                          // http://en.wikipedia.org/wiki/GNU_IceCat
		
        const BROWSER_NETSCAPE_NAVIGATOR = 'Netscape Navigator';  // http://browser.netscape.com/ (DEPRECATED)
		const BROWSER_GALEON = 'Galeon';                          // http://galeon.sourceforge.net/ (DEPRECATED)
		const BROWSER_NETPOSITIVE = 'NetPositive';                // http://en.wikipedia.org/wiki/NetPositive (DEPRECATED)
		const BROWSER_PHOENIX = 'Phoenix';                        // http://en.wikipedia.org/wiki/History_of_Mozilla_Firefox (DEPRECATED)
        
		const PLATFORM_UNKNOWN = 'unknown';
		const PLATFORM_WINDOWS = 'Windows';
		const PLATFORM_WINDOWS_CE = 'Windows CE';
		const PLATFORM_APPLE = 'Apple';
		const PLATFORM_LINUX = 'Linux';
		const PLATFORM_OS2 = 'OS/2';
		const PLATFORM_BEOS = 'BeOS';
		const PLATFORM_IPHONE = 'iPhone';
		const PLATFORM_IPOD = 'iPod';
        const PLATFORM_BLACKBERRY = 'BlackBerry';
		
		const OPERATING_SYSTEM_UNKNOWN = 'unknown';
		
		protected function __construct() {}
		
		public function __clone() {
	       trigger_error('Clone is not allowed.', E_USER_ERROR);
	   }
	 
		
		/**
		 * Reset all properties
		 */
		public static function reset() {
			self::_reset();
			self::determine();
		}
		
		protected static function _reset() { 
			self::$_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "";
			self::$_browser_name = self::BROWSER_UNKNOWN;
			self::$_version = self::VERSION_UNKNOWN;
			self::$_platform = self::PLATFORM_UNKNOWN;
			self::$_os = self::OPERATING_SYSTEM_UNKNOWN;
			self::$_is_aol = false;
            self::$_is_mobile = false;
            self::$_is_robot = false;
			self::$_aol_version = self::VERSION_UNKNOWN;
		}
		
		/**
		 * Check to see if the specific browser is valid
		 * @param string $browserName
		 * @return True if the browser is the specified browser
		 */
		public static function isBrowser($browserName) { return( 0 == strcasecmp(self::$_browser_name, trim($browserName))); }

		/**
		 * The name of the browser.  All return types are from the class constants
		 * @return string Name of the browser
		 */
		public static function getBrowser() { return self::$_browser_name; }
		/**
		 * Set the name of the browser
		 * @param $browser The name of the Browser
		 */
		public static function setBrowser($browser) { return self::$_browser_name = $browser; }
		/**
		 * The name of the platform.  All return types are from the class constants
		 * @return string Name of the browser
		 */
		public static function getPlatform() { return self::$_platform; }
		/**
		 * Set the name of the platform
		 * @param $platform The name of the Platform
		 */
		public static function setPlatform($platform) { return self::$_platform = $platform; }
		/**
		 * The version of the browser.
		 * @return string Version of the browser (will only contain alpha-numeric characters and a period)
		 */
		public static function getVersion() { return self::$_version; }
		/**
		 * Set the version of the browser
		 * @param $version The version of the Browser
		 */
		public static function setVersion($version) { self::$_version = preg_replace('[^0-9,.,a-z,A-Z]','',$version); }
		/**
		 * The version of AOL.
		 * @return string Version of AOL (will only contain alpha-numeric characters and a period)
		 */
		public static function getAolVersion() { return self::$_aol_version; }
		/**
		 * Set the version of AOL
		 * @param $version The version of AOL
		 */
		public static function setAolVersion($version) { self::$_aol_version = preg_replace('[^0-9,.,a-z,A-Z]','',$version); }
		/**
		 * Is the browser from AOL?
		 * @return boolean True if the browser is from AOL otherwise false
		 */
		public static function isAol() { return self::$_is_aol; }
		/**
		 * Is the browser from a mobile device?
		 * @return boolean True if the browser is from a mobile device otherwise false
		 */
		public static function isMobile() { return self::$_is_mobile; }
		/**
		 * Is the browser from a robot (ex Slurp,GoogleBot)?
		 * @return boolean True if the browser is from a robot otherwise false
		 */
		public static function isRobot() { return self::$_is_robot; }
		/**
		 * Set the browser to be from AOL
		 * @param $isAol
		 */
		public static function setAol($isAol) { self::$_is_aol = $isAol; }
		/**
		 * Get the user agent value in use to determine the browser
		 * @return string The user agent from the HTTP header
		 */
		public static function getUserAgent() { return self::$_agent; }
		/**
		 * Set the user agent value (the construction will use the HTTP header value - this will overwrite it)
		 * @param $agent_string The value for the User Agent
		 */
		public static function setUserAgent($agent_string) {
			self::reset();
			self::$_agent = $agent_string;
			self::determine();
		}
        protected static function setMobile($value=true) {
            self::$_is_mobile = $value;
        }
        protected static function setRobot($value=true) {
            self::$_is_robot = $value;
        }
		/**
		 * Protected routine to calculate and determine what the browser is in use (including platform)
		 */
		protected static function determine() {
			self::checkPlatform();
			self::checkBrowsers();
			self::checkForAol();
		}

		/**
		 * Protected routine to determine the browser type
		 * @return boolean True if the browser was detected otherwise false
		 */
		protected static function checkBrowsers() {
			return (
						self::checkBrowserGoogleBot() ||
						self::checkBrowserSlurp() ||
						self::checkBrowserInternetExplorer() ||
						self::checkBrowserShiretoko() ||
						self::checkBrowserIceCat() ||
						self::checkBrowserNetscapeNavigator9Plus() ||
						self::checkBrowserFirefox() ||
						self::checkBrowserChrome() ||
                        self::checkBrowserAndroid() ||
						self::checkBrowserSafari() ||
						self::checkBrowserOpera() ||
						self::checkBrowserNetPositive() ||
						self::checkBrowserFirebird() ||
						self::checkBrowserGaleon() ||
						self::checkBrowserKonqueror() ||
						self::checkBrowserIcab() ||
						self::checkBrowserOmniWeb() ||
						self::checkBrowserPhoenix() ||
						self::checkBrowserWebTv() ||
						self::checkBrowserAmaya() ||
						self::checkBrowserLynx() ||
						self::checkBrowseriPhone() ||
						self::checkBrowseriPod() ||
                        self::checkBrowserBlackBerry() ||
						self::checkBrowserW3CValidator() ||
						self::checkBrowserMozilla() /* Mozilla is such an open standard that you must check it last */	
						);
		}

		/**
		 * Determine if the user is using a BlackBerry
		 * @return boolean True if the browser is the BlackBerry browser otherwise false
		 */
		protected static function checkBrowserBlackBerry() {
			$retval = false;
			if( preg_match('/blackberry/i',self::$_agent) ) {
				$aresult = explode("/",stristr(self::$_agent,"BlackBerry"));
				$aversion = explode(' ',$aresult[1]);
				self::setVersion($aversion[0]);
				self::$_browser_name = self::BROWSER_BLACKBERRY;
				self::setMobile();
				$retval = true;
			}
			return $retval;
		}

		/**
		 * Determine if the user is using an AOL User Agent
		 * @return boolean True if the browser is from AOL otherwise false
		 */
		protected static function checkForAol() {
			$retval = false;
			if( preg_match('/aol/i', self::$_agent) ) {
				$aversion = explode(' ',stristr(self::$_agent, 'AOL'));
				self::setAol(true);
				self::setAolVersion(preg_replace('/[^0-9\.a-z]/i', '', $aversion[1]));
				$retval = true;
			}
			else {
				self::setAol(false);
				self::setAolVersion(self::VERSION_UNKNOWN);
				$retval = true;
			}
			return $retval;
		}
		
		/**
		 * Determine if the browser is the GoogleBot or not
		 * @return boolean True if the browser is the GoogletBot otherwise false
		 */
		protected static function checkBrowserGoogleBot() {
			$retval = false;
			if( preg_match('/googlebot/i',self::$_agent) ) {
				$aresult = explode('/',stristr(self::$_agent,'googlebot'));
				$aversion = explode(' ',$aresult[1]);
				self::setVersion(str_replace(';','',$aversion[0]));
				self::$browser_name = self::BROWSER_GOOGLEBOT;
                self::setRobot();
				$retval = true;
			}
			return $retval;
		}
				
		/**
		 * Determine if the browser is the W3C Validator or not
		 * @return boolean True if the browser is the W3C Validator otherwise false
		 */
		protected static function checkBrowserW3CValidator() {
			$retval = false;
			if( preg_match('/W3C-checklink/i',self::$_agent) ) {
				$aresult = explode('/',stristr(self::$_agent,'W3C-checklink'));
				$aversion = explode(' ',$aresult[1]);
				self::setVersion($aversion[0]);
				self::$browser_name = self::BROWSER_W3CVALIDATOR;
				$retval = true;
			}
			return $retval;
		}
		
		/**
		 * Determine if the browser is the W3C Validator or not
		 * @return boolean True if the browser is the W3C Validator otherwise false
		 */
		protected static function checkBrowserSlurp() {
			$retval = false;
			if( preg_match('/Slurp/i',self::$_agent) ) {
				$aresult = explode('/',stristr(self::$_agent,'Slurp'));
				$aversion = explode(' ',$aresult[1]);
				self::setVersion($aversion[0]);
				self::$browser_name = self::BROWSER_SLURP;
                self::setRobot();
				$retval = true;
			}
			return $retval;
		}
		
		/**
		 * Determine if the browser is Internet Explorer or not
		 * @return boolean True if the browser is Internet Explorer otherwise false
		 */
		protected static function checkBrowserInternetExplorer() {
			$retval = false;

			// Test for v1 - v1.5 IE
			if( preg_match('/microsoft internet explorer/i', self::$_agent) ) {
				self::setBrowser(self::BROWSER_IE);
				self::setVersion('1.0');
				$aresult = stristr(self::$_agent, '/');
				if( preg_match('/308|425|426|474|0b1/i', $aresult) ) {
					self::setVersion('1.5');
				}
				$retval = true;
			}
			// Test for versions > 1.5
			else if( preg_match('/msie/i',self::$_agent) && !preg_match('/opera/i',self::$_agent) ) {
				$aresult = explode(' ',stristr(str_replace(';','; ',self::$_agent),'msie'));
				self::setBrowser( self::BROWSER_IE );
				self::setVersion(str_replace(array('(',')',';'),'',$aresult[1]));
				$retval = true;
			}
			// Test for Pocket IE
			else if( preg_match('/mspie/i',self::$_agent) || preg_match('/pocket/i', self::$_agent) ) {
				$aresult = explode(' ',stristr(self::$_agent,'mspie'));
				self::setPlatform( self::PLATFORM_WINDOWS_CE );
				self::setBrowser( self::BROWSER_POCKET_IE );
				self::setMobile();
				
				if( preg_match('/mspie/i', self::$_agent) ) {
					self::setVersion($aresult[1]);
				}
				else {
					$aversion = explode('/',self::$_agent);
					self::setVersion($aversion[1]);
				}
				$retval = true;
			}
			return $retval;
		}
		
		/**
		 * Determine if the browser is Opera or not
		 * @return boolean True if the browser is Opera otherwise false
		 */
		protected static function checkBrowserOpera() {
			$retval = false;
			if( preg_match('/opera mini/i',self::$_agent) ) {
				$resultant = stristr(self::$_agent, 'opera mini');
				if( preg_match('/\//',$resultant) ) {
					$aresult = explode('/',$resultant);
					$aversion = explode(' ',$aresult[1]); 
					self::setVersion($aversion[0]);
					self::$browser_name = self::BROWSER_OPERA_MINI;
					self::setMobile();
                    $retval = true;
				}
				else {
					$aversion = explode(' ',stristr($resultant,'opera mini'));
					self::setVersion($aversion[1]);
					self::$browser_name = self::BROWSER_OPERA_MINI;
					self::setMobile();
					$retval = true;
				}
			}
			else if( preg_match('/opera/i',self::$_agent) ) {
				$resultant = stristr(self::$_agent, 'opera');
				if( preg_match('/Version\/(10.*)$/',$resultant,$matches) ) {
					self::setVersion($matches[1]);
					self::$browser_name = self::BROWSER_OPERA;
					$retval = true;
				}
				else if( preg_match('/\//',$resultant) ) {
					$aresult = explode('/',$resultant);
					$aversion = explode(' ',$aresult[1]); 
					self::setVersion($aversion[0]);
					self::$browser_name = self::BROWSER_OPERA;
					$retval = true;
				}
				else {
					$aversion = explode(' ',stristr($resultant,'opera'));
					self::setVersion($aversion[1]);
					self::$browser_name = self::BROWSER_OPERA;
					$retval = true;
				}
			}
			return $retval;
		}
		
		/**
		 * Determine if the browser is WebTv or not
		 * @return boolean True if the browser is WebTv otherwise false
		 */
		protected static function checkBrowserWebTv() {
			$retval = false;
			if( preg_match('/webtv/i',self::$_agent) ) {
				$aresult = explode('/',stristr(self::$_agent,'webtv'));
				$aversion = explode(' ',$aresult[1]);
				self::setVersion($aversion[0]);
				self::$browser_name = self::BROWSER_WEBTV;
				$retval = true;
			}
			return $retval;
		}
				
		/**
		 * Determine if the browser is NetPositive or not
		 * @return boolean True if the browser is NetPositive otherwise false
		 */
		protected static function checkBrowserNetPositive() {
			$retval = false;
			if( preg_match('/NetPositive/i',self::$_agent) ) {
				$aresult = explode('/',stristr(self::$_agent,'NetPositive'));
				$aversion = explode(' ',$aresult[1]);
				self::setVersion(str_replace(array('(',')',';'),'',$aversion[0]));
				self::$browser_name = self::BROWSER_NETPOSITIVE;
				self::$_platform = self::PLATFORM_BEOS;
				$retval = true;
			}
			return $retval;
		}
			
		/**
		 * Determine if the browser is Galeon or not
		 * @return boolean True if the browser is Galeon otherwise false
		 */
		protected static function checkBrowserGaleon() {
			$retval = false;
			if( preg_match('/galeon/i',self::$_agent) ) {
				$aresult = explode(' ',stristr(self::$_agent,'galeon'));
				$aversion = explode('/',$aresult[0]);
				self::setVersion($aversion[1]);
				self::setBrowser(self::BROWSER_GALEON);
				$retval = true;
			}
			return $retval;
		}
			
		/**
		 * Determine if the browser is Konqueror or not
		 * @return boolean True if the browser is Konqueror otherwise false
		 */
		protected static function checkBrowserKonqueror() {
			$retval = false;
			if( preg_match('/Konqueror/i',self::$_agent) ) {
				$aresult = explode(' ',stristr(self::$_agent,'Konqueror'));
				$aversion = explode('/',$aresult[0]);
				self::setVersion($aversion[1]);
				self::setBrowser(self::BROWSER_KONQUEROR);
				$retval = true;
			}
			return $retval;
		}
			
		/**
		 * Determine if the browser is iCab or not
		 * @return boolean True if the browser is iCab otherwise false
		 */
		protected static function checkBrowserIcab() {
			$retval = false;
			if( preg_match('/icab/i',self::$_agent) ) {
				$aversion = explode(' ',stristr(str_replace('/',' ',self::$_agent),'icab'));
				self::setVersion($aversion[1]);
				self::setBrowser(self::BROWSER_ICAB);
				$retval = true;
			}
			return $retval;
		}
			
		/**
		 * Determine if the browser is OmniWeb or not
		 * @return boolean True if the browser is OmniWeb otherwise false
		 */
		protected static function checkBrowserOmniWeb() {
			$retval = false;
			if( preg_match('/omniweb/i',self::$_agent) ) {
				$aresult = explode('/',stristr(self::$_agent,'omniweb'));
				$aversion = explode(' ',$aresult[1]);
				self::setVersion($aversion[0]);
				self::setBrowser(self::BROWSER_OMNIWEB);
				$retval = true;
			}
			return $retval;
		}
			
		/**
		 * Determine if the browser is Phoenix or not
		 * @return boolean True if the browser is Phoenix otherwise false
		 */
		protected static function checkBrowserPhoenix() {
			$retval = false;
			if( preg_match('/Phoenix/i',self::$_agent) ) {
				$aversion = explode('/',stristr(self::$_agent,'Phoenix'));
				self::setVersion($aversion[1]);
				self::setBrowser(self::BROWSER_PHOENIX);
				$retval = true;
			}
			return $retval;
		}
		
		/**
		 * Determine if the browser is Firebird or not
		 * @return boolean True if the browser is Firebird otherwise false
		 */
		protected static function checkBrowserFirebird() {
			$retval = false;
			if( preg_match('/Firebird/i',self::$_agent) ) {
				$aversion = explode('/',stristr(self::$_agent,'Firebird'));
				self::setVersion($aversion[1]);
				self::setBrowser(self::BROWSER_FIREBIRD);
				$retval = true;
			}
			return $retval;
		}
		
		/**
		 * Determine if the browser is Netscape Navigator 9+ or not (http://browser.netscape.com/ - Official support ended on March 1st, 2008)
		 * @return boolean True if the browser is Netscape Navigator 9+ otherwise false
		 */
		protected static function checkBrowserNetscapeNavigator9Plus() {
			$retval = false;
			if( preg_match('/Firefox/i',self::$_agent) && preg_match('/Navigator\/([^ ]*)/i',self::$_agent,$matches) ) {
				self::setVersion($matches[1]);
				self::setBrowser(self::BROWSER_NETSCAPE_NAVIGATOR);
				$retval = true;
			}
			return $retval;
		}
		
		/**
		 * Determine if the browser is Shiretoko or not (https://wiki.mozilla.org/Projects/shiretoko)
		 * @return boolean True if the browser is Shiretoko otherwise false
		 */
		protected static function checkBrowserShiretoko() {
			$retval = false;
			if( preg_match('/Mozilla/i',self::$_agent) && preg_match('/Shiretoko\/([^ ]*)/i',self::$_agent,$matches) ) {
				self::setVersion($matches[1]);
				self::setBrowser(self::BROWSER_SHIRETOKO);
				$retval = true;
			}
			return $retval;
		}
		
		/**
		 * Determine if the browser is Ice Cat or not (http://en.wikipedia.org/wiki/GNU_IceCat)
		 * @return boolean True if the browser is Ice Cat otherwise false
		 */
		protected static function checkBrowserIceCat() {
			$retval = false;
			if( preg_match('/Mozilla/i',self::$_agent) && preg_match('/IceCat\/([^ ]*)/i',self::$_agent,$matches) ) {
				self::setVersion($matches[1]);
				self::setBrowser(self::BROWSER_ICECAT);
				$retval = true;
			}
			return $retval;
		}
		
		/**
		 * Determine if the browser is Firefox or not
		 * @return boolean True if the browser is Firefox otherwise false
		 */
		protected static function checkBrowserFirefox() {
			$retval = false;
			if( preg_match('/Firefox/i',self::$_agent) ) {
				$aresult = explode('/',stristr(self::$_agent,'Firefox'));
				$aversion = explode(' ',$aresult[1]);
				self::setVersion($aversion[0]);
				self::setBrowser(self::BROWSER_FIREFOX);
				$retval = true;
			}
			return $retval;
		}
		
		/**
		 * Determine if the browser is Mozilla or not
		 * @return boolean True if the browser is Mozilla otherwise false
		 */
		protected static function checkBrowserMozilla() {
			$retval = false;
			if( preg_match('/mozilla/i',self::$_agent) && preg_match('/rv:[0-9].[0-9][a-b]?/i',self::$_agent) && !preg_match('/netscape/i',self::$_agent)) {
				$aversion = explode(' ',stristr(self::$_agent,'rv:'));
				preg_match('/rv:[0-9].[0-9][a-b]?/i',self::$_agent,$aversion);
				self::setVersion(str_replace('rv:','',$aversion[0]));
				self::setBrowser(self::BROWSER_MOZILLA);
				$retval = true;
			}
			else if( preg_match('/mozilla/i',self::$_agent) && preg_match('/rv:[0-9]\.[0-9]/i',self::$_agent) && !preg_match('/netscape/i',self::$_agent) ) {
				$aversion = explode('',stristr(self::$_agent,'rv:'));
            	preg_match('/rv:[0-9]\.[0-9]\.[0-9]/i',self::$_agent,$aversion);
            	echo 
				self::setVersion(str_replace('rv:','',$aversion[0]));
				self::setBrowser(self::BROWSER_MOZILLA);
				$retval = true;
			}
			return $retval;
		}

		/**
		 * Determine if the browser is Lynx or not
		 * @return boolean True if the browser is Lynx otherwise false
		 */
		protected static function checkBrowserLynx() {
			$retval = false;
			if( preg_match('/libwww/i',self::$_agent) && preg_match('/lynx/i', self::$_agent) ) {
				$aresult = explode('/',stristr(self::$_agent,'Lynx'));
				$aversion = explode(' ',$aresult[1]);
				self::setVersion($aversion[0]);
				self::setBrowser(self::BROWSER_LYNX);
				$retval = true;
			}
			return $retval;
		}
			
		/**
		 * Determine if the browser is Amaya or not
		 * @return boolean True if the browser is Amaya otherwise false
		 */
		protected static function checkBrowserAmaya() {
			$retval = false;
			if( preg_match('/libwww/i',self::$_agent) && preg_match('/amaya/i', self::$_agent) ) {
				$aresult = explode('/',stristr(self::$_agent,'Amaya'));
				$aversion = explode(' ',$aresult[1]);
				self::setVersion($aversion[0]);
				self::setBrowser(self::BROWSER_AMAYA);
				$retval = true;
			}
			return $retval;
		}
			
		/**
		 * Determine if the browser is Chrome or not
		 * @return boolean True if the browser is Chrome otherwise false
		 */
		protected static function checkBrowserChrome() {
			$retval = false;
			if( preg_match('/Chrome/i',self::$_agent) ) {
				$aresult = explode('/',stristr(self::$_agent,'Chrome'));
				$aversion = explode(' ',$aresult[1]);
				self::setVersion($aversion[0]);
				self::setBrowser(self::BROWSER_CHROME);
				$retval = true;
			}
			return $retval;
		}		
		
		/**
		 * Determine if the browser is Safari or not
		 * @return boolean True if the browser is Safari otherwise false
		 */
		protected static function checkBrowserSafari() {
			$retval = false;
			if( preg_match('/Safari/i',self::$_agent) && ! preg_match('/iPhone/i',self::$_agent) && ! preg_match('/iPod/i',self::$_agent) ) {
				$aresult = explode('/',stristr(self::$_agent,'Version'));
				if( isset($aresult[1]) ) {
					$aversion = explode(' ',$aresult[1]);
					self::setVersion($aversion[0]);
				}
				else {
					self::setVersion(self::VERSION_UNKNOWN);
				}
				self::setBrowser(self::BROWSER_SAFARI);
				$retval = true;
			}
			return $retval;
		}		
		
		/**
		 * Determine if the browser is iPhone or not
		 * @return boolean True if the browser is iPhone otherwise false
		 */
		protected static function checkBrowseriPhone() {
			$retval = false;
			if( preg_match('/iPhone/i',self::$_agent) ) {
				$aresult = explode('/',stristr(self::$_agent,'Version'));
				if( isset($aresult[1]) ) {
					$aversion = explode(' ',$aresult[1]);
					self::setVersion($aversion[0]);
				}
				else {
					self::setVersion(self::VERSION_UNKNOWN);
				}
				self::setMobile();
				self::setBrowser(self::BROWSER_IPHONE);
				$retval = true;
			}
			return $retval;
		}		

		/**
		 * Determine if the browser is iPod or not
		 * @return boolean True if the browser is iPod otherwise false
		 */
		protected static function checkBrowseriPod() {
			$retval = false;
			if( preg_match('/iPod/i',self::$_agent) ) {
				$aresult = explode('/',stristr(self::$_agent,'Version'));
				if( isset($aresult[1]) ) {
					$aversion = explode(' ',$aresult[1]);
					self::setVersion($aversion[0]);
				}
				else {
					self::setVersion(self::VERSION_UNKNOWN);
				}
				self::setMobile();
				self::setBrowser(self::BROWSER_IPOD);
				$retval = true;
			}
			return $retval;
		}		

		/**
		 * Determine if the browser is Android or not
		 * @return boolean True if the browser is Android otherwise false
		 */
		protected static function checkBrowserAndroid() {
			$retval = false;
			if( preg_match('/Android/i',self::$_agent) ) {
				$aresult = explode('/',stristr(self::$_agent,'Version'));
				if( isset($aresult[1]) ) {
					$aversion = explode(' ',$aresult[1]);
					self::setVersion($aversion[0]);
				}
				else {
					self::setVersion(self::VERSION_UNKNOWN);
				}
				self::setMobile();
				self::setBrowser(self::BROWSER_ANDROID);
				$retval = true;
			}
			return $retval;
		}		

		/**
		 * Determine the user's platform
		 */
		protected static function checkPlatform() {
			if( preg_match('/iPhone/i', self::$_agent) ) {
				self::$_platform = self::PLATFORM_IPHONE;
			}
			else if( preg_match('/iPod/i', self::$_agent) ) {
				self::$_platform = self::PLATFORM_IPOD;
			}
			else if( preg_match('/BlackBerry/i', self::$_agent) ) {
				self::$_platform = self::PLATFORM_BLACKBERRY;
			}
			else if( preg_match('/win/i', self::$_agent) ) {
				self::$_platform = self::PLATFORM_WINDOWS;
			}
			elseif( preg_match('/mac/i', self::$_agent) ) {
				self::$_platform = self::PLATFORM_APPLE;
			}
			elseif( preg_match('/linux/i', self::$_agent) ) {
				self::$_platform = self::PLATFORM_LINUX;
			}
			elseif( preg_match('/OS\/2/i', self::$_agent) ) {
				self::$_platform = self::PLATFORM_OS2;
			}
			elseif( preg_match('/BeOS/i', self::$_agent) ) {
				self::$_platform = self::PLATFORM_BEOS;
			}
		}
	}

?>