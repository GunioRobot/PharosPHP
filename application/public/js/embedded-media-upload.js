$(function() {
	$("a.assign-file").live('click', function(e) {
		e.preventDefault();
		var win = window.dialogArguments || parent || top;
		var html = '<img src="'+$(this).closest("div.file").attr("href")+'" />';
		$("strong.remove").remove();
		win.send_to_editor(html);
	});
});