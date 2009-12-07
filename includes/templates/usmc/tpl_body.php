
<? if ($controllerClass === "Session") : ?>

	<div class="wrapper">
		<div id="contentWrapper" align="center">
		    	<div id="smallWrap" align="left">	

					<h1><?=SITE_NAME?></h1>
					<div class="clearBoth"></div>
					<p>Log in here for access to the <?=SITE_NAME?> Area, which provides access to your account and functions for: managing content and users.</p>		

		       		<form name="login" action="<?=site_link('session/login/')?>" method="post">
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

	

<? else : ?>

	<?

		if ( !isset($_GET['pid']) ) $_GET['pid'] = '';
	
		require_once CLASSES_DIR.'Sidebar.php' ;
		$sidebar = new Sidebar();
		$page = $sidebar->info($_GET['pid']);
		define('CURRENT_HTML_FILE', $page['pg']);
	
		$myFile = explode('?',$page['pg']);
		$myFile[0] = html_entity_decode(preg_replace('/<[^>]*>/', '', $myFile[0]));
		$myFile[1] = html_entity_decode(preg_replace('/<[^>]*>/', '', $myFile[1]));
		$option = explode('&',$myFile[1]);
		for($i=0; $i<count($option); $i++){
			$item = explode('=',$option[$i]);
			if ( substr($item[1],0,2) == '%%' ) {
				$_GET[$item[0]] = eval(substr($item[1],2));
			} else $_GET[$item[0]]=$item[1];
		}

	?>

		<div id="contentWrapper">
	    <div id="lNav" align="center">
	        <b class="btop"><b></b></b>
	        <div id="lNavContent" align="left">
	        	<? echo $sidebar->show($_GET['pid']); ?>
	         </div>
	        <b class="bbot"><b></b></b>
	    </div> 
	    <div id="content">
			<? 
				if ( ($function = get("function")) ) {
					 require INCLUDES_DIR.$function.".php";
				} else { 
					if ( file_exists($myFile[0]) ) require $myFile[0]; 
				}
			?>
	    </div>
	
<? endif ?>