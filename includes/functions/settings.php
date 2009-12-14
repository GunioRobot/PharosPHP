<?


	////////////////////////////////////////////////////////////////////////////////
	//
	//	get_settings(key:String, default:String, stripTags:BOOL)
	//
	//	Returns string value from the key.  If not defined or empty, returns default
	//
	////////////////////////////////////////////////////////////////////////////////

	function get_setting($key, $default="", $stripTags=false) {
	
		global $db;
		
		$setting = $db->Execute("SELECT * FROM general_settings WHERE setting_name RLIKE '$key' LIMIT 1");
		if ( !$setting->EOF ) {
			return $stripTags ? strip_tags($setting->fields['setting_value']) : $setting->fields['setting_value'];
		} else return $default;
	
	}
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	load_dynamic_system_settings()
	//
	//	Grabs known system settings from db to use throughout site
	//
	////////////////////////////////////////////////////////////////////////////////

	function load_dynamic_system_settings() {
		define('SYS_ADMIN_EMAIL', get_setting('Admin Email', 'matt@dmgx.com', true));
		define('SERVER_MAILER', get_setting('Server Email', 'matt@dmgx.com', true));
		define('SITE_NAME', get_setting('Site Name', 'CMS Lite Install', true));
		define('SITE_TAGLINE', get_setting('Site Tagline', 'CMS Framework for Developers', true));
		define('TEMPLATE_NAME', get_setting('Template Name', 'default', true));
		define('TITLE_SEPARATOR', get_setting('Title Separator', ' | ', true));
		define('DEFAULT_KEYWORDS', get_setting('Default Keywords', 'CMS, Content Management System, CMS-Lite, Matt Brewer, PHP', true));
		define('DEFAULT_DESCRIPTION', get_setting('Default Description', SITE_TAGLINE, true));
		define('DEFAULT_ROWS_PER_TABLE_PAGE', get_setting('Default Rows per Table Page', 25, true));
		define('DEFAULT_PAGES_PER_PAGINATION', get_setting('Default Pages per Pagination', 5, true));
		define('SHOW_PROFILER_RESULTS', get_setting('Show Profiler Results', false, true)==="true"?true:false);	
		define('DELETE_OLD_WHEN_UPLOADING_NEW', get_setting('Delete Old When Uploading New',true,true)==="true"?true:false);
		define('RESET_PASSWORD_RANDOM_WORD', get_setting('Reset Password Random Word', '_cmslite',true));
		define('DEFAULT_CONTROLLER_NAME', get_setting('Default Controller Name', 'Users', true));
	}
	
?>