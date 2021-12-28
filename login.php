<?php
include 'header.php';

//if the user is already logged in, it will redirect to homepage
if (isset ($loggedinUserID)) {
	header ( "Location: ". "index.php" );
}

//
if (isset ( $_POST ['username'] ) && isset ( $_POST ['password'] ) ) {
	$username = sanitizeString ( $_POST ['username'] );
	$password = sanitizeString ( $_POST ['password'] );
	loginUser($username, $password)	;
	$message = $_SESSION['message'];
}

include 'menu.php';

?>

<div class="content">
	<form method='post' action='login.php'>
		<label><b>Username</b></label>
		<input type="text" placeholder="Enter Username" name="username" >
		
		<br>

		<label><b>Password</b></label>
		<input type="password" placeholder="Enter Password" name="password" >
		
		<br>

		<button class="button-login" type="submit">Log in</button>
		
		<?php 
			if(isset($message)) 
				echo '<br><br>' . $message;
		?>
	</form>
</div>

<?php
include 'footer.php'
?>