<div class="table-hover-cell-container">
	<div class="table-hover-cell-title">
		<?=$line?>
		</div>
	<div class="table-hover-cell-hover">
		<? if ( !empty($hovers) ): ?>
		<ul>
			<? foreach($hovers as $index => $hov): ?>
			<? if ( $index > 0 ) echo '<li>|</li>'?>
			<li><a href="<?=$hov->href?>" title="<?=$hov->title?>" class="<?=$hov->class?>" style="<?=$hov->style?>"><?=$hov->name?></a></li>
			<? endforeach ?>
		</ul>
		<? endif ?>
	</div>
</div>