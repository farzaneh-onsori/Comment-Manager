//Check cookie with help of javascript
function cookieEnabled(){
	if (!navigator.cookieEnabled) {
		window.alert("Cookie must be enabled!");
		window.stop();
	}
}


function checkForm(form){
	if(form.firstname.value == "") {
		alert("Error: Firstname cannot be blank!");
		form.firstname.focus();
		return false;
	}

	if(form.age.value == "") {
		alert("Error: age cannot be blank!");
		form.age.focus();
		return false;
	}

	if(form.lastname.value == "") {
		alert("Error: Lastname cannot be blank!");
		form.lastname.focus();
		return false;
	}

	if(form.email.value == "") {
		alert("Error: Email cannot be blank!");
		form.email.focus();
		return false;
	}

	re = /^\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,3}$/;
	if(!re.test(form.email.value)) {
		alert("Error: Email must contain only letters, numbers and underscores!");
		form.email.focus();
		return false;
	}
	
	if(form.password.value != "") {
		if(form.password.value == form.username.value) {
			alert("Error: Password must be different from Username!");
			form.password.focus();
			return false;
		}
		re=/([0-9]+[a-zA-z]+|[a-zA-z]+[0-9]+)[a-zA-Z0-9]*/;
		if(!re.test(form.password.value)) {
			alert("Error: password must contain at least one number and one character!");
			form.password.focus();
			return false;
		}

		
		
	} else {
		alert("Error: Please check that you've entered your password!");
		form.password.focus();
		return false;
	}
	
	return true;
}