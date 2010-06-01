
	(function($) {
	
		$.extend({
			
			download : function(url, data, method) {
			
				if ( url && data ) { 

					data = typeof data == 'string' ? data : $.param(data);

					var inputs = '';
					$.each(data.split('&'), function() { 
						var pair = this.split('=');
						inputs += '<input type="hidden" name="'+ pair[0] +'" value="'+ pair[1] +'" />'; 
					});

					//send request
					$('<form action="'+ url +'" method="'+ (method||'post') +'">'+inputs+'</form>')
						.appendTo('body')
						.submit()
						.remove();
				}
				
			}
			
		}); 

	})(jQuery);