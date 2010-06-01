
	<div id="smallWrap" align="left">	

		<h1><?=SITE_NAME?> | <?=$this->title?></h1>
		<div class="clearBoth"></div>
		<p>Provide the username/email that is on file.  An email containing a new password will be sent to this address.</p>		

		<? if ( $loginMessage != "" ): ?>
		<p class="error-message"><?=$loginMessage?></p>
		<? endif ?>

		<form name="login" action="<?=controller_link(get_class($this), "process-password-reset/")?>" method="post">
		<input type="submit" style="width:0px;height:0px;border:none;margin:0px;padding:0px;"/>
		<div class="greyBox">
	        <b class="btop"><b></b></b>
	        <div class="logInBox">
	        	<div class="floatLeft">Username:<br /><input type="text" size="41" name="user"/></div>
				<div class="clearBoth"></div>
				<div class="floatLeft" align="center"><a href="#" onclick="document.login.submit();" class="buttons">Reset Password</a></div>
				<div class="clearBoth"></div>
	        </div>
	        <b class="bbot"><b></b></b>
	        </form>
	   </div><br /><br /><br /><br />
	</div>
