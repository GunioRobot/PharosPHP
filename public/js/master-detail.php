<script type="text/javascript">


	$(function() {
		
		$("form#create-new").submit(function(e) {

			e.preventDefault();			
			var saving = $("#submit");

			saving.get(0).disabled = true;
			saving.val("Saving...");

			var str = $(this).serialize();
			$.post($(this).attr("action"), str, function(obj) {

				if ( !obj.error ) {

					if ( obj.created = true ) {
						history.go(0);
					} else {
						saving.val("Save Information");
						saving.get(0).disabled = false;
					}

				} else {

				}

			}, "json");

			return false;
		});



		$("input#delete").click(function(e) {

			e.preventDefault();
			var $this = $(this);

			$this.get(0).disabled = true;
			$this.val("Deleting...");

			jConfirm("Are you sure you want to delete this information?<br /><br /><strong>This action cannot be undone.</strong>", "Delete this information?", function(r) {

				if ( r ) {

					$.post("<?=controller_link(get_class($controller),"delete")?>/"+$("input.identifier").val()+"/", {}, function() {
						history.go(0);
					});

				} else {

					$this.get(0).disabled = false;
					$this.val("Delete");

				}

			});

		});



		$("ul.iphone-list:first li span").click(function(e) {

			var $span = $(this);
			$("ul.iphone-list:first li a.active").removeClass("active");
			$span.siblings("div.continue-arrow").find("a").addClass("active");
			e.preventDefault();

			$("div#template").fadeOut("fast", function() {

				var $template = $(this);	

				if ( $span.attr("id") == "create" ) {

					$("#submit").val("Create New");
					$("#delete").parent().hide();

					$("input.identifier").val(0);
					$("#value, #order_num").val("");
					$template.fadeIn('fast', function() {
						$("#value").focus();
					});

				} else {

					$("#submit").val("Save Information");
					$("input#delete").val("Delete");
					$("input#delete").attr("disabled", false);
					$("#delete").parent().show();

					$("input.identifier").val($span.attr("id"));
					$.get("<?=controller_link(get_class($controller),"info")?>/"+$span.attr("id")+"/", {}, function(obj) {

						if ( !obj.error ) {

							$("#value").val(obj.info.value);
							$("#order_num").val(obj.info.order_num);
							$template.fadeIn('fast', function() {
								$("#value").focus();
							});

						}

					}, "json");

				}

			});


		});
				
		
	});
	
	
</script>