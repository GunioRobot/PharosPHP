
			<div class="clearBoth"></div>
		</div>
	
		<div id="footer">
			<div class="floatRight">
				<p>Proudly Powered by the <a href="http://www.pharosphp.com">PharosPHP Framework</a></p>
			</div>
			<div class="floatLeft">
				<p>Copyright &copy; <?=date("Y")." ".Settings::get('application.system.site.name')?></p>
			</div>
			<div class="clearBoth"></div>
		</div>
	
	</div>
	
	<? if ( SHOW_PROFILER_RESULTS ) {global $profiler; $profiler->display($db);} ?>
	
	<? NotificationCenter::execute(NotificationCenter::TEMPLATE_FOOTER_NOTIFICATION) ?>

	</body>
</html>
