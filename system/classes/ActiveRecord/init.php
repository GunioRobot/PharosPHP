<?
	
	require_once dir_name(__FILE__).'/ActiveRecord.php';
	
	ActiveRecord\Config::initialize(function($cfg)
	{
	    $cfg->set_model_directory(MODELS_DIR);
	    $cfg->set_connections(array('development' => sprintf('mysql://%s:%s@%s/%s', Settings::get('database.username'), Settings::get('database.password'), Settings::get('database.host'), Settings::get('database.name'))));
	});

?>