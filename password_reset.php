<?
	require_once('includes/app_header.php');
	
	@define('TITLE', SITE_NAME .'Admin - Password Reset');

	if ( isset($_GET['success']) AND $_GET['success'] == 'true' ) {
		$title = 'Password Successfully Reset';
		$message = 'Your password was successfully reset.<br /><br />';
		$message .= 'Check the email account you registered with for your new password and instructions.';
	} else {
		if ( isset($_GET['bad']) AND $_GET['bad'] == 'true' ) {
			$title = 'Unable to Reset Password';
			$message = 'We were unable to reset your password because either the username provided is not in the system or there is not a valid email address for this user.';
		} else {
			$title = 'Unable to Send Email';
			$message = 'We were unable to send an email to the email address specified in your profile.<br />Please contact the <a href="mailto:'.SYS_ADMIN_EMAIL.'subject?Failed Password Reset">system administrator</a> for help.';
		}
	}

	require_once TEMPLATE_DIR.'HTML_header.php';
	require_once TEMPLATE_DIR.'tpl_header.php';

?>


<div class="wrapper">
	<div id="contentWrapper" align="center">
	    <div id="smallWrap" align="left">	
		<h1><?=$title?></h1>
		<div class="clearBoth"></div>
			<p><?=$message?></p><hr>
			<p>Try again at: <a href="<? echo HTTP_SERVER.ADMIN_DIR ?>"><? echo HTTP_SERVER.ADMIN_DIR ?></a></p>
		</div>
	</div>
</div>


<?php require_once TEMPLATE_DIR.'tpl_footer.php'; ?>
	
