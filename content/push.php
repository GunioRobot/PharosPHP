<?
	
	if ( isset($_GET['f']) AND $_GET['f'] != '' AND file_exists($_GET['f']) ) {

		require_once '../includes/functions/downloads.php';
		force_download(dirname(__FILE__).'/'.$_GET['f']);
		
	} else {
				
		require_once '../includes/app_header.php';
		
		@define('TITLE', SITE_NAME.' - File Not Found');
		require_once TEMPLATE_DIR.'tpl_HTML_header.php';
		require_once TEMPLATE_DIR.'tpl_header.php';		
		
	?>	
	
		<div id="contentWrapper">
			<div id="content" style="margin-left:200px;">
				<p style="color:#82AACD;font-size:18px;">Sorry, your file &quot;<?=get("f")?>&quot; was't found.</p>
				<p>Most likely are viewing an older email and the user has uploaded a newer version in it's place.  Check your inbox for a newer email containing a link to download the updated version.<br /><br />Please contact the system administrator at <a href="mailto:<?=SYS_ADMIN_EMAIL?>?subject=File Not Found&body=I was attempting to download &quot;<?=get("f")?>&quot;, but it isn't there.  Can you help me out?" title="Email Administrator"><?=SYS_ADMIN_EMAIL?></a> if you have any questions.</p>
			</div>
		</div>
		
	<?	
		require_once TEMPLATE_DIR.'tpl_footer.php';	
	}
?>