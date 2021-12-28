<?php
include 'header.php';


//Check if the user was logged in and has done a logout
if (isset ( $_SESSION ['logout'] )) {
  $logout = $_SESSION ['logout'];

  if ($logout) {
    $logout = false;
    session_destroy ();
  }
}

//deleting the comment the logged in user owns
if (isset ( $_GET ['delete'] )) {
  $commentID = sanitizeString ( $_GET ['delete'] );
  deleteComment($commentID);
}

//adding a new comment
if (isset($_POST['comment']) && isset($_POST['score'])) {
  $comment = sanitizeString ( $_POST ['comment'] );
  $score = sanitizeString ( $_POST ['score'] );
  addNewComment($loggedinUserID, $comment, $score);
}

//this varibale defines if the user has already commented or not
$canLoggedinUserComment = true;

include 'menu.php';
?>



  <div class="content">
  <table>
    <tr>
      <td>
        <img src="brush-set.jpg" width="300px" height="300px">
        <h3>Niré Beauty: Essential Glow Set</h3>
      </td>
      <td style="text-align: left; width : 100%; padding-left: 50px;" >
        <?php 

          if(isset($message)){
            echo '<h2 class="message">' . $message . '</h2>';
          }

          $totalScore = 0;
          $comments = getAllComments();

          if(count($comments) > 0){

            //sum all the scores of comments
            foreach($comments as $comment) {
              $totalScore += $comment->score;
            }
  
            //calculate and print the average score
            $averageScore = $totalScore / count($comments);
            echo '<h2>Score: ' . round($averageScore,1) . ' / 5 based on ' . count($comments) . ' comments</h2>';
            echo '<hr/>';
  
            //for each comment, show the comment and other data
            foreach($comments as $comment) {
              $commentOwner = findUserDataFromID($comment->user_id);
  
              echo '<div class="comment">';
              echo '<p><strong>' . $commentOwner['firstname'] .
                 ' ' . $commentOwner['lastname'] . 
                 ' (Score: <span style="color: #FF0000">' . $comment->score . '</span>' .
                 ' | Feedback: <span id="' . $comment->id . '" style="color: #0000FF"> ' . $comment->vote . '</span> | ' . $commentOwner['email'] .')</strong></p>';
              echo '<p>' . $comment->text . '</p>';
  
              if(isset($loggedinUserID)){
				
                echo '<button id="upvote" type="button" value="' . $comment->id . '">+</button>';
                echo '<button id="downvote" type="button" value="' . $comment->id . '">-</button>';

                //check if user has already commented. if yes, he cant comment anymore
                //and enable delete option
                if($loggedinUserID == $commentOwner['id']){
                  $canLoggedinUserComment = false;
                  echo ' | <a href="?delete='.$comment->id . '" style="color: #FF0000;"><b>Delete My Comment</b></a>';
                }
              }
  
              echo '<hr/>';
              echo '</div>';
            }
          
          } else {
            echo '<h2>Score: 0 / 5 . There is no comment yet </h2>';
            echo '<hr/>';
          }
        ?>


        <?php
          if( isset($loggedinUserID) && $canLoggedinUserComment){

          ?>
		  <form method="post" action="index.php">
            Comment: <textarea name="comment" required></textarea>
            <br>
            Your score: 
            <select id="score" name="score">
              <option value="0">0</option>
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
              <option value="4">4</option>
              <option value="5">5</option>
            </select> 

            <button type="submit">Submit</button>
          </form>

        <?php

          } else {

            echo 'You can\'t post any comment. It may be because of either 1 or 2:
              <br/>
              1. You are not logged in. <a href="login.php">Login</a> to comment on this product.
              <br/>
              2. You have already commented. Delete your comment to post a new one.';
          
          }
          ?>
      </td>
    </tr>
  </table> 
    
  </div>
    

  <script>
    $(document).ready(function(){
      var voteCounter = 0; //how many votes the user is given
      var previousId = 0; //last vote id to compare with current vote

      //voting +1 button
      $("button#upvote").click(function(){
        var id = $(this).val(); //read it from text
        
        //on new vote ID, counter is reset
        if(previousId != id){ 
          previousId = id;
          voteCounter = 0;
        } 
        
        voteCounter += 1;
        if(voteCounter < 4){
    
          $.ajax({
            url : 'vote.php',
            method : 'post',
            data : { "upvote" : "upvote", commentID : id},
            success : function (response){ 
                //response is the updated value
                $("#"+id).text(response);
              }
          });

        } else {
          //if user exceeds 3 times voting
          alert("You can't give feed back more than 3 times")
        }
      });

      //voting -1
      $("button#downvote").click(function(){
        var id = $(this).val(); //read it from text

        //on new vote ID, counter is reset
        if(previousId != id){
          previousId = id;
          voteCounter = 0;
        }

        voteCounter += 1;
        if(voteCounter < 4){
          $.ajax({
            url : 'vote.php',
            method : 'post',
            data : { "downvote" : "downvote", commentID : id},
            success : function (response){
                //response is the updated value
                $("#"+id).text(response);
              }
          });

        } else {
          //if user exceeds 3 times voting
          alert("You can't give feed back more than 3 times")
        }
      });

    });
  </script>

    
<?php include 'footer.php' ?>