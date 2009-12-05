	
	
	<div id="DCheaderWrap">
		<div id="DCheader">
		
			<? if ( session('uid') ) : ?>
		        <div id="loginInfo">
					<span style="color:#82aacd;">Welcome to the <?=SITE_NAME?>, <?=$_SESSION['fullname']?></span><br />
					<span style="color:#82aacd;float:right;">Active Location: <select id="application" name="application"><?=list_applications($CURRENT_APP_ID);?></select></span>
					<div class="clearBoth"></div>
				</div>
			<? endif ?>
	        <a href="index.php" title="Dashboard"><img style="margin-left:50px;" src="<?=TEMPLATE_SERVER.'images/logo.jpg'?>" id="logo" alt="Logo" /></a>
			<div class="clearBoth"></div>
               
	        <br clear="all" />
	    </div>
	</div>	
	