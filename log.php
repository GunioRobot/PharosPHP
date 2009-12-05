<?

	require_once 'includes/app_header.php';

	@define('TITLE',SITE_NAME.' - Login');
	@define('TOOLBAR_TITLE', 'Login');
	@define('KEYWORDS','');
	@define('DESCRIPTION','');

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
			$_SESSION['cid'] = $info->fields['company_id'];
			
			// Update last login
			$db->Execute("UPDATE users SET user_last_login = NOW() WHERE user_id = '".$info->fields['user_id']."' LIMIT 1");
			
			// Pass on all the given GET vars (from being redirected here when wasn't logged in to a page they wanted)
			$get_string = '';
			foreach ( $_GET as $key => $value ) {
				$get_string .= '&'.$key.'='.$value;
			}
										
			// Finish redirecting
			redirect('index.php?'.$get_string);
		}
	}
	
	else if ( get('logout', "false") == "true" ) {
					
		unset($_SESSION['domain_id']);
		unset($_SESSION['pws']);
		unset($_SESSION['signin']);
		unset($_SESSION['uid']);
		unset($_SESSION['login_type']);
		unset($_SESSION['user_level']);
		unset($_SESSION['app_id']);
		unset($_SESSION['company_id']);
		
		redirect('log.php');

	}
	

	// Display the content now
	// Will redirect to mobile version if get("mobile") is not set, or set to true and is_iPhone() == true
	list($useMobileVersion, $loadViaAjax) = use_mobile_version();
	if ( $useMobileVersion && !$loadViaAjax ) {
		
		$js_array[] = array('name' => 'jQuery', 'type' => '.js', 'file' => jQuery_src);
		$js_array[] = array('name' => 'jQTouch', 'type' => '.js', 'file' => 'iPhone/jqtouch.js');
		$js_array[] = array('name' => 'jQTouch Init', 'type' => '.php', 'file' => 'iPhone/general.php');

		require_once TEMPLATE_DIR.'tpl_iphone_HTML_header.php';
		require_once TEMPLATE_DIR.'tpl_iphone_header.php';
		require_once TEMPLATE_DIR.'tpl_iphone_log.php';
		require_once TEMPLATE_DIR.'tpl_iphone_footer.php';

	} else if ( $useMobileVersion ) {
		
		require_once TEMPLATE_DIR.'tpl_iphone_header.php';
		require_once TEMPLATE_DIR.'tpl_iphone_log.php';
		require_once TEMPLATE_DIR.'tpl_iphone_footer.php';
		
	} else {

		require_once TEMPLATE_DIR.'tpl_HTML_header.php';
		require_once TEMPLATE_DIR.'tpl_header.php';
		require_once TEMPLATE_DIR.'tpl_log.php';
		require_once TEMPLATE_DIR.'tpl_footer.php';	

	}
		
?>