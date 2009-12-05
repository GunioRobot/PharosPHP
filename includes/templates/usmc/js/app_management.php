<script type="text/javascript">

	$(function() {
		$('#application').change(function() {
			var i = $("#application").val(); 
			window.location.href = '<?=INCLUDES_SERVER?>change_applications.php?&app_id='+i+'&location='+URLEncode(window.location.href);
		})
	});

</script>