<script type="text/javascript" src="<?=PUBLIC_URL?>js/autoload/jquery.js"></script>
<script type="text/javascript">

	PharosPHP = {};
	PharosPHP.ROOT_URL = "<?=ROOT_URL?>";
	PharosPHP.PUBLIC_URL = "<?=PUBLIC_URL?>";
	PharosPHP.UPLOAD_URL = "<?=UPLOAD_URL?>";
	PharosPHP.SITE_NAME = "<?=Settings::get('application.system.site.name')?>";

	$(function() {	
		
		setTimeout(function() {
			$('#alert').slideUp("15000")
		}, 3500);
		
		$('#application').change(function() {
			$.post(PharosPHP.ROOT_URL+'Applications/change/'+$(this).val()+'/', {redirect:window.location.href}, function(data) {
				window.location.href = data.redirect;
			}, "json");
		})
		
	});
	

	// function parses mysql datetime string and returns javascript Date object
	function mysqlTimeStampToDate(timestamp) {
    	var regex=/^([0-9]{2,4})-([0-1][0-9])-([0-3][0-9]) ?(?:([0-2][0-9]):([0-5][0-9]):([0-5][0-9]))?$/;
    	var parts=timestamp.replace(regex,"$1 $2 $3 $4 $5 $6").split(' ');
    	return new Date(parts[0],parts[1]-1,parts[2],parts[3],parts[4],parts[5]);
  	}
	
</script>