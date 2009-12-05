<?
	
	// Site includes
	require_once 'includes/app_header.php';
	
	// Supporting logout in sidebar
	if ( get('pid', "false") == '6' ) {
		redirect('log.php?logout=true');
	}
	
	// Validate login information
	validate_login();
	
	// Whether to display the iPhone version of site or not 
	//	will redirect to mobile version if not specifically set to no
	list($useMobileVersion, $loadViaAjax) = use_mobile_version();
	
	// Get the name of the page
	$pid = get("pid", $useMobileVersion ? IPHONE_WELCOME_PID : WELCOME_PID);
	$_GET['pid'] = $pid;
	$info = $db->Execute("SELECT name FROM admin_nav WHERE id = '$pid' LIMIT 1");
	
	// Support for dynamic titles
	if ( substr($info->fields['name'],0,2) == "%%" ) {
		$info->fields['name'] = eval(substr($info->fields['name'],2));
	}
	
	@define('TITLE', SITE_NAME.' Admin - '.$info->fields['name']);
	@define('TOOLBAR_TITLE', $info->fields['name']);
	@define('KEYWORDS','');
	@define('DESCRIPTION','');
	
	
	if ( !$useMobileVersion ) {
		
		// JavaScipt Array Setup
		$js_array = array(	
		
			// System wide includes
			array('name' => 'IE PNG Fix', 'type' => '.php', 'file' => 'pngfix.php'),
			array('name' => 'jQuery Framework', 'type' => '.js', 'file' => jQuery_src),
			array('name' => 'jQuery UI', 'type' => '.js', 'file' => 'jquery-ui.js'),
			array('name' => 'jQuery Alerts', 'type' => '.js', 'file' => 'jquery.alerts.js'),
			array('name' => 'Confirm Delete using jQuery Alerts', 'type' => '.php', 'file' => 'confirmDelete.php'),
			array('name' => 'Resizing iFrames', 'type' => '.php', 'file' => 'noScroll.php'),
			array('name' => 'jQuery Tree View', 'type' => '.js', 'file' => 'jquery.treeview.pack.js'),
			array('name' => 'Category Tree View', 'type' => '.php', 'file' => 'treeview.php'),		
			array('name' => 'Tiny MCE', 'type' => '.js', 'file' => 'tinymce/jquery.tinymce.js'),
			array('name' => 'Tiny MCE Include', 'type' => '.php', 'file' => 'tiny_mce_include.php'),
			array('name' => 'Fading Repost Message', 'type' => '.js', 'file' => 'repost.js'),
			array('name' => 'URL Encode/Decode', 'type' => '.js', 'file' => 'JS_URLEncode.js'),
			array('name' => 'App Management', 'type' => '.php', 'file' => 'app_management.php'),
			array('name' => 'Publish App', 'type' => '.php', 'file' => 'publish_app.php'),
			array('name' => 'AC Run Flash Content', 'type' => '.js', 'file' => 'AC_RunActiveContent.js'),
			array('name' => 'Time List', 'type' => '.js', 'file' => 'TimeList.js'),	
			array('name' => 'jQuery Color Animation Support', 'type' => '.js', 'file' => 'jquery.color.js'),
	//		array('name' => 'jQuery Facebox', 'type' => '.js', 'file' => 'jquery.facebox.js'),
			
				

		
			// Page specific includes
			array('name' => 'Ajax Upload Support', 'type' => '.js', 'file' => 'swfupload.js', 'pid_limits' => array('','52')),	
			array('name' => 'File Progress', 'type' => '.js', 'file' => 'fileprogress.js', 'pid_limits' => array('','52')),
			array('name' => 'File Upload', 'type' => '.php', 'file' => 'file_upload.php', 'pid_limits' => array('','52')),
			array('name' => 'File Profile', 'type' => '.php', 'file' => 'file_profile.php', 'pid_limits' => array('','52')),
			
			
			array('name' => 'JQuery Facebook/Tokenizer', 'type' => '.js', 'file' => 'jquery.tokeninput.js', 'pid_limits' => array('','25')),
			array('name' => 'JQuery Facebook/Tokenizer', 'type' => '.php', 'file' => 'email_users.php', 'pid_limits' => array('','25')),
			array('name' => 'Manage Applications', 'type' => '.php', 'file' => 'manage_applications.php', 'pid_limits' => array('','26'))
		
		);
		
	} else {
		
		$js_array[] = array('name' => 'jQuery', 'type' => '.js', 'file' => jQuery_src);
		$js_array[] = array('name' => 'jQTouch', 'type' => '.js', 'file' => 'iPhone/jqtouch.js');
		$js_array[] = array('name' => 'jQTouch Init', 'type' => '.php', 'file' => 'iPhone/general.php');
		
	}
	
		
	// Display the content now
	if ( $useMobileVersion && !$loadViaAjax ) {

		require_once TEMPLATE_DIR.'tpl_iphone_HTML_header.php';
		require_once TEMPLATE_DIR.'tpl_iphone_header.php';
		require_once TEMPLATE_DIR.'tpl_iphone_body.php';
		require_once TEMPLATE_DIR.'tpl_iphone_footer.php';

	} else if ( $useMobileVersion ) {
		
		require_once TEMPLATE_DIR.'tpl_iphone_header.php';
		require_once TEMPLATE_DIR.'tpl_iphone_body.php';
		require_once TEMPLATE_DIR.'tpl_iphone_footer.php';
		
	} else {	
		
		require_once TEMPLATE_DIR.'tpl_HTML_header.php';
		require_once TEMPLATE_DIR.'tpl_header.php';
		require_once TEMPLATE_DIR.'tpl_body.php';
		require_once TEMPLATE_DIR.'tpl_footer.php';	
		
	}
	
?>