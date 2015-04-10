<?PHP

		require_once('class.phpmailer.php');

		$servername = "localhost";
		 $username = "root";
		// $username = "matt";
		$password = "YmQCl60qMwe2YpUn34k7";
		$dbname = "jokeswaps";

		// Create connection
		$conn = new mysqli($servername, $username, $password, $dbname );
		// Check connection
		if ($conn->connect_error) {
		    die("Connection failed: " . $conn->connect_error);
		}


		function ProcessMailQueue(){
			// This is the mail queue function to be called when new tokens created
			
			exec("wget -qO- http://jokeswaps.com/mail.php?process_email_queue=true &> /dev/null &");
			
		}

		if (isset($_GET["process_email_queue"]) && $_GET["process_email_queue"] == 'true')
		{
   		 $sql = "SELECT * FROM emailQueue WHERE sent = '0'" ;
   		 $result = $conn->query($sql);
		 
		 if ($result->num_rows > 0) {
		 	// output data of each row
		     while($row = $result->fetch_assoc()) {
				 
				 // Read email data
				 $TokenID = $row["TokenID"];
				 $EmailID = $row["EmailID"];
					 
			   		 $sql = "SELECT * FROM tokens WHERE TokenID = '$TokenID'" ;
			   		 $tokensresult = $conn->query($sql);
					 
					 for ($tokenInfo = array (); $tokensrow = $tokensresult->fetch_assoc(); $tokenInfo[] = $tokensrow); 
					 
 				 	$tokenHash = $tokenInfo[0]["hash"];
				 	$username = $tokenInfo[0]["username"];
				 	$tokenType = $tokenInfo[0]["type"];
				 
		   		 $sql = "SELECT * FROM users WHERE username = '$username'" ;
		   		 $usersresult = $conn->query($sql);
				 
				 for ($userInfo = array (); $usersrow = $usersresult->fetch_assoc(); $userInfo[] = $usersrow); 
 
			 	$email = $userInfo[0]["email"];
				 
				 // Send email
				 if ($tokenType == 'reset')
					 { mailReset($username, $email, $tokenHash, $EmailID); }
				 elseif ($tokenType == 'verify')
					 { mailVerify($username, $email, $tokenHash, $EmailID); }
				 
				 
			 }
			 
		 }
			
		}




function mailReset($username, $email, $tokenHash, $EmailID) {

	
 $mail             = new PHPMailer(); // defaults to using php "mail()"

 $mail->IsSendmail(); // telling the class to use SendMail transport

 $body             = 'Someone has requested a password reset for the JokeSwaps.com account "' . $username . '", which is associated with your email address, if it was not you no action is required and you can ignore this email, the account will remain secure.' . 
 ' <BR />If you did request a password reset, please confirm by clicking <a href="http://www.jokeswaps.com/parents.php?r=' . $tokenHash . '&username=' . $username . '">here</a>' . 
 ' or copy and pasting the following link into your browser: ' .
 'http://www.jokeswaps.com/parents.php?r=' . $tokenHash . '&username=' . $username . 
 ' <BR /><BR />From the Friendly JokeSwaps Robot';

 $mail->AddReplyTo("admin@jokeswaps.com","JokeSwaps");

 $mail->SetFrom('robot@jokeswaps.com', 'JokeSwaps Robot');

 $mail->AddAddress($email, "JokeSwaps Parent");

 $mail->Subject    = "Password reset";

 $mail->AltBody    = 'Someone has requested a password reset for the JokeSwaps.com account "' . $username . '", which is associated with your email address, if it was not you no action is required and you can ignore this email, the account will remain secure.' . 
 '\r\nIf you did request a password reset, please confirm by copy and pasting the following link into your browser: ' .
 'http://www.jokeswaps.com/parents.php?r=' . $tokenHash . '&username=' . $username . 
 '\r\n\r\nFrom the Friendly JokeSwaps Robot';

 $mail->MsgHTML($body);

if(!$mail->Send()) {
  $errorMessage = $mail->ErrorInfo;
  $sql = "UPDATE emailQueue SET sent='3', error='$errorMessage' WHERE EmailID='$EmailID'";
  $conn->query($sql);
} else {
  $sql = "UPDATE emailQueue SET sent='1' WHERE EmailID='$EmailID'";
  $conn->query($sql);
}

	
}

function mailVerify($username, $email, $tokenHash, $EmailID)
	
{

  echo 'Preparing to email: Email ID -> ' . $EmailID ;
  $sql = "UPDATE emailQueue SET sent = '4' WHERE EmailID = '$EmailID' " ;
  if ($conn->query($sql) === TRUE) {
	  echo 'saved to DB';
  } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
  }
  echo 'Checkpoint post sql update';
	
	$mail             = new PHPMailer(); // defaults to using php "mail()"

	$mail->IsSendmail(); // telling the class to use SendMail transport

	$body             = 'Someone has signed up to JokeSwaps.com using your email address, if it was not you no action is required and you can ignore this email.' . 
	' <BR /><BR />If you did sign up to JokeSwaps.com, please confirm your email address by clicking <a href="http://www.jokeswaps.com/parents.php?v=' . $tokenHash . '&username=' . $username . '">here</a>' . 
	' or copy and pasting the following link into your browser: ' .
	'http://www.jokeswaps.com/parents.php?v=' . $tokenHash . '&username=' . $username . 
	' <BR /><BR />From the Friendly JokeSwaps Robot';

	$mail->AddReplyTo("admin@jokeswaps.com","JokeSwaps");

	$mail->SetFrom('robot@jokeswaps.com', 'JokeSwaps Robot');

	$mail->AddAddress($email, "JokeSwaps Parent");

	$mail->Subject    = "Please confirm your email address";

	$mail->AltBody    = 'Someone has signed up to JokeSwaps.com using your email address, if it was not you no action is required and you can ignore this email.' . 
	'\r\nIf you did sign up to JokeSwaps.com, please confirm your email address by copy and pasting the following link into your browser: ' .
	'http://www.jokeswaps.com/parents.php?v=' . $tokenHash . '&username=' . $username . 
	'\r\n\r\nFrom the Friendly JokeSwaps Robot';

	$mail->MsgHTML($body);

	if(!$mail->Send()) {
	  $errorMessage = $mail->ErrorInfo;
	  $sql = "UPDATE emailQueue SET sent=3, error='$errorMessage' WHERE EmailID='$EmailID'";
	  $conn->query($sql);
	} else {
	  $sql = "UPDATE emailQueue SET sent=1 WHERE EmailID='$EmailID'";
	  $conn->query($sql);
	  echo 'Succes: Email ID -> ' . $EmailID . ' Sent to the user';
	}
	
}




$conn->close();

?>