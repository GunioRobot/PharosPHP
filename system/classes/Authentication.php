<?

	class Authentication {
		
		public $user_id = false;
		
		protected $logged_in = false;
		protected $login_required = false;
		protected $user;
		protected $db;

		public static function global() {
			return session("pharos_auth");
		}


		public function __construct() {
			global $db;
			$this->db =& $db;
		}

		public function user($u=false) {
			
			if ( $u !== false ) {
				$this->user = $u;
				$this->logged_in = true;
			} else {
				return $this->user;
			}
			
		}
		
		public function login($username, $password, $level) {
			
			$sql = sprintf("SELECT * FROM users WHERE user_username = '%s' AND user_password = '%s' AND user_level = '%d' LIMIT 1", $this->db->prepare_input($username), $this->db->prepare_input($password), $level);
			$info = $this->db->Execute($sql);
			
			if ( !$info->EOF && $info->fields['user_id'] > 0 ) {
				
				$this->logged_in = true;
				unset($info->fields['user_password']);
				$this->user(clean_object($info->fields));
				
				$this->db->Execute(sprintf("UPDATE users SET user_last_login = NOW() WHERE user_id = '%d' LIMIT 1", $this->user->user_id));
				
				$_SESSION['pharos_auth'] = $this;
				
				define('SECURITY_LVL', $_SESSION['user_level']);
				
				return true;
				
			} else return false;
			
		}
		
		public function logout() {
			unset($_SESSSION['pharos_auth']);
		}
		
		public function logged_in() {
			return $this->logged_in;
		}
		
		public function login_required() {
			return $this->login_required;
		}
		
		public function reset_password($username) {

			Modules::load("rmail");

			$info = $this->db->Execute("SELECT * FROM users WHERE user_username = '$username' LIMIT 1");
			if ( $info->fields['user_primary_email'] != '' ) {

				$new_password = random_password();
				$db->Execute("UPDATE users SET user_password = '".$new_password."', last_updated = NOW() WHERE user_id = '".$info->fields['user_id']."' LIMIT 1");

				$html = '<html><body>';
				$html .= '<h2>Password Reset</h2>';
				$html .= 'You requested to have your password reset.  If you did not make the request, please user the system administrator at: <a href="mailto:'.SYS_ADMIN_EMAIL.'?subject=Bad Password Reset">'.SYS_ADMIN_EMAIL.'</a>';
				$html .= '<br /><br /><hr>';
				$html .= 'Your new password is: <strong>'.$new_password.'</strong><br />';
				$html .= 'Login at <a href="'.Template::site_link().'">'.Template::site_link().'</a> with your username and new password.';
				$html .= '</body></html>';

				$mail = new Rmail();
				$mail->setFrom(SERVER_MAILER);
				$mail->setSubject(Settings::get('system.site.name').': Password Reset');
				$mail->setPriority('high');
				$mail->setHTML($html);

				if ( !$mail->send(array($info->fields['user_primary_email'])) ) redirect(site_link('password_reset.php?success=false'));
				else redirect(Template::controller_link('SessionController', 'passwordSuccessfullyReset/'));

			} else redirect(Template::controller_link('SessionController', 'passwordResetFailed/1/'));
			
		}
		
		
		function random_password() {
			return str_replace('_', '', substr(rand(0,100).chr(rand(65,117)).chr(rand(65,117)).chr(rand(65,117)).chr(rand(65,117)).rand(50,100).RESET_PASSWORD_RANDOM_WORD, 0, 49));	// Limit the password to 50 max characters (all the database holds)
		}
		
		
	}

?>