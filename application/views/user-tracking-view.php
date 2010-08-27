
<div id="dashboard">

	<div class="dashboard-header">Trends - <span id="app_name"><?=$user->full_name?></span></div>

	<div id="dashboard-leftWrapper">
		<br />
	
		<div id="dashboard-top10">
			
			<? if ( !empty($track) ): ?>
						
			<table cellpadding="0" cellspacing="0">
				<tr class="dashboard-table-header-row dashboard-table-row">
					<th class="dashboard-table-header number">&nbsp;</th>
					<th class="dashboard-table-header content-name" style="width:300px;">Video Name</th>
					<th class="dashboard-table-header notes">Date</th>
				</tr>
				
				<? foreach($track as $i => $t): ?>
				<tr class="dashboard-table-row">
					<td class="number"><?=($i+1)?></td>
					<td><?=$t->title?></td>
					<td><?=format_date($t->timestamp,true,true)?></td>
				</tr>
				<? endforeach; ?>
				
			</table>
			
			<? else: ?>
			
				<h2>There is no tracking information for this user at this time</h2>
				<div class="clearBoth"></div>
			
			<? endif ?>
			
		</div>
		<hr class="light">
	</div>


	<div id="dashboard-rightWrapper">
		<h1>Usage Information</h1><div class="clearBoth"></div>
		<p><?=$user->full_name?> registered <?=date('m/d/Y \a\t h:i A', $user->date_added)?>.</p>
		
		<p><?=$user->user_first_name?> has launched the application a total of <?=$launches?> time<?=($launches>0?"s":"")?>.</p>
			
	</div>
	
	<div class="clearBoth"></div>
	<div class="dashboard-header"></div>

</div>





