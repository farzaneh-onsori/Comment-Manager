<?php 
include 'header.php';

//if email exists a new user is registering. so before i clear session
// and after succesful signup user can start new session
if (isset($_SESSION['email'])){
	destroySession();
}

//if email exists it shows new registration
//i sanitize input data to clear bad things and make it secure
if (isset($_POST['email'])){
	$firstname = sanitizeString($_POST['firstname']);
	$lastname = sanitizeString($_POST['lastname']);
	$email = sanitizeString($_POST['email']);
	$password = sanitizeString($_POST['password']);
	SignupNewUser($firstname, $lastname, $email, $password);
}

include 'menu.php';

?>
	


<div class="content">
	<form method="post" action="signup.php" onsubmit="return checkForm(this)" >
		<label><b>Firstname: </b></label>
		<input type="text" name="firstname" >
		
		<br>

		<label><b>Lastname: </b></label>
		<input type="text" name="lastname" >
		
		<br>
		<label><b>age: </b></label>
		<input type="text" placeholder="Enter age: " name="age" >
		
		<br>

		<label><b>Username: </b></label>
		<input type="text" placeholder="Enter Username: u1@p.it" name="email" >
		
		<br>

		<label><b>Password: </b></label>
		<input type="password" placeholder="Enter Password: ( min 1 letter, 1 number)" name="password" >
		
		<br>


	
		
		<button class="button-login" type="submit" >Sign up</button>
	</form>
</div>


<?php 

include 'footer.php' 
?>