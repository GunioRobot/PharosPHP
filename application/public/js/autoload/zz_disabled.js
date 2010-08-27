
	$(function() {
		$("a.disabled").unbind('click').click(function(e) {
			e.preventDefault();
		});
	});