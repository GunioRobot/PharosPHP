<?

	require_once LAYOUTS_PATH.'tpl_HTML_header.php';
	echo '<div id="ajax-content">';
	echo Application::controller()->output->content();

?>

		</div>
	</body>
</html>