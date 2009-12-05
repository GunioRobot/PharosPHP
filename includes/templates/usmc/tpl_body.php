<?

	if ( !isset($_GET['pid']) ) $_GET['pid'] = '';
	
	require_once CLASSES_DIR.'Sidebar.php' ;
	$sidebar = new Sidebar();
	$page = $sidebar->info($_GET['pid']);
	define('CURRENT_HTML_FILE', $page['pg']);
	
	$myFile = explode('?',$page['pg']);
	$myFile[0] = html_entity_decode(preg_replace('/<[^>]*>/', '', $myFile[0]));
	$myFile[1] = html_entity_decode(preg_replace('/<[^>]*>/', '', $myFile[1]));
	$option = explode('&',$myFile[1]);
	for($i=0; $i<count($option); $i++){
		$item = explode('=',$option[$i]);
		if ( substr($item[1],0,2) == '%%' ) {
			$_GET[$item[0]] = eval(substr($item[1],2));
		} else $_GET[$item[0]]=$item[1];
	}

?>

	<div id="contentWrapper">
    <div id="lNav" align="center">
        <b class="btop"><b></b></b>
        <div id="lNavContent" align="left">
        	<? echo $sidebar->show($_GET['pid']); ?>
         </div>
        <b class="bbot"><b></b></b>
    </div> 
    <div id="content">
		<? 
			if ( ($function = get("function")) ) {
				 require INCLUDES_DIR.$function.".php";
			} else { 
				if ( file_exists($myFile[0]) ) require $myFile[0]; 
			}
		?>
    </div>