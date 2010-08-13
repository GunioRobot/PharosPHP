

<div id="bodyWrapper">

	<div id="DCheader">
					
		
	    <a id="logo" href="<?=Template::site_link()?>" title="Home"></a>
    	<div class="clearBoth"></div>
        <br clear="all" />
		
		<div id="meta-area">
			<a href="<?=Template::site_link("users/edit/".Authentication::get()->user()->user_id."/")?>" title="Edit My Account Information">My Account</a>
			&nbsp;|&nbsp;
			<a href="<?=Template::site_link("session/logout/")?>" title="Sign Out Now">Sign Out</a>
		</div>
		
		
		<div id="nav">
			<?

				require_once CLASSES_PATH.'Sidebar.php' ;
				$sidebar = new Sidebar();
				$sidebar->build();	

				$pages = $sidebar->pages();
				$i = 0;
				$count = count($pages);
				foreach($pages as $pid => $parent) : ?>
					<a href="#" class="topNav<? if ( Template::is_current_parent_nav($parent) ): ?> current<? endif?>" id="nav<?=$parent->id?>"><?=$parent->name?></a>
					<? if ( ++$i != $count ): ?><span class="divider">|</span><? endif ?>
				<? endforeach ?>
				
				<div class="clearBoth"></div>
				<div id="pointer"><img src="<?=PUBLIC_SERVER?>images/nav-pointer.png" alt="v" /></div>
				
				
				<?foreach($pages as $pid => $parent) : ?>
					<div id="nav<?=$parent->id?>Sub" class="subNav">
						<? $i = 0; $count = count($parent->children); ?>
						<? if ( !empty($parent->children) ) : foreach($parent->children as $c) : ?>
						<a href="<?=Template::site_link($c->page)?>" id="<?=$c->id?>-nav"><?=$c->name?></a>
						<? if ( ++$i != $count ): ?><span class="divider">|</span><? endif ?>
					<? endforeach; endif; ?>
					<div class="clearBoth"></div>
					</div>
				<? endforeach ?>
		</div>
		
    </div>
