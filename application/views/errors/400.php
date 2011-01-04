<h1>Bad Request</h1><div class="clearBoth"></div>
<p>The request provided is invalid.</p>

<? if ( !Application::environment()->settings->debug ): ?>
<!--
<? endif ?>
<? 
	echo '<code><pre style="overflow:auto;padding:25px;border:1px solid #545454;background:#DBDBDB;font-family:monospace;">';
	echo "Controller: {".Router::controller()."} Method: {".Router::method()."} Params: {"._formatted_output_from_var(Router::params())."}";
	debug_print_backtrace();
	echo '</pre></code>';
?>
<? if ( !Application::environment()->settings->debug ): ?>
-->
<? endif ?>