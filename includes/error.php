<br><br>
<div id="errorBox" align="center">

<? 

	// Present title, or default of "ERROR" if not one
	if ( isset($_SESSION['errorTitle']) ) {
		echo '<h1>' . $_SESSION['errorTitle'] . '</h1>';
	} else echo '<h1>ERROR</h1>';
	
	// Present the error message, or "An unkown error occurred" if not one
	if ( isset($_SESSION['errorMessage']) ) { 
		echo '<p>'. $_SESSION['errorMessage'] . '</p>';
	} else echo '<p>An Unknown Error Occurred</p>';
	
	// Get the text for the link (defaults to 'Continue' if not set)
	$linkText = isset($_SESSION['linkText']) && $_SESSION['linkText'] != '' ? $_SESSION['linkText'] : 'Continue';
	
	// Where you want the link to go
	$linkSrc = isset($_SESSION['linkSrc']) && $_SESSION['linkSrc'] != '' ? $_SESSION['linkSrc'] : 'index.php';

	// Left link
	$leftLinkText = isset($_SESSION['leftLinkText']) && $_SESSION['leftLinkText'] != '' ? $_SESSION['leftLinkText'] : 'Cancel';
	$leftLinkSrc = isset($_SESSION['leftLinkSrc']) && $_SESSION['leftLinkSrc'] != '' ? $_SESSION['leftLinkSrc'] : 'index.php';
	
	// Unregister these so that if call this again and don't provide either, will use default and not previous calls
	unset($_SESSION['errorTitle']);
	unset($_SESSION['errorMessage']);
	unset($_SESSION['linkText']);
	unset($_SESSION['linkSrc']);
	unset($_SESSION['leftLinkSrc']);
	unset($_SESSION['leftLinkText']);
	
	if ( isset($_SESSION['useLeftLink']) AND $_SESSION['useLeftLink'] == 'true' ) echo '<a href="'.$leftLinkSrc.'">'.$leftLinkText.'</a>&nbsp;&nbsp;&nbsp;&nbsp;';
	echo '<a href="'.$linkSrc.'">'.$linkText.'</a>';
	
?>
</div>