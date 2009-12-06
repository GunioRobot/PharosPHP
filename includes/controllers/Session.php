<?

	class Session extends Controller {
		
		public function __construct() {
			
			parent::__contruct();
			$this->name = "Session Manager";
			
		}

	
		public function index() {
			
			if ( session('uid') ) {
				redirect('/'.ADMIN_DIR);
			} else {
				redirect('session/login/');
			}
		
		}
		
		public function login() {
			
			$this->title = "Login";
			
			// If resetting password
			if ( post('user') AND post('forgot_password') ) {
				reset_password($_POST['user']);
				exit;
			}
			
			else if ( ($user = post('user')) AND ($pass = post('pass')) ) {

				$info = $db->Execute("SELECT * FROM users WHERE user_username = '$user' AND user_password = '$pass' AND user_level >= ".BASIC_USER_LVL." LIMIT 1");
				if ( $info->fields['user_id'] ) {

					// Info needed by system
					$_SESSION['domain_id'] = DOMAIN_ID;
					$_SESSION['pws'] = $info->fields['user_password'];
					$_SESSION['signin'] = $info->fields['user_username'];
					$_SESSION['uid'] = $info->fields['user_id'];
					$_SESSION['user_level'] = $info->fields['user_level'];
					$_SESSION['fullname'] = $info->fields['user_first_name'] . ' ' . $info->fields['user_last_name'];

					// Update last login
					$db->Execute("UPDATE users SET user_last_login = NOW() WHERE user_id = '".$info->fields['user_id']."' LIMIT 1");

					// Finish redirecting
					redirect('/'.ADMIN_DIR);
				}
			}			
		
		}
		
		public function logout() {
			
			$this->title = "Logout";
			
			unset($_SESSION['domain_id']);
			unset($_SESSION['pws']);
			unset($_SESSION['signin']);
			unset($_SESSION['uid']);
			unset($_SESSION['login_type']);
			unset($_SESSION['user_level']);
			unset($_SESSION['app_id']);
			
			redirect('index.php');
			
		}
	
	}
	
?>