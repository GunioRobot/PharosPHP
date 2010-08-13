<script type="text/javascript" src="<?=PUBLIC_URL?>js/autoload/jquery.js"></script>
<script type="text/javascript">

	CMSLite = {};
	CMSLite.ROOT_URL = "<?=ROOT_URL?>";
	CMSLite.PUBLIC_URL = "<?=PUBLIC_URL?>";
	CMSLite.UPLOAD_URL = "<?=UPLOAD_URL?>";
	CMSLite.SITE_NAME = "<?=Settings::get('application.system.site.name')?>";

	$(function() {	
		
		setTimeout(function() {
			$('#alert').slideUp("15000")
		}, 3500);
		
		$('#application').change(function() {
			$.post(CMSLite.ROOT_URL+'Applications/change/'+$(this).val()+'/', {redirect:window.location.href}, function(data) {
				window.location.href = data.redirect;
			}, "json");
		})
		
	});
	
</script>