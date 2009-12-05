<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!--<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">-->
<html>
<head>
	<title><?=TITLE?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<meta name="keywords" content="<?=KEYWORDS?>" />
	<meta name="description" content="<?=DESCRIPTION?>" />
	<meta name="author" content="Matt Brewer" />
	<!--[if lt IE 7.]>
	<script defer type="text/javascript" src="includes/jscripts/pngfix.js"></script>
	<![endif]-->

	<style type="text/css" media="screen">@import "<?=INCLUDES_SERVER?>jscripts/css/cupertino/jquery-ui-1.7.2.custom.css"; </style>
	

	<?

		
			
		$folder = TEMPLATE_DIR.'css/';
		if ($handle = opendir($folder)) {
			while (false !== ($file = readdir($handle))){
				if ($file != "." && $file != ".." && !is_dir($folder.$file) ) {
					echo '<style type="text/css" media="screen">@import "'.TEMPLATE_SERVER.'css/'.$file.'";</style>'."\n";
				}
			}
		}
		
		
		// Include all the Javascript files - If .php type, then it's directly inserted in here
		if ( is_array($js_array) ) {
			for ( $i=0; $i < count($js_array); $i++ ) {
								
				if ( isset($js_array[$i]['pid_limits']) ) {
					$active = array_search($_GET['pid'], $js_array[$i]['pid_limits']); 
				} else $active = true;
				
				if ( $active !== false ) {
					if ( $js_array[$i]['type'] == '.php' ) {
						require INCLUDES_DIR.'jscripts/'.$js_array[$i]['file'];
						echo "\n";
					} else if ( $js_array[$i]['type'] == '.js' ) {
						echo '<script type="text/javascript" src="'.INCLUDES_SERVER.'jscripts/'.$js_array[$i]['file'].'"></script>'."\n";
					}
				}
			}
		}
	?>
</head>
<body>
