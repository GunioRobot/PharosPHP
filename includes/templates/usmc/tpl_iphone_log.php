
	<?
		// Pass on all the given GET vars (from being redirected here when wasn't logged in to a page they wanted)
		$get_string = '';
		foreach ( $_GET as $key => $value ) {
			$get_string .= '&'.$key.'='.$value;
		}
	?>
	
	<form id="login" action="log.php?<?=$get_string?>" method="POST" class="form">
       
            <ul>
               	<li><input type="text" placeholder="Username" size="41" name="user"/></li>
				<li><input type="password" placeholder="Password" size="41" name="pass"/></li>
			
            </ul>
            <a style="margin:0 10px;color:rgba(0,0,0,.9)" href="#" class="submit whiteButton">Login</a>
    </form>
	<br />
  
	<div class="info">
		<p>Log in here for access to the HGTV Facebook Page Administrative Area, which provides access to your account and functions for: managing content such as shows and videos.</p>		
	</div>
