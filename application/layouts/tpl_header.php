

<div id="bodyWrapper">

	<div id="DCheader">
					
		
	    <a id="logo" href="<?=site_link()?>" title="Home"></a>
    	<div class="clearBoth"></div>
        <br clear="all" />

		<? if ( is_logged_in() ) : ?>
		
		<div id="meta-area">
			<a href="<?=site_link("users/edit/".session("uid")."/")?>" title="Edit My Account Information">My Account</a>
			&nbsp;|&nbsp;
			<a href="<?=site_link("session/logout/")?>" title="Sign Out Now">Sign Out</a>
		</div>
		
		
		<div id="nav">
			<?

				require_once CLASSES_DIR.'Sidebar.php' ;
				$sidebar = new Sidebar();
				$sidebar->build();	

				$pages = $sidebar->pages();
				$i = 0;
				$count = count($pages);
				foreach($pages as $pid => $parent) : ?>
					<a href="#" class="topNav<? if ( is_current_parent_nav($parent) ): ?> current<? endif?>" id="nav<?=$parent->id?>"><?=$parent->name?></a>
					<? if ( ++$i != $count ): ?><span class="divider">|</span><? endif ?>
				<? endforeach ?>
				
				<div class="clearBoth"></div>
				<div id="pointer"><img src="<?=TEMPLATE_SERVER?>images/nav-pointer.png" alt="v" /></div>
				
				
				<?foreach($pages as $pid => $parent) : ?>
					<div id="nav<?=$parent->id?>Sub" class="subNav">
						<? $i = 0; $count = count($parent->children); ?>
						<? if ( !empty($parent->children) ) : foreach($parent->children as $c) : ?>
						<a href="<?=site_link($c->page)?>" id="<?=$c->id?>-nav"><?=$c->name?></a>
						<? if ( ++$i != $count ): ?><span class="divider">|</span><? endif ?>
					<? endforeach; endif; ?>
					<div class="clearBoth"></div>
					</div>
				<? endforeach ?>
		</div>
		
		<? endif ?>

    </div>