<?PHP
// Scheduler plugin 

require_once('mail.php');
$servername = "localhost";
 $username = "root";
$password = "YmQCl60qMwe2YpUn34k7";
$dbname = "scheduler";
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname );
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user = strtolower(mysqli_real_escape_string($conn, $_GET['user']));
// Verify email address
if (isset($_GET['v']) && isset($_GET['username']))
{
	$username = strtolower(mysqli_real_escape_string($conn, $_GET["username"]));
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
	$username = strtolower(mysqli_real_escape_string($conn, $_GET["username"]));
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
		
		 $username = strtolower(mysqli_real_escape_string($conn, $_POST["username"]));
		$email = $_POST["email"];
		 $email = mysqli_real_escape_string($conn, $email);
		
		 // Check username and email match
		 $sql = "SELECT * FROM users WHERE username = '$username'" ;
		 $result = $conn->query($sql);
		 
		 for ($userInfo = array (); $row = $result->fetch_assoc(); $userInfo[] = $row);
		 if ($userInfo[0]["email"] == $email)
		 {
		
			 $dtz = new DateTimeZone("UTC"); //Your timezone
			 $now = new DateTime(date("Y-m-d"), $dtz);
			 $expires = $now->modify('+4 days');
			 $expires = $expires->format("Y-m-d H:i:s");
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
	$username = strtolower(mysqli_real_escape_string($conn, $_POST["username"]));
$email = $_POST["email"];
 $email = mysqli_real_escape_string($conn, $email);
$bio = $_POST["bio"];
 $bio = mysqli_real_escape_string($conn, $bio);
 	$secret = strtolower(mysqli_real_escape_string($conn, $_POST["secret"]));
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
	$dtz = new DateTimeZone("UTC"); //Your timezone
	$now = new DateTime(date("Y-m-d"), $dtz);
	$expires = $now->modify('+4 days');
	$expires = $expires->format("Y-m-d H:i:s");
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
$session = $_POST["session"];
 $session = mysqli_real_escape_string($conn, $session);
$theme = $_POST["theme"];
$avatar = $_POST["avatar"];
$hash = crypt($_POST["password"], (sprintf("$2a$%02d$", 10) . strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.')));
// Check session token
$sql = "SELECT * FROM tokens WHERE username = '$username' AND type = 'login' AND status = '0' ORDER BY TokenID DESC " ;
$result = $conn->query($sql);
for ($userInfo = array (); $row = $result->fetch_assoc(); $userInfo[] = $row);
$tokeHash = $userInfo[0]["hash"];
$tokenExpires = $userInfo[0]["expires"];
$tokenStatus = $userInfo[0]["status"];
if ($tokeHash == $session)
{
if ($tokenExpires < date("now"))
{
// Save user
$sql = "UPDATE users SET password='$hash', email='$email', theme='$theme', bio='$bio', avatar='$avatar', secret='$secret' WHERE username='$username'";
if ($conn->query($sql) === TRUE) {
    echo 'Account updated for ' . $username . '!';
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}
$signedIn = 'true';
$tokenHash = $tokeHash;
$sql = "SELECT * FROM users WHERE username = '$username'" ;
$result = $conn->query($sql);
for ($userInfo = array (); $row = $result->fetch_assoc(); $userInfo[] = $row);
echo 'You are signed in as the parent for user :' . $username . '<BR />';
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
// Logout
if (isset($_POST['logout']) && $_POST['logout'] == "true")
{
	$username = $_POST["username"];
	 $username = mysqli_real_escape_string($conn, $username);
	 
	 $sql = "UPDATE tokens SET status = '1' WHERE username = '$username' AND type = 'login'";
	 if ($conn->query($sql) === TRUE) {
		 echo 'Signout success';
		 $signedIn = 'false';
	 }
	 
	 
}
// Login
if (isset($_POST['login']) && $_POST['login'] == "true")
{
	$username = strtolower(mysqli_real_escape_string($conn, $_POST["username"]));
$sql = "SELECT * FROM users WHERE username = '$username'" ;
$result = $conn->query($sql);
for ($userInfo = array (); $row = $result->fetch_assoc(); $userInfo[] = $row);
$hash = $userInfo[0]["password"];
if ( hash_equals($hash, crypt($_POST["password"], $hash)) ) {
$signedIn = 'true';
// Create login token
$tokenHash = urlencode(crypt(rand(), strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.')));
$dtz = new DateTimeZone("UTC"); //Your timezone
$now = new DateTime(date("Y-m-d"), $dtz);
$expires = $now->modify('+4 days');
$expires = $expires->format("Y-m-d H:i:s");
 // Save token
 $sql = "INSERT INTO tokens (type, hash, expires, status, username)
 VALUES ('login', '$tokenHash', '$expires', '0', '$username')";
 if ($conn->query($sql) === TRUE) {
echo 'You are signed in as the parent for user :' . $username . '<BR />';
}
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
Scheduler workspace
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
<DIV NAME="CONTENT" STYLE="min-height:100%;margin-bottom:-100px;">
<H1> Scheduler Plugin - workspace</H1>
<BR />
<BR />

<?PHP
if ($signedIn == 'true')
{
global $userInfo;
?>

Username (cannot be changed): <?PHP echo $userInfo[0]["username"]; ?><br />


Update settings<br />
<FORM METHOD="POST" ACTION="<?php echo $_SERVER['REQUEST_URI']?>" name="updateForm">

<input type="hidden" name="session" id="session" value="<?PHP echo $tokenHash; ?>">
<input type="hidden" name="update" id="update" value="true">
<input type="hidden" name="username" id="username" value="<?PHP echo $userInfo[0]["username"]; ?>">
<label for="email">Email: </label><input type="email" name="email" id="email" value="<?PHP echo $userInfo[0]["email"]; ?>"  required="required"><br />
<label for="bio">Description: </label><input type="text" name="bio" id="bio" value="<?PHP echo $userInfo[0]["bio"]; ?>"><br />

	

<br />
<label for="password">Password: </label><input type="password" name="password" id="password" required="required" onInput="checkPasswdMatch(update');"><br />
<label for="password2">Renter Password: </label><input type="password" name="password2" id="password2" required="required" onInput="checkPasswdMatch('update');"><span id="passwordMisMatch_update" style="color:red;"> </span><br />

<input type="submit" value="Save"><br />
</form>

<?PHP
$username = $userInfo[0]["username"];


// Logout
echo '
<FORM METHOD="POST" ACTION="' . $_SERVER['REQUEST_URI'] . '" name="logout">
<input type="hidden" name="logout" id="logout" value="true">
<input type="hidden" name="username" id="username" value="' . $userInfo[0]["username"] . '">
<input type="hidden" name="session" id="session" value="' . $tokenHash . '">
<input type="submit" value="Logout"><br />
</FORM>
';
}
else
{
?>

<FORM METHOD="POST" ACTION="<?php echo $_SERVER['REQUEST_URI']?>" name="login">
<STRONG>Login</STRONG><BR />
<input type="hidden" name="login" id="login" value="true">
<label for="username">Username: </label><input type="text" name="username" id="username" required="required" value="<?PHP if(empty($user)) {} elseif ($user == "www") {} elseif ($user == "jokeswaps") {} else { echo $user; } ?>"><br />
<label for="password">Password: </label><input type="password" name="password" id="password" required="required"><br />
<input type="submit" value="Login"><br />
</FORM>

<FORM METHOD="POST" ACTION="<?php echo $_SERVER['REQUEST_URI']?>" name="forgotPassword">
<STRONG>Forgotten Password</STRONG><BR />
<input type="hidden" name="forgotPassword" id="forgotPassword" value="true">
<label for="username">Username: </label><input type="text" name="username" id="username" required="required" value="<?PHP if(empty($user)) {} elseif ($user == "www") {} elseif ($user == "jokeswaps") {} else { echo $user; } ?>"><br />
<label for="email">Email: </label><input type="email" name="email" id="email" required="required"><br />
<input type="submit" value="Reset"><br />
</FORM>


<FORM METHOD="POST" ACTION="<?php echo $_SERVER['REQUEST_URI']?>" name="signupForm">
<STRONG>Signup</STRONG><BR />
<input type="hidden" name="signup" id="signup" value="true">
<label for="username">Username: </label><input type="text" name="username" id="username" required="required"><br />
<label for="password">Password: </label><input type="password" name="password" id="password" required="required" onInput="checkPasswdMatch('signup');"><br />
<label for="password2">Renter Password: </label><input type="password" name="password2" id="password2" required="required" onInput="checkPasswdMatch('signup');"><span id="passwordMisMatch_signup" style="color:red;"> </span><br />
<label for="email">Email Address: </label><input type="email" name="email" id="email" required="required"><br />

	

<br />

I have read, understand and agree with the <a href="tac.php">Terms and Conditions</a> and <a href="privacy.php">Privacy Policy</a> for this website.<br />
By clicking the 'Signup' button below you are agreeing with these statements.<br />

<div class="g-recaptcha" data-sitekey="6Le44QQTAAAAAEy-cOwETZWZ9cKBNZZAhQP4d91C"></div>

<input type="submit" value="Signup"><br />
</FORM>

<?PHP
}
?>

</DIV>
<DIV NAME="FOOTER" STYLE="height:100px;background:#000;color:#FFF;text-align:center;">Matt Watt 2016 - GPLv3 copyleft</DIV>



</BODY>
</HTML>
