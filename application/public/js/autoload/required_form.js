
	$(function() {
	
		$('form.required-elements').submit(function() {
						
			// Used for the form validation
			errorMessage = '';
			var canSubmit = true;
			
			$('input.form-required').each(function(index,el) {
				
				if ( $(el).attr("placeholder") == $(el).val() ) {
					canSubmit = false;
					errorMessage += el.name.toTitleCase() + ' needs a value!'+"\n";
				} else {
			
					if ( $(el).hasClass("email-field") ) {	// Email address validation
						if ( !el.value.isValidEmail() ) {
							errorMessage += el.name.toTitleCase() + ' is not a valid email address'+"\n";
							$(el).flashField();
							canSubmit = false;
						} else {
							if ( $(el).hasClass("invalid-field") ) {
								$(el).clearField();
							}
						}
					
					} else if ( $(el).hasClass("phone-field") ) {	// Phone number validation
					
						if ( !el.value.isValidPhone() ) {
							errorMessage += el.name.toTitleCase() + ' is not a valid phone number'+"\n";
							$(el).flashField();
							canSubmit = false;
						} else {
							if ( $(el).hasClass("invalid-field") ) {
								$(el).clearField();
							}
						}
					
					} else if ( $(el).hasClass("credit-card-field") ) {
					
						var ccnumber = el.value;
						var type = $(".credit-card-type-field:first :selected").text();
					
						if ( typeof checkCreditCard == 'function' ) {
							if ( !checkCreditCard(ccnumber, type ) ) {
								errorMessage += el.name.toTitleCase() + ' is not a valid number for '+type+"\n";
								$(el).flashField();
								canSubmit = false;
							} else {
								if ( $(el).hasClass("invalid-field") ) {
									$(el).clearField();
								}
							}
						}
										
					} else if ( $(el).is("input:checkbox") ) {
					
						if ( !$(el).is(":checked") ) {
							errorMessage += el.name.toTitleCase() + ' must be checked'+"\n";
							$(el).flashField();
							canSubmit = false;
						} else {
							if ( $(el).hasClass("invalid-field") ) {
								$(el).clearField();
							}
						}
					
					} else if ( $(el).hasClass("validation") ) {
					
						var name = el.name;
						var operand = $(":input:hidden[name="+name+"_operation]").val();
						var first = parseInt($(":input:hidden[name="+name+"_first]").val());
						var second = parseInt($(":input:hidden[name="+name+"_second]").val());
					
						var result = operand === "+" ? first + second : first - second;
						// alert(first+" "+operand+" "+second+" = "+result);
						if ( result != $(el).val() ) {
							canSubmit = false;
							errorMessage += el.name.toTitleCase() + ' is not valid!'+"\n";
						} else {
							if ( $(el).hasClass("invalid-field") ) {
								$(el).clearField();
							}
						}
										
					} else {	// Old school length requirement verification
			
						var minimum = 1;
				
						if ( $(el).hasClass("credit-card-security-code") ) minimum = 3;
				
						if ( el.value.length < minimum ) {
							errorMessage += el.name.toTitleCase() + ' must be at least '+minimum+' characters'+"\n";
							$(el).flashField();
							canSubmit = false;
						} else {
							if ( $(el).hasClass("invalid-field") ) {
								$(el).clearField();
							}
						}
					
					}
					
				}
					
			});
			
			canSubmit = false;			
			if ( !canSubmit ) {
				alert(errorMessage);
			} return canSubmit;
			
		});
		
		
		$('input[type=reset]').click(function(e) {
			$('span.required ~ input[type=text]').clearField();
		});
		
	
	});