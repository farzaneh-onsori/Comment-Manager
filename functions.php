<?php

//Databse configurations
$dbhost  = 'localhost'; //server
$dbname  = 's246923'; //DB name
$dbuser  = 'root';     // DB username
$dbpass  = '';     //DB password


//in the begining connect to db and check if it works fine
$connection = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
if ($connection->connect_error){
	die($connection->connect_error);
}

//set the autocommit to false for handle the concurrency 
//https://www.php.net/manual/en/mysqli.autocommit.php
mysqli_autocommit($connection, false); 


//run the query and return result
//so i dont need to handle it everytime
function queryMysql($query){
	global $connection;
	$result =mysqli_query($connection , $query);
	return $result;
}


//i need to know if user is logged in or not
//after login, the id is stored in session. so I check it
function userLoggedIn(){
	if (isset($_SESSION['loggedinUserID'])){
		return $user=$_SESSION['loggedinUserID'];
	} else {
		return false;
	}
}

//after logout clear all sessuin data for next login
function destroySession(){
	$_SESSION=array();
	session_destroy();
}



//clear all input data from bad things
function sanitizeString($var){
	global $connection;
	$var = strip_tags($var); //Strip HTML and PHP tags from a string
	$var = htmlentities($var); //Convert all applicable characters to HTML entities
	$var = stripslashes($var);
	return mysqli_real_escape_string($connection,$var);
}


//forces user to use HTTPS url
function forceHttps(){
	if(empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "off"){
		$redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		header('HTTP/1.1 301 Moved Permanently');
		header('Location: ' . $redirect);
		exit();
	}
}

//according to part 6 the user activity has a 2 minute duration
// so on each time function.php is run, this time is updated

function checkSession(){
	if(userLoggedIn()){
		if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 120)) {
			session_unset();     // remove session variable
			session_destroy();   // destroy current session
			session_start();
			$sessionex = "Session expired, please log in again";
			$_SESSION['sessionex']= $sessionex;
			header ( "Location:"."login.php" );
		}
		
		$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
	}
}

/*
 * This function check if the cookie are enabled by getting a msg
 * and verify the msg and if the cookie is set.In case this condition fails
 * the server does not allow the navigation otherwhise set a new cookie
 * with a year duration and redirect to the page that called
 */
function cookieCheck(){
	if(isset($_GET['msg']) && $_GET['msg']=="cookieCheck" && !isset($_COOKIE['cookieCheck'])){
		die("Sorry: Your browser does not support or has disabled cookie.Please enable cookie support to allow the correct execution of the website.");
	}

	if(!isset($_GET['msg']) && !isset($_COOKIE['cookieCheck'])){
		setcookie("cookieCheck", "true",time()+(3600*24*365), '/');
		$redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] .'?msg=cookieCheck'; 
		header('Location: ' . $redirect);
		
	}
	
}










//to show all comments and their data i need to have them all
//so with a select query, i keep all them in a array list
function getAllComments(){
	$query = "SELECT * FROM comments";
	$result = queryMysql ($query);
	while($r = mysqli_fetch_object($result))
    {
        $res[] = $r;
    }
	return $res;
}


//to find the data about the owner of the comment
//it returns only 1 result
function findUserDataFromID($userID){
	$query = "SELECT * FROM users where id='$userID'";
	$result = queryMysql($query);
	return mysqli_fetch_array($result , MYSQLI_ASSOC);
}


//comments can be deleted by their own id, but before ant deletion
// i first check if the comment exists or not
function deleteComment($commentId) {
	global $connection;
	try {
		$query = "SELECT * FROM comments WHERE id='$commentId' FOR UPDATE";
		if (mysqli_num_rows ( queryMysql ( $query ) )) {
			$query = "DELETE from comments WHERE id='$commentId'";
			if (! queryMysql ( $query )){
				throw new Exception ( "Deletion failed!!!" );
			}
			$message = "Comment deleted successfully";
			$_SESSION ['message'] = $message;
			header ( "Location: ". "index.php" );
		} else {
			$message = "Nothing to delete!!!";
			$_SESSION ['message'] = $message;
		}
	} catch ( Exception $e ) {
		mysqli_rollback ( $connection );
		echo "Rollback  " . $e->getMessage ();
	}
	mysqli_commit ( $connection );
}



