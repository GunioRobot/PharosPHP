
<script type="text/javascript">

	$.jQTouch({
		icon: 'jqtouch.png',
		addGlossToIcon: false,
		startupScreen: 'jqt_startup.png',
		statusBar: 'black',
	//	loadHrefByAjax: false,
		preloadImages: [
			'<?=TEMPLATE_DIR.'css/iPhone/'?>themes/jqt/img/chevron_white.png',
			'<?=TEMPLATE_DIR.'css/iPhone/'?>themes/jqt/img/bg_row_select.gif',
			'<?=TEMPLATE_DIR.'css/iPhone/'?>themes/jqt/img/back_button.png',
			'<?=TEMPLATE_DIR.'css/iPhone/'?>themes/jqt/img/back_button_clicked.png',
			'<?=TEMPLATE_DIR.'css/iPhone/'?>themes/jqt/img/button_clicked.png',
			'<?=TEMPLATE_DIR.'css/iPhone/'?>themes/jqt/img/grayButton.png',
			'<?=TEMPLATE_DIR.'css/iPhone/'?>themes/jqt/img/whiteButton.png',
			'<?=TEMPLATE_DIR.'css/iPhone/'?>themes/jqt/img/loading.gif'
		]
	});
      
	// Some sample Javascript functions:
	$(function(){
          
		// Show a swipe event on swipe test
		$('#swipeme').addTouchHandlers().bind('swipe', function(evt, data){                
			$(this).html('You swiped <strong>' + data.direction + '</strong>!');
			// alert('Swiped '+ data.direction +' on #' + $(evt.currentTarget).attr('id') + '.');
		});

		$('a[target="_blank"]').click(function(){
			if ( confirm('This link opens in a new window.') ) {
				return true;
			} else {
				$(this).removeClass('active');
				return false;
			}
		});

		$('a.static').click(function(e) {
			e.preventDefault();
		});
          
		// Page transition callback events
		$('#pageevents')
			.bind('pageTransitionStart', function(e, info){
				$(this).find('.info').append('Started transitioning ' + info.direction + '&hellip; ');
			})
			.bind('pageTransitionEnd', function(e, info){
				$(this).find('.info').append(' finished transitioning ' + info.direction + '.<br /><br />');
			});
              
          
          // AJAX with callback event
		$('a.ajax').bind('pageTransitionEnd', function(e, info) {
			if (info.direction == 'in' && $(this).data('loaded') != 'true') {
				
				confirm($(e.target).attr('href'));
                      
				$(this)
					.append($('<div>Loading&hellip;</div>')
					.load($(e.target).attr('href'), function(){
						$(this).parent().data('loaded', 'true');
					}));
			}
		});

		// Orientation callback event
		$('body').bind('turn', function(e, data){
			$('#orient').html('Orientation: ' + data.orientation);
		})
	});

</script>