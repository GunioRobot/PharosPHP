
	<?
		// Pass on all the given GET vars (from being redirected here when wasn't logged in to a page they wanted)
		$get_string = '';
		foreach ( $_GET as $key => $value ) {
			$get_string .= '&'.$key.'='.$value;
		}
	?>

	<div class="wrapper">
		<div id="contentWrapper" align="center">
		    	<div id="smallWrap" align="left">	

					<h1><?=SITE_NAME?></h1>
					<div class="clearBoth"></div>
					<p>Log in here for access to the <?=SITE_NAME?> Area, which provides access to your account and functions for: managing content and users.</p>		

		       		<form name="login" action="log.php?<?=$get_string?>" method="post">
					<input type="submit" style="width:0px;height:0px;border:none;margin:0px;padding:0px;"/>
					<div class="greyBox">
				        <b class="btop"><b></b></b>
				        <div class="logInBox">
				        	<div>Username:<br /><input type="text" size="41" name="user"/></div>
				        	<div>Password:<br /><input type="password" size="41" name="pass"/></div>
							<div class="clearBoth"></div>
							<div>
								<label style="vertical-align:middle;" for="forgot_password">
									<input id="forgot_password" name="forgot_password" type="checkbox" style="vertical-align:middle;border:none;padding:0;margin:0px 5px 0px 0px;width:15px;background:none;">Forgot Password? (Email New Password)
								</label>
							</div>
							<div class="clearBoth"></div><br /><br />
							<div align="center"><a href="#" onclick="document.login.submit();" onClick class="buttons">Login</a></div>
				        </div>
				        <b class="bbot"><b></b></b>
				        </form>
				   </div><br /><br /><br /><br />
				</div>
		</div>
	</div>