//vote +1 to the comment
function upvoteComment($commentId) {
    global $connection;
	try {
		$query = "UPDATE comments SET vote=vote+1 WHERE id='$commentId'";
		$result = queryMysql ( $query );
		if (! $result ){
			throw new Exception ( "Upvote Failed" );
		}
	} catch ( Exception $e ) {
		mysqli_rollback ( $connection );
		echo "Rollback  " . $e->getMessage ();
	}
	mysqli_commit ( $connection );
}


// vote -1 
function downvoteComment($commentId) {
	global $connection;
	try {
		$query = "UPDATE comments SET vote=vote-1 WHERE id='$commentId'";
		if (! queryMysql ( $query )){
			throw new Exception ( "Downvote Failed" );
		}
	} catch ( Exception $e ) {
		mysqli_rollback ( $connection );
		echo "Rollback  " . $e->getMessage ();
	}
	mysqli_commit ( $connection );
}


//i only need the updated value to be returned as response
//this func with echo the comment['vote']
function fetchUpdatedFeedback($commentID){
	$query = "SELECT vote FROM comments WHERE id='$commentID';";
	$result = queryMysql($query);
	$comment = mysqli_fetch_array($result,MYSQLI_ASSOC);
	echo $comment['vote'];
}


//adding new comment, but each user can insert only 1 time
// so before any insertion i check if there is any comment
// with this user id??
function addNewComment($userID, $comment, $score){
	global $connection;
    try {
        $query = "SELECT * FROM comments WHERE user_id='$userID' FOR UPDATE";
        if (mysqli_num_rows ( queryMysql ( $query ) )) {
            $message = "You have already commented";
            $_SESSION['message'] = $message;
        } else {
            if($userID != null){
                $query = "INSERT INTO comments (text,score,vote,user_id)
							VALUES ('$comment','$score',0,'$userID')";
                $message = "Comment successfully posted.";
                $_SESSION['message'] = $message;
                if (! queryMysql ( $query )){
                    throw new Exception ( "Commenting failed" );
				}
            }
        }
    } catch ( Exception $e ) {
        mysqli_rollback ( $connection );
        echo "Rollback  " . $e->getMessage ();
    }
    mysqli_commit ( $connection );
}











// User can login by email and password
// if eveything is fine the UserID is saved in session
// for further usages in index.php while i need to find comment's owner
function loginUser($email,$password){
	global $connection;
	//fields are not filled
	if ($email == "" || $password == "") {
		$message= "Fill up all fields";
        $_SESSION['message']= $message;
    } else {
		//wrong data?
        $query = "SELECT email,password,id FROM users
        WHERE email='$email' AND password= md5('$password')";
        $result= queryMysql ($query);
		$array = $result->fetch_array(MYSQLI_ASSOC);
		
        if (mysqli_num_rows($result) == 0) {
            $message= "Email and/or password is wrong";
            $_SESSION['message']= $message;
        } else {
			//save in session the userid
			//it is used later in index
            $_SESSION ['loggedinUserID'] = $array['id'];		
            header ( "Location: ". "index.php" );
        }
    }
}

// signing up new user is performed after the input data is checked
//in signup.php by javascript. but again I check them in here
// for more security
// but before any sign up, i should check if the user already exists?
function SignupNewUser($firstname, $lastname, $email, $password) {
    global $connection;
    
	try{
		$query = "SELECT * FROM users WHERE email='$email' FOR UPDATE";
		if (mysqli_num_rows (queryMysql($query))) {
			echo "<script type=\"text/javascript\">
					alert(\"This email already exists. Please sign up with another email. \")
					</script>";
		} else {
			$reg = preg_match ( '/^\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,3}$/', $email );
			$regp = preg_match ( "/([0-9]+[a-zA-z]+|[a-zA-z]+[0-9]+)[a-zA-Z0-9]*/", $password );
			
			//if the number of found values is bigger than 0, it means regular expression is valid
			if ($reg > 0 && $regp > 0) {
                $query = "INSERT INTO users (firstname, lastname, email, password) VALUES('$firstname', '$lastname', '$email', md5('$password'))";
				
				//after successful sign up login the user
				if(!queryMysql($query)){
					throw new Exception("Signing up new user failed");
                } else {
                    loginUser($email, $password);
                }
			} else {
				echo "<script type=\"text/javascript\">
					alert(\"Error: Input data is not valid. Try again.\")
					</script>";
			}
		}
	} catch (Exception $e){
		mysqli_rollback($connection);
		echo "Rollback  ". $e->getMessage();
	}
	mysqli_commit($connection);
}



?>