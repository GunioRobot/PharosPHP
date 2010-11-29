<?
	
	require_once dirname(__FILE__) . DS . 'module' . DS . 'ActiveRecord.php';
	ActiveRecord\Config::initialize(function($cfg) {
	    $cfg->set_model_directory(MODELS_PATH);
		$environment = Application::environment();
	    $cfg->set_connections(array($environment->env => sprintf('mysql://%s:%s@%s/%s', $environment->settings->database->username, $environment->settings->database->password, $environment->settings->database->host, $environment->settings->database->name)));
	});

?>