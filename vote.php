<?php
include_once 'functions.php';
session_start();
ob_start();
forceHttps();
checkSession();

//it is important to user be logged in first and if allowed she can update votes
if (isset ( $_SESSION ['loggedinUserID'] )) {
    $loggedinUserID = $_SESSION ['loggedinUserID'];
} else {
    header ( "Location: ". "index.php" );
}

//upvote the comment
//but before allowing access i have to be sure if user is logged in
if (isset ( $_POST ['upvote'] )) {
    $commentID = sanitizeString ( $_POST ['commentID'] );
    upvoteComment($commentID);
}

  
//downvote the comment
//but before allowing access i have to be sure if user is logged in
if (isset ( $_POST ['downvote'] )) {
    $commentID = sanitizeString ( $_POST ['commentID'] );
    downvoteComment($commentID);
}


//i only need the updated value to be returned as response
//this func with echo the comment['vote']
fetchUpdatedFeedback($commentID);

?>

