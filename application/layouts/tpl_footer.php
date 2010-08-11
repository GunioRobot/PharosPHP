
			<div class="clearBoth"></div>
		</div>
	
		<div id="footer">
		
			<br />
			<p>Copyright &copy; <?=date("Y")." ".Settings::get('application.system.site.name')?></p>
		
		</div>
	
	</div>
	
	<? if ( SHOW_PROFILER_RESULTS ) {global $profiler; $profiler->display($db);} ?>
	
	<? Hooks::execute(Hooks::HOOK_TEMPLATE_FOOTER) ?>

	</body>
</html>
