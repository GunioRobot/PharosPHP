
			<div class="clearBoth"></div>
		</div>
	
		<div id="footer">
		
			<br />
			<p>Copyright &copy; <?=date("Y")." ".Settings::get('system.site.name')?></p>
		
		</div>
	
	</div>
	
	<? if ( is_logged_in() && SHOW_PROFILER_RESULTS ) $profiler->display($db) ?>
	
	<? Hooks::call_hook(Hooks::HOOK_TEMPLATE_FOOTER) ?>

	</body>
</html>
