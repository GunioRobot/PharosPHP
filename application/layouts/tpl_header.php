<body>
	
	<div id="header-container">
		<div class="container">
			<div id="header">
			
				<div id="extra-nav">
					Welcome, <a href="<?=Template::edit("Users", Authentication::get()->user()->user_id)?>"><?=Authentication::get()->user()->user_first_name?></a> | <a href="<?=Template::controller_link("Session", "logout/")?>">Log Out</a>
				</div><div class="clearBoth"></div>

				<div id="menu">
					
					<?

					Loader::load_class("Sidebar");
					$sidebar = new Sidebar();
					$sidebar->build();	

					$pages = $sidebar->pages();
					$parent_obj = null;
					foreach($pages as $pid => $parent) : ?>
						<? if ( Template::is_current_parent_nav($parent) ) $parent_obj = $parent; ?>
						<a href="<?=Template::site_link($parent->page)?>" class="tab <?=$parent->color?> <? if ( $parent == $parent_obj ): ?> active<? endif?>" id="nav<?=$parent->id?>">
							<? if ($parent->icon != "" ): ?>
							<div class="icon <?=$parent->icon?>"></div>
							<? endif ?>
							<?=$parent->name?>
						</a>
					<? endforeach ?>

					<div class="clearBoth"></div>
					
				</div>
				
				<div id="sub-menu" class="<?=$parent_obj->color ? $parent_obj->color : "charcoal"?>">
					
					<? if ( isset($parent_obj->children) && is_array($parent_obj->children) && !empty($parent_obj->children)): ?>
					<? foreach($parent_obj->children as $child) : ?>
					<div class="button <? if ( Template::site_link($child->page) == Template::site_link(Input::Server("REDIRECT_URL"))) echo 'pressed'?>">
						<div class="button-inner">
							<a href="<?=Template::site_link($child->page)?>"><?=$child->name?></a>
						</div>
					</div>
						
					<? endforeach; endif; ?>
					
					<div class="clear"></div>
				
				</div>
				
			</div>
		</div>
	</div>
	
	<div class="container">
		
	


