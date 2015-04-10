<?PHP


		require_once('mail.php');

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

$user = substr($_SERVER['HTTP_HOST'], 0, strpos($_SERVER['HTTP_HOST'], "."));
 $user = mysqli_real_escape_string($conn, $user);

// Verify email address
if (isset($_GET['v']) && isset($_GET['username']))
{
$username = $_GET['username'];
$sql = "SELECT * FROM tokens WHERE username = '$username' AND type = 'verify' ORDER BY TokenID DESC " ;
$result = $conn->query($sql);

for ($userInfo = array (); $row = $result->fetch_assoc(); $userInfo[] = $row);
$tokeHash = $userInfo[0]["hash"];
$tokenExpires = $userInfo[0]["expires"];
$tokenStatus = $userInfo[0]["status"];

if ($tokeHash == $_GET['v'])
{

if ($tokenExpires < date("now"))
{

if ($tokenStatus == '0')
{

// Save token
$sql = "UPDATE tokens SET status='1' WHERE username='$username'";
if ($conn->query($sql) === TRUE) {
$sql = "UPDATE users SET verified='1' WHERE username='$username'";
if ($conn->query($sql) === TRUE) {
echo 'Your email address has been verified and your account has been enabled at <a href="http://' . $username . '.jokeswaps.com">http://' . $username . '.jokeswaps.com</a>';
}
}
}
else
{
echo 'Email address already verified';
}

}
else
{
echo 'Token expired';
}

}
else 
{
echo 'Invalid token.';

}

}





