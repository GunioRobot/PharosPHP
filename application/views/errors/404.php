<h1>Resource Not Found</h1><div class="clearBoth"></div>
<p>May we suggest:</p>

<ul>
<?
	
	global $db;
	for ( $pages = $db->Execute("SELECT * FROM pages ORDER BY title ASC"); !$pages->EOF; $pages->moveNext() ) {
		echo '<li><a href="'.internal_external_link($pages->fields['slug'].'/').'" title="'.$pages->fields['title'].'">'.$pages->fields['title'].'</a></li>';
	} 

?>
</ul>

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