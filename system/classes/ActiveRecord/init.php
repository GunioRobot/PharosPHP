<?
	
	require_once dirname(__FILE__).'/ActiveRecord.php';
	
	ActiveRecord\Config::initialize(function($cfg)
	{
	    $cfg->set_model_directory(MODELS_PATH);
	    $cfg->set_connections(array('development' => sprintf('mysql://%s:%s@%s/%s', Settings::get('application.database.username'), Settings::get('application.database.password'), Settings::get('application.database.host'), Settings::get('application.database.name'))));
	});

?>