// Verify password reset
if (isset($_GET['r']) && isset($_GET['username']) && empty($_POST['update']))
{
$username = $_GET['username'];
$sql = "SELECT * FROM tokens WHERE username = '$username' AND type = 'reset' ORDER BY TokenID DESC " ;
$result = $conn->query($sql);

for ($userInfo = array (); $row = $result->fetch_assoc(); $userInfo[] = $row);
$tokeHash = $userInfo[0]["hash"];
$tokenExpires = $userInfo[0]["expires"];
$tokenStatus = $userInfo[0]["status"];

if ($tokeHash == $_GET['r'])
{

if ($tokenExpires < date("now"))
{

if ($tokenStatus == '0')
{

// Save token
$sql = "UPDATE tokens SET status='1' WHERE username='$username'";
if ($conn->query($sql) === TRUE) {


	$sql = "SELECT * FROM users WHERE username = '$username'" ;
	$result = $conn->query($sql);


	for ($userInfo = array (); $row = $result->fetch_assoc(); $userInfo[] = $row);

	$hash = $userInfo[0]["password"];


	$signedIn = 'true';

	echo 'You are signed in as the parent for user :' . $username;
	echo '<BR />Please use the update form to save a new password.';


}
}
else
{
echo 'Password already reset using this token.';
}

}
else
{
echo 'Token expired';
}

}
else 
{
echo 'Invalid token.';

}

}


	// Forgotten Password form submit
	if (isset($_POST['forgotPassword']) && $_POST['forgotPassword'] == "true")
	{
		
		$username = $_POST["username"];
		 $username = mysqli_real_escape_string($conn, $username);
		$email = $_POST["email"];
		 $email = mysqli_real_escape_string($conn, $email);
		
		 // Check username and email match
		 $sql = "SELECT * FROM users WHERE username = '$username'" ;
		 $result = $conn->query($sql);
		 
		 for ($userInfo = array (); $row = $result->fetch_assoc(); $userInfo[] = $row);

		 if ($userInfo[0]["email"] == $email)
		 {
		
		 $expires = date("Y-m-d H:i:s");

		 $tokenHash = urlencode(crypt(rand(), strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.')));
		 // Save token
		 $sql = "INSERT INTO tokens (type, hash, expires, status, username)
		 VALUES ('reset', '$tokenHash', '$expires', '0', '$username')";

		 if ($conn->query($sql) === TRUE) {


		 echo 'http://www.jokeswaps.com/parents.php?r=' . $tokenHash . '&username=' . $username;

 		$TokenID =	 mysqli_insert_id($conn);
		 
		 $sql = "INSERT INTO emailQueue (TokenID, sent)
			 VALUES ('$TokenID', '0')";
		 if ($conn->query($sql) === TRUE) {
			 ProcessMailQueue();
		 } else {
		     echo "Error: " . $sql . "<br>" . $conn->error;
		 }



		 } else {
		     echo "Error: " . $sql . "<br>" . $conn->error;
		 }
		 
	 }
	 else
	 {
		 echo 'Username and email do not match.';
		
	 }
		
		
	}


	// Edit and delete jokes
if (isset($_POST['editJokes']) && $_POST['editJokes'] == "true")
	{

		$forUser = $_POST['username'];
		
		foreach ($_POST['joke'] as $id => $joke)
		{
			
			 $joke = mysqli_real_escape_string($conn, $joke);

			// Update joke
			$sql = "UPDATE jokes SET joke= '$joke' WHERE id= '$id' AND forUser = '$forUser'";
		
			if ($conn->query($sql) === TRUE) {
			} else {
			    echo "Error: " . $sql . "<br>" . $conn->error;
			}	
		}		
		
		foreach ($_POST['answer'] as $id => $answer)
		{
			
			 $answer = mysqli_real_escape_string($conn, $answer);

			// Update joke
			$sql = "UPDATE jokes SET answer= '$answer' WHERE id= '$id' AND forUser = '$forUser'";
		
			if ($conn->query($sql) === TRUE) {
			} else {
			    echo "Error: " . $sql . "<br>" . $conn->error;
			}	
		}
		
		foreach ($_POST['delete'] as $id => $delete)
		{
			if (isset($delete) && $delete == 'true')

			// Update joke
			$sql = "DELETE FROM jokes WHERE id= '$id' AND forUser = '$forUser'";
		
			if ($conn->query($sql) === TRUE) {
			} else {
			    echo "Error: " . $sql . "<br>" . $conn->error;
			}	
		}	
	}



// Signup form submit
if (isset($_POST['signup']) && $_POST['signup'] == "true")
{

$captcha;
if(isset($_POST['g-recaptcha-response'])){
      $captcha=$_POST['g-recaptcha-response'];
    }
    else {
      echo '<h2>Please check the the captcha form.</h2>';
      exit;
    }
    $response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6Le44QQTAAAAALSxrlG4JJes_KkBDh308YpOiquR&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']);

$response = explode(",", $response);
$response = explode(":", $response[0]);
$response = str_replace(' ', '', $response[1]);
    if($response == "false")
{
echo 'Error: We think you are a robot! You didn\'t complete the verification';
}
else
{

$username = $_POST["username"];
 $username = mysqli_real_escape_string($conn, $username);
$email = $_POST["email"];
 $email = mysqli_real_escape_string($conn, $email);
$bio = $_POST["bio"];
 $bio = mysqli_real_escape_string($conn, $bio);
$secret = $_POST["secret"];
 $secret = mysqli_real_escape_string($conn, $secret);

$theme = $_POST["theme"];
$avatar = $_POST["avatar"];


$hash = crypt($_POST["password"], (sprintf("$2a$%02d$", 10) . strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.')));

// Check username availability
$sql = "SELECT * FROM users WHERE username = '$username'" ;
$result = $conn->query($sql);
$count = $result->num_rows;

if ($count > 0)
{
echo 'Username already taken, please try a new username';

//Need to add a prefill with other entered data

}
else
{

// Save user
$sql = "INSERT INTO users (username, password, email, theme, bio, avatar, secret, verified)
VALUES ('$username', '$hash', '$email', '$theme', '$bio', '$avatar', '$secret', '0')";

if ($conn->query($sql) === TRUE) {
    echo 'Account created for ' . $username . '!';


$expires = date("Y-m-d H:i:s");


$tokenHash = urlencode(crypt(rand(), strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.')));
// Save token
$sql = "INSERT INTO tokens (type, hash, expires, status, username)
VALUES ('verify', '$tokenHash', '$expires', '0', '$username')";

if ($conn->query($sql) === TRUE) {


echo 'http://www.jokeswaps.com/parents.php?v=' . $tokenHash . '&username=' . $username;

 		$TokenID =	 mysqli_insert_id($conn);

		 
 		 $sql = "INSERT INTO emailQueue (TokenID, sent)
 			 VALUES ('$TokenID', '0')";
 		 if ($conn->query($sql) === TRUE) {
 			 ProcessMailQueue();
 		 } else {
 		     echo "Error: " . $sql . "<br>" . $conn->error;
 		 }



} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}


} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}


}
}

}

// Update form submit
if (isset($_POST['update']) && $_POST['update'] == "true")
{

$username = $_POST["username"];
 $username = mysqli_real_escape_string($conn, $username);
$email = $_POST["email"];
 $email = mysqli_real_escape_string($conn, $email);
$bio = $_POST["bio"];
 $bio = mysqli_real_escape_string($conn, $bio);
$secret = $_POST["secret"];
 $secret = mysqli_real_escape_string($conn, $secret);

$theme = $_POST["theme"];
$avatar = $_POST["avatar"];


$hash = crypt($_POST["password"], (sprintf("$2a$%02d$", 10) . strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.')));


// Save user
$sql = "UPDATE users SET password='$hash', email='$email', theme='$theme', bio='$bio', avatar='$avatar', secret='$secret' WHERE username='$username'";

if ($conn->query($sql) === TRUE) {
    echo 'Account updated for ' . $username . '!';
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}


}




// Login
if (isset($_POST['login']) && $_POST['login'] == "true")
{


$username = $_POST["username"];
 $username = mysqli_real_escape_string($conn, $username);

$sql = "SELECT * FROM users WHERE username = '$username'" ;
$result = $conn->query($sql);


for ($userInfo = array (); $row = $result->fetch_assoc(); $userInfo[] = $row);

$hash = $userInfo[0]["password"];

if ( hash_equals($hash, crypt($_POST["password"], $hash)) ) {

$signedIn = 'true';

echo 'You are signed in as the parent for user :' . $username;


}
else
{
echo 'Incorrect password';
}


}









?>





<HTML>
<HEAD>
<TITLE>
JokeSwaps - Parents Console
</TITLE>

<script src='https://www.google.com/recaptcha/api.js'></script>
<script>
function checkPasswdMatch(form)
{

if (form == "signup")
{

	if (signupForm.password.value == signupForm.password2.value)
	{
		document.getElementById("passwordMisMatch_signup").innerHTML = "";
	}
	else
	{
		document.getElementById("passwordMisMatch_signup").innerHTML = "Passwords do not match";
		signup.password.focus();
	}
}
else if (form == "update")
{

	if (updateForm.password.value == updateForm.password2.value)
	{
		document.getElementById("passwordMisMatch_update").innerHTML = "";
	}
	else
	{
		document.getElementById("passwordMisMatch_update").innerHTML = "Passwords do not match";
		update.password.focus();
	}
}
 


}
</script>
</HEAD>
<BODY>
<H1>This is the parents Console</H1>


<?PHP

if ($signedIn == 'true')
{

global $userInfo;

?>

Childs username (cannot be changed): <?PHP echo $userInfo[0]["username"]; ?><br />


Update settings<br />
<FORM METHOD="POST" ACTION="<?php echo $_SERVER['REQUEST_URI']?>" name="updateForm">
<input type="hidden" name="update" id="update" value="true">
<input type="hidden" name="username" id="username" value="<?PHP echo $userInfo[0]["username"]; ?>">
<label for="secret">Secret word: </label><input type="text" name="secret" id="secret" value="<?PHP echo $userInfo[0]["secret"]; ?>"  required="required"><br />
<label for="email">Parents email: </label><input type="email" name="email" id="email" value="<?PHP echo $userInfo[0]["email"]; ?>"  required="required"><br />
<label for="theme">Page theme: </label><input type="text" name="theme" id="theme" value="<?PHP echo $userInfo[0]["theme"]; ?>"><br />
<label for="bio">Childs bio: </label><input type="text" name="bio" id="bio" value="<?PHP echo $userInfo[0]["bio"]; ?>"><br />
Choose an avatar for your child<br />
<br /><br /><br /><br />
<label for="password">Parents password: </label><input type="password" name="password" id="password" required="required" onInput="checkPasswdMatch(update');"><br />
<label for="password2">Renter Password: </label><input type="password" name="password2" id="password2" required="required" onInput="checkPasswdMatch('update');"><span id="passwordMisMatch_update" style="color:red;"> </span><br />

<input type="submit" value="Save"><br />
</form>

<?PHP

$username = $userInfo[0]["username"];

// Print jokes on users page
$sql = "SELECT * FROM jokes WHERE forUser = '$username' ORDER BY id DESC" ;
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo '<FORM METHOD="POST" ACTION="' . $_SERVER['REQUEST_URI'] . '" name="editJokes">
		<input type="hidden" name="editJokes" id="editJokes" value="true">
		<input type="hidden" name="username" id="username" value="' . $userInfo[0]["username"] . '">
	<table>
	    <tr>
	      <th>From</th>
	      <th>Joke</th> 
	      <th>Answer</th>
	      <th>Delete</th>
	    </tr>';
	
	
	// output data of each row
    while($row = $result->fetch_assoc()) {
        echo '<tr><td><input type="text" name="fromName[' . $row["id"] . ']" value="' . $row["fromName"] . '" disabled="disabled"></td>';
        echo '<td><input type="text" name="joke[' . $row["id"] . ']" value="' . $row["joke"] . '"></td>';
        echo '<td><input type="text" name="answer[' . $row["id"] . ']" value="' . $row["answer"] . '"></td>';
        echo '<td><input type="checkbox" name="delete[' . $row["id"] . ']" value="true"></td></tr>';

    }
	
   echo ' <tr>
      <th>From</th>
      <th>Joke</th> 
      <th>Answer</th>
      <th>Delete</th>
    </tr>
		  
	</table>
		  <input type="submit" value="Save">
		  </form>';
				
} else {
    echo "No jokes yet";
}




}
else
{
?>

<FORM METHOD="POST" ACTION="<?php echo $_SERVER['REQUEST_URI']?>" name="login">
<STRONG>Login</STRONG><BR />
<input type="hidden" name="login" id="login" value="true">
<label for="username">Childs username: </label><input type="text" name="username" id="username" required="required" value="<?PHP if(empty($user)) {} elseif ($user == "www") {} elseif ($user == "jokeswaps") {} else { echo $user; } ?>"><br />
<label for="password">Parents password: </label><input type="password" name="password" id="password" required="required"><br />
<input type="submit" value="Login"><br />
</FORM>

<FORM METHOD="POST" ACTION="<?php echo $_SERVER['REQUEST_URI']?>" name="forgotPassword">
<STRONG>Forgotten Password</STRONG><BR />
<input type="hidden" name="forgotPassword" id="forgotPassword" value="true">
<label for="username">Childs username: </label><input type="text" name="username" id="username" required="required" value="<?PHP if(empty($user)) {} elseif ($user == "www") {} elseif ($user == "jokeswaps") {} else { echo $user; } ?>"><br />
<label for="email">Parents email: </label><input type="email" name="email" id="email" required="required"><br />
<input type="submit" value="Reset"><br />
</FORM>


<FORM METHOD="POST" ACTION="<?php echo $_SERVER['REQUEST_URI']?>" name="signupForm">
<STRONG>Signup</STRONG><BR />
<input type="hidden" name="signup" id="signup" value="true">
<label for="username">Childs username: </label><input type="text" name="username" id="username" required="required"><br />
<label for="secret">Secret word: </label><input type="text" name="secret" id="secret"><br />
<label for="password">Parents password: </label><input type="password" name="password" id="password" required="required" onInput="checkPasswdMatch('signup');"><br />
<label for="password2">Renter Password: </label><input type="password" name="password2" id="password2" required="required" onInput="checkPasswdMatch('signup');"><span id="passwordMisMatch_signup" style="color:red;"> </span><br />



<label for="email">Parents email: </label><input type="email" name="email" id="email" required="required"><br />

<label for="theme">Page theme: </label><input type="text" name="theme" id="theme"><br />

<label for="bio">Childs bio: </label><input type="text" name="bio" id="bio"><br />
Choose an avatar for your child<br />
<br /><br /><br /><br />

I declare I am a responsible adult, parent or guardian giving permission for this child to receive a JokeSwaps profile page.<br />
I declare I have discussed the site rules with the child, and they understand the rules.<br />
I declare I will be supervising the child when using this website.<br />
I have read the rules and privacy policy for this website.<br />

<div class="g-recaptcha" data-sitekey="6Le44QQTAAAAAEy-cOwETZWZ9cKBNZZAhQP4d91C"></div>

<input type="submit" value="Signup"><br />
</FORM>

<?PHP
}

$conn->close();
?>


</BODY>
</HTML>