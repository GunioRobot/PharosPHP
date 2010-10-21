<link rel="stylesheet" type="text/css" href="<?=PUBLIC_URL?>js/fancybox/jquery.fancybox-1.3.1.css" />
<script type="text/javascript" src="<?=PUBLIC_URL?>js/fancybox/jquery.easing-1.3.pack.js"></script>
<script type="text/javascript" src="<?=PUBLIC_URL?>js/fancybox/jquery.mousewheel-3.0.2.pack.js"></script>
<script type="text/javascript" src="<?=PUBLIC_URL?>js/fancybox/jquery.fancybox-1.3.1.pack.js"></script>

<script type="text/javascript">

	$(function() {
	
		$("a[rel=fancybox]").fancybox({
			'width' : '75%',
			'height' : '75%',
			'transitionIn' : 'elastic',
			'transitionOut' : 'elastic',
			'type' : 'iframe'
		});
		
	});

</script>