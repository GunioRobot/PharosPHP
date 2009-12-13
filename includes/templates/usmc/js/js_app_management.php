<script type="text/javascript">

	$(function() {
		$('#application').change(function() {
			var i = $("#application").val(); 
			$.post('<?=controller_link("applications", "change/")?>'+i+'/', {redirect:window.location.href}, function(data) {
				window.location.href = data.redirect;
			}, "json");
		})
	});

</script>