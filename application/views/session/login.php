
	<div id="smallWrap" align="left">	

		<h1><?=Settings::get('application.system.site.name')?></h1>
		<div class="clearBoth"></div>
		<p>Log in here for access to the <?=Settings::get('application.system.site.name')?> Area, which provides access to your account and functions for: managing content and users.</p>		

		<? if ( $loginMessage != "" ): ?>
		<p class="error-message"><?=$loginMessage?></p>
		<? endif ?>

	  		<form name="login" action="<?=Template::site_link('session/login/')?>" method="post">
		<input type="submit" style="width:0px;height:0px;border:none;margin:0px;padding:0px;"/>
		<div class="greyBox">
	        <b class="btop"><b></b></b>
	        <div class="logInBox">
	        	<div class="floatLeft">Username:<br /><input type="text" size="41" name="user"/></div>
	        	<div class="floatLeft">Password:<br /><input type="password" size="41" name="pass"/></div>
				<div class="clearBoth"></div>
				<div class="floatLeft">
					<a href="<?=Template::site_link("session/password-reset/")?>">Reset my password</a>
				</div>
				<div class="clearBoth"></div>
				<div class="floatLeft" align="center"><a href="#" onclick="document.login.submit();" class="buttons">Login</a></div>
				<div class="clearBoth"></div>
	        </div>
	        <b class="bbot"><b></b></b>
	        </form>
	   </div><br /><br /><br /><br />
	</div>
