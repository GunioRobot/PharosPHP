
	(function($) {

		$.fn.flashField = function (options, callback) {
		   
		 	var settings = $.extend({
		        startColor: '#FFFFFF',
				stopColor:'#F0F099',
				color1:'#F0F099',
				color2:'#FFFFFF',
				speed:'fast',
				animFunc:'linear',
				times:3
		    }, options);
		
			return this.each(function () {
				
				if ( settings.times == 3 ) {
				
					var el = this;
					$(el).animate({backgroundColor : settings.stopColor}, settings.speed, settings.animFunc, function() {
						$(el).animate({backgroundColor : settings.startColor}, settings.speed, settings.animFunc, function() {
							$(el).animate({backgroundColor : settings.stopColor}, settings.speed, settings.animFunc, function() {
								if ( $.isFunction(callback) ) {
									callback(el);
								}
							});
						});
					});
					
				} else if ( settings.times == 2 ) {
					
					var el = this;
					$(el).animate({backgroundColor : settings.color1}, settings.speed, settings.animFunc, function() {
						$(el).animate({backgroundColor : settings.color2}, settings.speed, settings.animFunc, function() {
							if ( $.isFunction(callback) ) {
								callback(el);
							}
						});
					});
					
				} else {
					
					if ( console ) console.info("Unable to flash field ("+this+") "+settings.times+" times.");
					
				}
				
		    });
		
		}
		
		
		
		$.fn.clearField = function (options, callback) {
			
			var settings = $.extend({
				color:'#FFFFFF',
				speed:'fast',
				animFunc:'linear'
			}, options);
			
			return this.each(function() {
				var el = this;
				$(el).animate({backgroundColor: settings.color}, settings.speed, settings.animFunc, function() {
					if ( $.isFunction(callback) ) {
						callback(el);
					}
				});
			});
			
		}
		
	})(jQuery);