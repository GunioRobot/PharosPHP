<script type="text/javascript" src="<?=PUBLIC_SERVER?>js/autoload/jquery.js"></script>
<script type="text/javascript">

	CMSLite = {};
	CMSLite.HTTP_SERVER = "<?=HTTP_SERVER?>";
	CMSLite.PUBLIC_SERVER = "<?=PUBLIC_SERVER?>";
	CMSLite.UPLOAD_SERVER = "<?=UPLOAD_SERVER?>";
	CMSLite.SITE_NAME = "<?=Settings::get('application.system.site.name')?>";

	$(function() {	
		
		setTimeout(function() {
			$('#alert').slideUp("15000")
		}, 3500);
		
		$('#application').change(function() {
			$.post(CMSLite.HTTP_SERVER+'Applications/change/'+$(this).val()+'/', {redirect:window.location.href}, function(data) {
				window.location.href = data.redirect;
			}, "json");
		})
		
	});
	
</script>