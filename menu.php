<div class="menu">
	<a class="menu-link" href="index.php">Homepage</a>
	<hr/>
	<?php

		//if user is logged in, hide login-signup and show logout option
		//i read $loggedinUserID from header
		if( isset($loggedinUserID) ){
			echo '<a class="menu-link" href="logout.php">Logout</a>';
		} else {
			echo ' <a  class="menu-link" href="login.php">Login</a>
				<hr/>
				<a  class="menu-link" href="signup.php">Sign up</a>';
		}
	?>
</div>