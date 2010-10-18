$(function() {

	$("div.table-hover-cell-container").hover(function() {
		$(this).find("div.table-hover-cell-hover").show();
	}, function() {
		$(this).find("div.table-hover-cell-hover").hide();
	});
	
});