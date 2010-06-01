
			<div class="clearBoth"></div>
		</div>
	
		<div id="footer">
		
			<br />
			<p>Copyright &copy; <?=date("Y")." ".substr(SITE_NAME,0,-strlen(" Admin"))?></p>
		
		</div>
	
	</div>
	
	<? if ( is_logged_in() && SHOW_PROFILER_RESULTS ) $profiler->display($db) ?>

	</body>
</html>
