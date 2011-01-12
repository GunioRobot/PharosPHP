
<? require_once LAYOUTS_PATH.'tpl_HTML_header.php'; ?>

	<div id="header-container">
		<div class="container">
			<div id="header">
				
				<div id="extra-nav">
					&nbsp;
				</div><div class="clearBoth"></div>
				
				<div id="menu">
					<div id="login-header">
						<h1><?=Settings::get("application.system.site.name")?></h1>
					</div>					
				</div>
				
				<div id="sub-menu" class="blue"></div>
			</div>
		</div>
	</div>
	
	<div class="container">
	
<?

	require_once LAYOUTS_PATH.'tpl_body.php';
	require_once LAYOUTS_PATH.'tpl_footer.php';
	
?>