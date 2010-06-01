<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!--<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">-->
<html>
<head>
	<title><?=SITE_NAME.TITLE_SEPARATOR.$controller->title?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="keywords" content="<?=$controller->keywords?>" />
	<meta name="description" content="<?=$controller->description?>" />
	<meta name="author" content="Matt Brewer" />


	<?

		
		write_css();
		
		$css = $controller->css();
		if ( !empty($css) ) {
			
			foreach($css as $style) : ?>
				<style type="text/css" media="<?=$style['type']?>">@import url(<?=TEMPLATE_SERVER.'css/'.$style['path']?>);</style>
			<? endforeach; 
		}
		
		write_js();	

		$javascript = $controller->javascript();		
		if ( !empty($javascript) ) {
			foreach($javascript as $js) : ?>
				<? if ( $js['type'] == JAVASCRIPT_INCLUDE ) : ?>
					<? $data = $js['data']; require TEMPLATE_DIR.'js/'.$js['path']; ?>
				<? else : ?>
					<script type="text/javascript" src="<?=TEMPLATE_SERVER?>js/<?=$js['path']?>"></script>
			<? endif; endforeach;
		}
		
	?>
	
	<!--[if IE 6]>
		
		<script type="text/javascript" src="<?=TEMPLATE_SERVER.'js/'?>pngfix.js"></script>
		<script type="text/javascript">
			DD_belatedPNG.fix('img, a.ordered-down, a.ordered-up, #pointer, #iphone, #site, ul.iphone-list li');
		</script>
		
		<style type="text/css">
		
			#nav {
				top:24px;
			}
				
		</style>
				
	<![endif]-->
	
</head>
<body>
