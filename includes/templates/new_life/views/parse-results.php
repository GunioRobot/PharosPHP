
	<h1>Parsing Results</h1>
	<div class="clearBoth"></div>
	
	<? if ( !empty($messages) ): ?>
	
	<h3>Messages</h3>
	<ul>
		<? foreach($messages as $m): ?>
		<li><?=$m?></li>
		<? endforeach; ?>
	</ul>
	
	<? endif; if ( !empty($added) ): ?>
	
	<br /><br />
	<h3>Items Modified During Import</h3><div class="clearBoth"></div>
	<p><strong>The following (<?=count($added)?>) items were added or updated.</strong></p>
	<p><strong>These changes will be available to users after publishing.</strong></p>
	
	<table cellpadding="0" cellspacing="0" width="100%" align="left">
	
		<tr>
			<th align="left"><strong>Type</strong></th>
			<th align="left"><strong>Name</strong></th>
		</tr>
		
		<? foreach($added as $a): ?>
		<tr>
			<td><?=($a->table_name=="categories"?"Category":"Product")?></td>
			<td><?=$a->title?></td>
		</tr>
		<? endforeach ?>
		
	</table>
	
	<? endif; if ( !empty($removed) ): ?>

	<br /><br />
	<h3>Items Removed During Import</h3><div class="clearBoth"></div>
	<p><strong>The following (<?=count($removed)?>) items were not found in the Excel Database just provided.</strong></p>
	<p>If one of these items was removed in error, please fix the error in the Excel Database and go through the import process again.</p>
	
	<table cellpadding="0" cellspacing="0" width="100%" align="left">
	
		<tr>
			<th align="left"><strong>Type</strong></th>
			<th align="left"><strong>Name</strong></th>
		</tr>
		
		<? foreach($removed as $r): ?>
		<tr>
			<td><?=($r->table_name=="categories"?"Category":"Product")?></td>
			<td><?=$r->title?></td>
		</tr>
		<? endforeach ?>
		
	</table>
	
	<? endif ?>
	
	<div class="floatLeft" align="center"><a class="buttons" href="<?=controller_link(get_class($this))?>" title="Refresh parser and view new results">Parse Again</a></div>
	<div class="clearBoth"></div>