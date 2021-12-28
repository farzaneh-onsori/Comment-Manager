<?php
include 'header.php';

//if only user is already logged in can log out else she redirects to homepage
//for log out delete all session data and then redirect
if (isset($_SESSION['loggedinUserID'])){
	session_unset();
	destroySession();
	session_start();
	$logout= true;
	$_SESSION['logout'] = $logout;
	header ( "Location: ". "index.php" );
	
} else {
	//user is not logged in , cannot do logout
	header ( "Location: ". "index.php" );
}

?>