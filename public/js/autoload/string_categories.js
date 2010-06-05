
	String.prototype.toTitleCase = function () {
		var A = this.replace(/_/g,' ').split(' '), B = [];
		for (var i = 0; A[i] !== undefined; i++) {
			B[B.length] = A[i].substr(0,1).toUpperCase() + A[i].substr(1);
		}
		return B.join(' ');
	}
	
	String.prototype.isValidPhone = function() {
		var re = new RegExp(/^\(?\s*[1-9]\d{2}\s*\)?\s*-*\s*\d{3}\s*\-?\s*\d{4}\s*$/);
		return re.test(this);
	}
	
	String.prototype.isValidEmail = function() {
		var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
		return emailPattern.test(this);
	}