<?php
include_once 'functions.php';
session_start();
ob_start();
forceHttps();
checkSession();

//check for logged in user ID
if (isset ( $_SESSION ['loggedinUserID'] )) {
    $loggedinUserID = $_SESSION ['loggedinUserID'];
}

if(isset($_SESSION ['message'])){
    $message = $_SESSION ['message'];
    //in footer the message is unset
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Comment On Niré Beauty</title>
    <link href="style.css" rel="stylesheet" type="text/css">
    <script src="js/mycode.js"></script>
    <script src="js/jquery-3.5.1.min.js"></script>
</head>
<body>
    <script type="text/javascript">
        cookieEnabled();
    </script>

    <div class= "header">
       <noscript>
        Sorry: Your browser does not support or has disabled javascript.
        Please enable javascript to allow the correct execution of the website.
        </noscript>

        <h2>Comment On Niré Beauty!</h2>
        
        
    </div>
    

