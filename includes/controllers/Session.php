<?

	class Session extends Controller {
		
		public function __construct() {
			
			parent::__construct();
			$this->title = "Login";
						
		}

	
		public function index() {
						
			if ( session('uid') ) {
				redirect(site_link());
			} else {
				redirect(controller_link(__CLASS__,'login/'));
			}
		
		}
		
		public function login() {
			
			$this->title = "Login";
			
			// If resetting password
			if ( ($user = post('user')) AND post('forgot_password') ) {
				reset_password($user);
				exit;
			}
			
			else if ( ($user = post('user')) AND ($pass = post('pass')) ) {
				
				$info = $this->db->Execute("SELECT * FROM users WHERE user_username = '$user' AND user_password = '$pass' AND user_level >= ".BASIC_USER_LVL." LIMIT 1");
				if ( $info->fields['user_id'] ) {

					// Info needed by system
					$_SESSION['domain_id'] = SECURE_KEYWORD;
					$_SESSION['uid'] = $info->fields['user_id'];
					$_SESSION['user_level'] = $info->fields['user_level'];
					$_SESSION['fullname'] = $info->fields['user_first_name'] . ' ' . $info->fields['user_last_name'];

					// Update last login
					$this->db->Execute("UPDATE users SET user_last_login = NOW() WHERE user_id = '".$info->fields['user_id']."' LIMIT 1");

					// Finish redirecting
					redirect(site_link());
				}
				
				$loginMessage = "Incorrect username/password combination.";
				require_once TEMPLATE_DIR.'views/login-view.php';
							
			} else {
				
				require_once TEMPLATE_DIR.'views/login-view.php';				
				
			}			
		
		}
		
		public function logout() {
			
			$this->title = "Logout";
			
			unset($_SESSION['domain_id']);
			unset($_SESSION['uid']);
			unset($_SESSION['login_type']);
			unset($_SESSION['user_level']);
			unset($_SESSION['app_id']);
			
			redirect(site_link());
			
		}
	
		
		public function passwordReset() {
			$this->title = "Reset Password";
			require_once TEMPLATE_DIR.'views/password-reset-view.php';
		}


		public function processPasswordReset() {
			if ( ($user = post('user')) !== false ) {
				reset_password($user);
				exit;
			} else {
				redirect(controller_link(__CLASS__, "password-reset/"));
			}
		}


		public function passwordSuccessfullyReset() {

			$this->title = 'Password Successfully Reset';
			$message = 'Your password was successfully reset.<br /><br />';
			$message .= 'Check the email account you registered with for your new password and instructions.';
			require_once TEMPLATE_DIR.'views/password-reset-finished-view.php';

		}

		public function passwordResetFailed($code="1") {

			if ( $code == 1 ) {
				$this->title = 'Unable to Reset Password';
				$message = 'We were unable to reset your password because either the username provided is not in the system or there is not a valid email address for this user.';
			} else {
				$this->title = 'Unable to Send Email';
				$message = 'We were unable to send an email to the email address specified in your profile.<br />Please contact the <a href="mailto:'.SYS_ADMIN_EMAIL.'subject?Failed Password Reset">system administrator</a> for help.';
			}

			require_once TEMPLATE_DIR.'views/password-reset-finished-view.php';

		}
	
	}
	
?>