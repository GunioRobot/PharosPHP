
	<div id="dashboard">

		<div class="dashboard-header">Dashboard - <span id="app_name"><?=$title?></span></div>

		<div id="dashboard-leftWrapper">
			<br />
			
			<div id="dashboard-tabs">
				<a href="#" title="View User Information" id="users-top10">Users</a><a href="#" title="View Content Information" id="content-top10">Content</a>
			</div>
			<hr class="light">
	
			<br /><br />
		
			<div id="dashboard-top10"><div style="text-align:center;font-size:20px;">Loading...</div></div>
			<hr class="light">
		</div>
	
	
		<div id="dashboard-rightWrapper">
			<h1>Popularity</h1><div class="clearBoth"></div>
			<div id="download-popularity">
				<h2 class="popularity">Entries</h2>
				<div class="clearBoth"></div>
				<ol>
					<?=$top_items?>
				</ol>
			</div>		
		
		</div>

		<div class="clearBoth"></div>

		<div class="dashboard-header"></div>

	</div>
	
	



