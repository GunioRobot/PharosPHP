
	<div class="toolbar">
		
		<? if ( $pid != IPHONE_WELCOME_PID ) : ?>
			<a class="back" href="#">Back</a>
		<? endif ?>
		
		<h1><?=TOOLBAR_TITLE?></h1>
		<? if ( $loggedIn ) : ?>
			<a class="button slideup" id="logoutButton" href="#logout">Logout</a>
		<? endif ?>
		
	</div>