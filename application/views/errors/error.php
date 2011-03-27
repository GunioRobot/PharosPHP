<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>Pharos PHP | Uncaught Error</title>
	<link rel="stylesheet" type="text/css" href="<?=ROOT_URL . APP_DIR . DS . PUBLIC_DIR . DS . 'css' . DS?>style_site.css" />
</head>

<body>
	<div id="bodyWrapper">
		<div id="DCheader"></div>
		<div id="contentWrapper">
			<div id="content" style="font-size:16px;">
				
				<h1>Uncaught Error</h1>
				<div class="clearBoth"></div><br />
				<? echo $message; ?>
				
			</div>
			<div class="clearBoth"></div>
		</div>
		<div id="footer">
			<br /><br /><br />
			<p>Uncaught <?=get_class($exception)?></p>
		</div>
	</div>
</body>
</html>
