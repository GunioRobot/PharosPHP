	
	
	<div id="DCheaderWrap">
		<div id="DCheader">
		
			<? if ( session('uid') ) : ?>
		        <div id="loginInfo">
					<span>Welcome to the <?=SITE_NAME?>, <?=$_SESSION['fullname']?></span><br />
					<span>Active Location: <select id="application" name="application"><?=list_applications($CURRENT_APP_ID);?></select></span>
					<div class="clearBoth"></div>
				</div>
			<? endif ?>
	        <a href="<?=site_link()?>" title="Dashboard"><img style="margin-left:50px;" src="<?=TEMPLATE_SERVER.'images/logo.jpg'?>" id="logo" alt="Logo" /></a>
			<div class="clearBoth"></div>
               
	        <br clear="all" />
	    </div>
	</div>	
	