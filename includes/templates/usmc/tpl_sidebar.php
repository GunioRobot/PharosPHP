<div id="lNav" align="center">
    <b class="btop"><b></b></b>
    <div id="lNavContent" align="left">
		<?

			require_once CLASSES_DIR.'Sidebar.php' ;
			$sidebar = new Sidebar();
			$sidebar->build();	

			$pages = $sidebar->pages();
			$first = true;
			foreach($pages as $pid => $parent) : ?>
				<? if ( $first ) $first = false; else echo '<br /><br />'; ?>
				<?=$parent->name?><br />
				<? if ( !empty($parent->children) ) : foreach($parent->children as $c) : ?>
				<div align="center" class="lNavButton"><a href="<?=site_link($c->page)?>" class="buttons" id="<?=$c->id?>-nav"><?=$c->name?></a></div>
			<? endforeach; endif; endforeach; ?>

	    </div>
    <b class="bbot"><b></b></b>
</div>