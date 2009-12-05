	<? $loggedIn = session("uid") !== false ? true : false; ?>

	<div id="<?=make_id(TOOLBAR_TITLE)?>">

		<? require_once 'tpl_iphone_toolbar.php' ?>
		
