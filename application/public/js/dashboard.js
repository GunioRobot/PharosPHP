
	$(function() {
		
		// What the tabs do
		$('#dashboard-tabs a').click(function(e) {
			
			// Load in the data
			$("#dashboard-top10").load(PharosPHP.ROOT_URL+"dashboard/content/"+this.id+"/", '', setup_dropdowns);
			
			// Remove current class from all tabs
			$("#dashboard-tabs a").removeClass("current-tab");
						
			// Add the current class to this one
			$(this).addClass("current-tab");			
			
			// Prevent page from moving
			e.preventDefault();
		});
		
		// Load default view the first time
		$('#content-top10').click();
	
	});
	
	// Sets the onChange event for the dropdowns
	function setup_dropdowns() {
		
		// Make filtering by industries and content work
		$('select#paginate').change(register_dropdown);
		$('select#content_types').change(register_dropdown);
		
	}
	
	// Actual function called when select onChange fires
	function register_dropdown() {

		var table = ( $("#users-top10").hasClass("current-tab") ) ? "users-top10" : "content-top10";

		$('#dashboard-top10')
			.load(PharosPHP.ROOT_URL+"dashboard/content/"+table+"/"+$('#content_types').val()+"/"+$('#paginate').val()+"/", '', setup_dropdowns);
	}
	