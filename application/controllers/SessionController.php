<?

	class SessionController extends ApplicationController {
		
		public function __construct() {
			parent::__construct();
			$this->title = "Login";
		}

		public function login() {
			
			$this->title = "Login";
			$this->output->cache(1 * Cache::WEEKS);
			
			if ( ($user = post('user')) AND ($pass = post('pass')) ) {
				
				$auth = Authentication::get();
				if ( $auth->login($user, $pass, Settings::get('users.levels.admin'), ">=") ) {
					redirect(Template::site_link());
				}
				
				$loginMessage = "Incorrect username/password combination.";
				$this->output->set("loginMessage", $loginMessage);			
			} 		
			
			$this->output->view("login-view.php");
		
		}
		
		public function logout() {
			$this->title = "Logout";
			Authentication::get()->logout();
			redirect(Template::site_link());
		}
	
		
		public function passwordReset() {
			$this->output->layout = "session-controller-login";
			$this->title = "Reset Password";
			
			$this->output->set("title", $this->title);
			$this->output->cache(1 * Cache::WEEKS);
			$this->output->view("password-reset-view.php");
		}


		public function processPasswordReset() {
			if ( ($user = post('user')) !== false ) {
				Authentication::get()->reset_password($user);
			} else {
				redirect(Template::controller_link(__CLASS__, "password-reset/"));
			}
		}


		public function passwordSuccessfullyReset() {
			
			$this->output->layout = "session-controller-login";
			$this->output->cache(1 * Cache::WEEKS);

			$this->title = 'Password Successfully Reset';
			$message = 'Your password was successfully reset.<br /><br />';
			$message .= 'Check the email account you registered with for your new password and instructions.';
			
			$this->output->set("title", $this->title);
			$this->output->set("message", $message);
			$this->output->view("password-reset-finished-view.php");

		}

		public function passwordResetFailed($code="1") {

			$this->output->layout = "session-controller-login";
			$this->output->cache(1 * Cache::WEEKS);

			if ( $code == 1 ) {
				$this->title = 'Unable to Reset Password';
				$message = 'We were unable to reset your password because either the username provided is not in the system or there is not a valid email address for this user.';
			} else {
				$this->title = 'Unable to Send Email';
				$message = 'We were unable to send an email to the email address specified in your profile.<br />Please contact the <a href="mailto:'.SYS_ADMIN_EMAIL.'subject?Failed Password Reset">system administrator</a> for help.';
			}

			$this->output->set("title", $this->title);
			$this->output->set("message", $message);
			$this->output->view("password-reset-finished-view.php");

		}
	
	}
	
?>