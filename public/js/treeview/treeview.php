<script type="text/javascript" src="<?=TEMPLATE_SERVER?>js/treeview/jquery.treeview.pack.js"></script>
<script type="text/javascript">

	$(function() {
		
		// Set up the tree view itself
		$("ul#categories").treeview({
			collapsed: true,
			animated: "fast",
			control:"#treecontrol",
			prerendered: false,
			persist: "location"
		});
		
		// Add the addCategory() click handler to all the category titles that aren't already active on page load
		$('#categories li>a.cat-add').click(addCategory);
	
	});
	

	function removeCategory(dom) {
				
		var cid = $(dom).attr('id').split('-');			// Category id
		cid = cid[1];
		
		$('#cat-'+cid).val('false');					// Set hidden input field to false for this category now
		$(dom).hide();									// Hide the button
		
		// On the title of the category itself
		$('#cat-'+cid+'-add')
			.removeClass('used')						// Just to update the UI
			.click(addCategory);						// Enable the add event
				
	}
	
	function addCategory(e) {
		
		// If only allowing one at a time to be used, when adding click the "remove" button on all those currently used
		if ( typeof noAllowMultiple == 'boolean' && noAllowMultiple == true ) {
			$('#categories li a.used').next().click();
		}
								
		e.preventDefault();								// Don't want the click event moving the page
		
		var cid = $(e.target).attr('id').split('-');	// Category id
		cid = cid[1];
		
		$('#cat-'+cid).val('true');						// Set hidden input field to true for this category now
		$('#cat-'+cid+'-button').show();				// Show the button
		
		// On the title of the category itself
		$(e.target)
			.addClass('used')							// Update the UI
			.unbind('click', addCategory);				// Remove the addCategory functionality
		
	}
	
</script>
