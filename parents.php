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


$user = strtolower(mysqli_real_escape_string($conn, substr($_SERVER['HTTP_HOST'], 0, strpos($_SERVER['HTTP_HOST'], "."))));


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

	// edit and delete jokes
	if (isset($_POST['editJokes']) && $_POST['editJokes'] == "true")
		{

			$forUser = mysqli_real_escape_string($conn, $_POST['username']);
		
			// Check session token


			$sql = "SELECT * FROM tokens WHERE username = '$forUser' AND type = 'login' AND status = '0' ORDER BY TokenID DESC " ;
			$result = $conn->query($sql);

			for ($userInfo = array (); $row = $result->fetch_assoc(); $userInfo[] = $row);
			$tokeHash = $userInfo[0]["hash"];
			$tokenExpires = $userInfo[0]["expires"];
			$tokenStatus = $userInfo[0]["status"];
		

			$session = $_POST["session"];
			 $session = mysqli_real_escape_string($conn, $session);

			if ($tokeHash == $session)
			{

				if ($tokenExpires < date("now"))
				{
		
		
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
		
			foreach ($_POST['type'] as $id => $type)
			{
			
				 $type = mysqli_real_escape_string($conn, $type);

				// Update joke
				$sql = "UPDATE jokes SET type= '$type' WHERE id= '$id' AND forUser = '$forUser'";
		
				if ($conn->query($sql) === TRUE) {
				} else {
				    echo "Error: " . $sql . "<br>" . $conn->error;
				}	
			}
		
			foreach ($_POST['delete'] as $id => $delete)
			{
				if (isset($delete) && $delete == 'true')
				{

				// Update joke
				$sql = "DELETE FROM jokes WHERE id= '$id' AND forUser = '$forUser'";
		
				if ($conn->query($sql) === TRUE) {
				} else {
				    echo "Error: " . $sql . "<br>" . $conn->error;
				}	
				}
			}
			
		
			echo 'Jokes updated';
		
		
			$signedIn = 'true';
			$tokenHash = $tokeHash;

			$sql = "SELECT * FROM users WHERE username = '$forUser'" ;
			$result = $conn->query($sql);


			for ($userInfo = array (); $row = $result->fetch_assoc(); $userInfo[] = $row);
		
			echo 'You are signed in as the parent for user :' . $forUser . '<BR />';


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

// Add new ring
if (isset($_POST['addRing']) && $_POST['addRing'] == "true")
{
	
	$forUser = mysqli_real_escape_string($conn, $_POST['username']);
	
	// Check session token


	$sql = "SELECT * FROM tokens WHERE username = '$forUser' AND type = 'login' AND status = '0' ORDER BY TokenID DESC " ;
	$result = $conn->query($sql);

	for ($userInfo = array (); $row = $result->fetch_assoc(); $userInfo[] = $row);
	$tokeHash = $userInfo[0]["hash"];
	$tokenExpires = $userInfo[0]["expires"];
	$tokenStatus = $userInfo[0]["status"];
	

	$session = $_POST["session"];
	 $session = mysqli_real_escape_string($conn, $session);

	if ($tokeHash == $session)
	{

	if ($tokenExpires < date("now"))
	{
		
		$ringName = mysqli_real_escape_string($conn, $_POST['ringname']);
		$owner = $forUser;
		$ringDesc = mysqli_real_escape_string($conn, $_POST['ringdesc']);
		$ringSecret = mysqli_real_escape_string($conn, $_POST['secret']);
		
		// Check ring name availability
		$sql = "SELECT * FROM ringInfo WHERE name = '$ringName'" ;
		$result = $conn->query($sql);
		$count = $result->num_rows;

		if ($count > 0)
		{
		echo 'Ring name already taken, please try a new ring name';

		//Need to add a prefill with other entered data

		}
		else
		{
		
	 $sql = "INSERT INTO ringInfo (name, shortDesc, owner, secret)
		 VALUES ('$ringName', '$ringDesc', '$owner', '$ringSecret')";
	 if ($conn->query($sql) === TRUE) {
		 echo 'New ring ' . $ringName . ' has been created.';
		 
		 
	 	$sql = "SELECT * FROM ringInfo WHERE name = '$ringName'" ;
	 	$result = $conn->query($sql);

	 	for ($ringInfo = array (); $row = $result->fetch_assoc(); $ringInfo[] = $row);
		 $RingID = $ringInfo[0]["RingID"];
		 
		 $sql = "INSERT INTO rings (RingID, username)
			 VALUES ('$RingID', '$owner')";
		 if ($conn->query($sql) === TRUE) {
			 echo 'User ' . $owner . ' has been added to the ring ' . $ringName . '.';
		 } else {
		     echo "Error: " . $sql . "<br>" . $conn->error;
		 }
		 
	 } else {
	     echo "Error: " . $sql . "<br>" . $conn->error;
	 }
	 

	 
 }
	
$signedIn = 'true';
$tokenHash = $tokeHash;

$sql = "SELECT * FROM users WHERE username = '$forUser'" ;
$result = $conn->query($sql);


for ($userInfo = array (); $row = $result->fetch_assoc(); $userInfo[] = $row);

echo 'You are signed in as the parent for user :' . $forUser . '<BR />';
 
		
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

// Edit rings
if (isset($_POST['editRing']) && $_POST['editRing'] == "true")
	{ 
	
		$forUser = mysqli_real_escape_string($conn, $_POST['username']);
	
		// Check session token


		$sql = "SELECT * FROM tokens WHERE username = '$forUser' AND type = 'login' AND status = '0' ORDER BY TokenID DESC " ;
		$result = $conn->query($sql);

		for ($userInfo = array (); $row = $result->fetch_assoc(); $userInfo[] = $row);
		$tokeHash = $userInfo[0]["hash"];
		$tokenExpires = $userInfo[0]["expires"];
		$tokenStatus = $userInfo[0]["status"];
	

		$session = $_POST["session"];
		 $session = mysqli_real_escape_string($conn, $session);

		if ($tokeHash == $session)
		{

		if ($tokenExpires < date("now"))
		{
			
			foreach ($_POST['ringname'] as $id => $ringname)
			{
			 $ringname = mysqli_real_escape_string($conn, $ringname);

			$sql = "UPDATE ringInfo SET name = '$ringname' WHERE RingID = '$id' AND owner = '$forUser'";
	
			if ($conn->query($sql) === TRUE) {
			} else {
			    echo "Error: " . $sql . "<br>" . $conn->error;
			}	
			
			}
			foreach ($_POST['ringdesc'] as $id => $ringdesc)
			{
   			 $ringdesc = mysqli_real_escape_string($conn, $ringdesc);

   			$sql = "UPDATE ringInfo SET shortDesc = '$ringdesc' WHERE RingID = '$id' AND owner = '$forUser'";
	
   			if ($conn->query($sql) === TRUE) {
   			} else {
   			    echo "Error: " . $sql . "<br>" . $conn->error;
   			}	
			}
			foreach ($_POST['secret'] as $id => $secret)
			{
   			 $secret = mysqli_real_escape_string($conn, $secret);

   			$sql = "UPDATE ringInfo SET secret = '$secret' WHERE RingID = '$id' AND owner = '$forUser'";
	
   			if ($conn->query($sql) === TRUE) {
   			} else {
   			    echo "Error: " . $sql . "<br>" . $conn->error;
   			}	
			}
			foreach ($_POST['users'] as $id => $users)
			{
				// Clear all entries for this ring
				$sql = "DELETE FROM rings WHERE RingID = '$id'";
				if ($conn->query($sql) === TRUE) {
				} else {
				    echo "Error: " . $sql . "<br>" . $conn->error;
				}
				
				// Add owner by default (incase deleted)
				$sql = "INSERT INTO rings (RingID, username)
							 VALUES ('$id', '$forUser')";
						 if ($conn->query($sql) === TRUE) {
						 } else {
						     echo "Error: " . $sql . "<br>" . $conn->error;
						 }
				
				// Users to array
				$userList = explode(PHP_EOL, $users);
				foreach ($userList as $username)
				{
					if ($username == $forUser)
					{
						// Do nothing as owenr added by default
					}
					else
					{
						$username = str_replace(array("\n", "\r", "\r\n", ","), '', $username);
						
						$sql = "INSERT INTO rings (RingID, username)
									 VALUES ('$id', '$username')";
								 if ($conn->query($sql) === TRUE) {
								 } else {
								     echo "Error: " . $sql . "<br>" . $conn->error;
								 }
						
					}
				}
				
				foreach ($_POST['delete'] as $id => $delete)
				{
					if (isset($delete) && $delete == 'true')
					{

					// Delete ring info
					$sql = "DELETE FROM ringInfo WHERE RingID= '$id' AND owner = '$forUser'";
		
					if ($conn->query($sql) === TRUE) {

					
					// Delete all ring and user pairs - only if aboce delete success
					$sql = "DELETE FROM rings WHERE RingID= '$id'";
		
					if ($conn->query($sql) === TRUE) {
					} else {
					    echo "Error: " . $sql . "<br>" . $conn->error;
					}	
					} else {
				    echo "Error: " . $sql . "<br>" . $conn->error;
					}	
					
					}
					
					
				}
				
			}
	
	
			// Stay signed in
			$signedIn = 'true';
			$tokenHash = $tokeHash;

			$sql = "SELECT * FROM users WHERE username = '$forUser'" ;
			$result = $conn->query($sql);


			for ($userInfo = array (); $row = $result->fetch_assoc(); $userInfo[] = $row);

			echo 'You are signed in as the parent for user :' . $forUser . '<BR />';
 
		
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
JokeSwaps - Parents Console
</TITLE>

    <meta charset="utf-8" />
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link rel="stylesheet" href="css/960_16_col.css" />
    <link rel="stylesheet" href="css/reset.css" />
    <link rel="stylesheet" href="css/text.css" />
    <link rel="stylesheet" href="css/mainstyle.css" />
	
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

	</head>


	<body>
	    <div id="wrapper" class="container_16">
	        <!--Start of body wrraper-->
	        <header class="container_16">
	            <!--Start of Header-->
	            <div id="logo">
	                <img src="images/logo_blue.png" class="logo" />
	            </div>
	            <div id="hdr_img">

	                <img src="images/header-img.png" />

	            </div>

	        </header>
	        <!--End of Header-->


<H1>This is the parents Console</H1>


<?PHP

if ($signedIn == 'true')
{

global $userInfo;

?>

Childs username (cannot be changed): <?PHP echo $userInfo[0]["username"]; ?><br />


Update settings<br />
<FORM METHOD="POST" ACTION="<?php echo $_SERVER['REQUEST_URI']?>" name="updateForm">

<input type="hidden" name="session" id="session" value="<?PHP echo $tokenHash; ?>">
<input type="hidden" name="update" id="update" value="true">
<input type="hidden" name="username" id="username" value="<?PHP echo $userInfo[0]["username"]; ?>">
<label for="secret">Secret word: </label><input type="text" name="secret" id="secret" value="<?PHP echo $userInfo[0]["secret"]; ?>"  required="required"><br />
<label for="email">Parents email: </label><input type="email" name="email" id="email" value="<?PHP echo $userInfo[0]["email"]; ?>"  required="required"><br />
<label for="bio">Childs bio: </label><input type="text" name="bio" id="bio" value="<?PHP echo $userInfo[0]["bio"]; ?>"><br />
<label for="theme">Page theme: </label><br />
<table id="avatarSelect">
	<TR>
		<td><label><input type="radio" name="theme" value="blue" <?PHP if($userInfo[0]["theme"] == 'blue') {echo 'checked="checked"';} ?>/><img src="/images/theme_blue.png" style="width:200px"></label></td>
		<td><label><input type="radio" name="theme" value="pink" <?PHP if($userInfo[0]["theme"] == 'pink') {echo 'checked="checked"';} ?>/><img src="/images/theme_pink.png" style="width:200px"></label></td>
		<td><label><input type="radio" name="theme" value="purple" <?PHP if($userInfo[0]["theme"] == 'purple') {echo 'checked="checked"';} ?>/><img src="/images/theme_purple.png" style="width:200px"></label></td>
		<TR>
</table>	

<label for="bio">Choose an avatar for your child: </label><br />

<table id="avatarSelect">
	<TR>
		<td><label><input type="radio" name="avatar" value="01" <?PHP if($userInfo[0]["avatar"] == '01') {echo 'checked="checked"';} ?>/><img src="/images/avatars/01.png" style="width:200px"></label></td>
		<td><label><input type="radio" name="avatar" value="02" <?PHP if($userInfo[0]["avatar"] == '02') {echo 'checked="checked"';} ?>/><img src="/images/avatars/02.png" style="width:200px"></label></td>
		<td><label><input type="radio" name="avatar" value="03" <?PHP if($userInfo[0]["avatar"] == '03') {echo 'checked="checked"';} ?>/><img src="/images/avatars/03.png" style="width:200px"></label></td>
		<td><label><input type="radio" name="avatar" value="04" <?PHP if($userInfo[0]["avatar"] == '04') {echo 'checked="checked"';} ?>/><img src="/images/avatars/04.png" style="width:200px"></label></td>
		<td><label><input type="radio" name="avatar" value="05" <?PHP if($userInfo[0]["avatar"] == '05') {echo 'checked="checked"';} ?>/><img src="/images/avatars/05.png" style="width:200px"></label></td>
	</TR>
	<TR>
		<td><label><input type="radio" name="avatar" value="06" <?PHP if($userInfo[0]["avatar"] == '06') {echo 'checked="checked"';} ?>/><img src="/images/avatars/06.png" style="width:200px"></label></td>
		<td><label><input type="radio" name="avatar" value="07" <?PHP if($userInfo[0]["avatar"] == '07') {echo 'checked="checked"';} ?>/><img src="/images/avatars/07.png" style="width:200px"></label></td>
		<td><label><input type="radio" name="avatar" value="08" <?PHP if($userInfo[0]["avatar"] == '08') {echo 'checked="checked"';} ?>/><img src="/images/avatars/08.png" style="width:200px"></label></td>
		<td><label><input type="radio" name="avatar" value="09" <?PHP if($userInfo[0]["avatar"] == '09') {echo 'checked="checked"';} ?>/><img src="/images/avatars/09.png" style="width:200px"></label></td>
		<td><label><input type="radio" name="avatar" value="10" <?PHP if($userInfo[0]["avatar"] == '10') {echo 'checked="checked"';} ?>/><img src="/images/avatars/10.png" style="width:200px"></label></td>
	</TR>
	<TR>
		<td><label><input type="radio" name="avatar" value="11" <?PHP if($userInfo[0]["avatar"] == '11') {echo 'checked="checked"';} ?>/><img src="/images/avatars/11.png" style="width:200px"></label></td>
		<td><label><input type="radio" name="avatar" value="12" <?PHP if($userInfo[0]["avatar"] == '12') {echo 'checked="checked"';} ?>/><img src="/images/avatars/12.png" style="width:200px"></label></td>
		<td><label><input type="radio" name="avatar" value="13" <?PHP if($userInfo[0]["avatar"] == '13') {echo 'checked="checked"';} ?>/><img src="/images/avatars/13.png" style="width:200px"></label></td>
		<td><label><input type="radio" name="avatar" value="14" <?PHP if($userInfo[0]["avatar"] == '14') {echo 'checked="checked"';} ?>/><img src="/images/avatars/14.png" style="width:200px"></label></td>
		<td><label><input type="radio" name="avatar" value="15" <?PHP if($userInfo[0]["avatar"] == '15') {echo 'checked="checked"';} ?>/><img src="/images/avatars/15.png" style="width:200px"></label></td>
		</TR>
</table>	

<br />
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
    echo '	<H1>Edit Jokes</H1>
		<FORM METHOD="POST" ACTION="' . $_SERVER['REQUEST_URI'] . '" name="editJokes">
		<input type="hidden" name="editJokes" id="editJokes" value="true">
		<input type="hidden" name="username" id="username" value="' . $userInfo[0]["username"] . '">
		<input type="hidden" name="session" id="session" value="' . $tokenHash . '">
	<table>
	    <tr>
	      <th>From</th>
	      <th>Joke</th> 
	      <th>Answer</th>
	      <th>Type</th>
	      <th>Delete</th>
	    </tr>';
	
	
	// output data of each row
    while($row = $result->fetch_assoc()) {
        echo '<tr><td><input type="text" name="fromName[' . $row["id"] . ']" value="' . $row["fromName"] . '" disabled="disabled"></td>';
        echo '<td><input type="text" name="joke[' . $row["id"] . ']" value="' . $row["joke"] . '"></td>';
        echo '<td><input type="text" name="answer[' . $row["id"] . ']" value="' . $row["answer"] . '"></td>';
        echo '<td><input type="text" name="type[' . $row["id"] . ']" value="' . $row["type"] . '"></td>';
        echo '<td><input type="checkbox" name="delete[' . $row["id"] . ']" value="true"></td></tr>';

    }
	
   echo ' <tr>
      <th>From</th>
      <th>Joke</th> 
      <th>Answer</th>
	  <th>Type</th>
      <th>Delete</th>
    </tr>
		  
	</table>
		  <input type="submit" value="Save">
		  </form>';
				
} else {
    echo "No jokes yet";
}



echo '
	<H1>Rings your child belongs to: </H1>
<ul>Currently users can be added to a ring without their consent</ul>';

$sql = "SELECT * FROM rings WHERE username = '$username'" ;
$result = $conn->query($sql);

if ($result->num_rows > 0) {
	
	echo '<FORM METHOD="POST" ACTION="' . $_SERVER['REQUEST_URI'] . '" name="editRing">
	<input type="hidden" name="editRing" id="editRing" value="true">
	<input type="hidden" name="username" id="username" value="' . $userInfo[0]["username"] . '">
	<input type="hidden" name="session" id="session" value="' . $tokenHash . '">
<table>
    <tr>
      <th>Ring Name</th>
      <th>Desc</th> 
      <th>Secret</th>
      <th>Users</th>
      <th>Delete Ring</th>
    </tr>';
	
while($row = $result->fetch_assoc()) {
	$ringID = $row["RingID"];
	$sql = "SELECT * FROM ringInfo WHERE RingID = '$ringID'" ;
	$result = $conn->query($sql);
	for ($ringInfo = array (); $ringInforow = $result->fetch_assoc(); $ringInfo[] = $ringInforow);
	
	$sql = "SELECT * FROM rings WHERE RingID = '$ringID'" ;
	$result = $conn->query($sql);
	$userList = "";
	while($usersrow = $result->fetch_assoc()) {
		$userList .= $usersrow["username"] . "&#13;&#10;";
	}
	if ($ringInfo[0]["owner"] == $username)
		{ 
			$ringOwner = "true";
		}
		else
		{
			$ringOwner = "false";
		}
	
    echo '<tr><td><input type="text" name="ringname[' . $ringID . ']" value="' . $ringInfo[0]["name"] . '"';
		if ($ringOwner == 'false')
	{
		echo ' disabled="disabled"';
	} 
	echo '></td>';
    echo '<td><input type="text" name="ringdesc[' . $ringID . ']" value="' . $ringInfo[0]["shortDesc"] . '"';
		if ($ringOwner == 'false')
	{
		echo ' disabled="disabled"';
	} 
	echo '></td>';
    echo '<td><input type="text" name="secret[' . $ringID . ']" value="' . $ringInfo[0]["secret"] . '"';
		if ($ringOwner == 'false')
	{
		echo ' disabled="disabled"';
	} 
	echo '></td>';
    echo '<td><textarea name="users[' . $ringID . ']"';
		if ($ringOwner == 'false')
	{
		echo ' disabled="disabled"';
	} 
	echo '>' . $userList . '</textarea></td>';
    echo '<td><input type="checkbox" name="delete[' . $ringID . ']" value="true"';
		if ($ringOwner == 'false')
	{
		echo ' disabled="disabled"';
	} 
	echo '></td></tr>';
	
}
echo '<tr>
  <th>Ring Name</th>
  <th>Desc</th> 
  <th>Secret</th>
  <th>Users</th>
  <th>Delete Ring</th>
</tr>
	  
</table>
	  <input type="submit" value="Save">
	  </form>';

}
else
	{ echo 'Your child does not currently belong to any rings';}

// add to existing ring
echo '
	<H1>Request addition to existing ring</H1>
<ul>This feature has not been enabled yet</ul>
<FORM METHOD="POST" ACTION="' . $_SERVER['REQUEST_URI'] . '" name="requestRing">
<input type="hidden" name="requestRing" id="requestRing" value="true">
<input type="hidden" name="username" id="username" value="' . $userInfo[0]["username"] . '">
<input type="hidden" name="session" id="session" value="' . $tokenHash . '">
	<label for="ringname">Ring Name: </label><input type="text" name="ringname" id="ringname" required="required"><br />
	<input type="submit" value="Request">
	</FORM>';


// create new ring
echo '
	<H1>Create a new ring</H1>
<FORM METHOD="POST" ACTION="' . $_SERVER['REQUEST_URI'] . '" name="addRing">
		<input type="hidden" name="addRing" id="addRing" value="true">
		<input type="hidden" name="username" id="username" value="' . $userInfo[0]["username"] . '">
		<input type="hidden" name="session" id="session" value="' . $tokenHash . '">

			<label for="ringname">Ring Name: </label><input type="text" name="ringname" id="ringname" required="required"><br />
			<label for="username">Ring Short Description: </label><input type="text" name="ringdesc" id="ringdesc" required="required"><br />
			<label for="secret">Ring Secret word: </label><input type="text" name="secret" id="secret"><br />
			<input type="submit" value="Add Ring">
			</FORM>';


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

<label for="bio">Childs bio: </label><input type="text" name="bio" id="bio"><br />
<label for="theme">Page theme: </label><br />
<table id="avatarSelect">
	<TR>
		<td><label><input type="radio" name="theme" value="blue" /><img src="/images/theme_blue.png" style="width:200px"></label></td>
		<td><label><input type="radio" name="theme" value="pink" /><img src="/images/theme_pink.png" style="width:200px"></label></td>
		<td><label><input type="radio" name="theme" value="purple" /><img src="/images/theme_purple.png" style="width:200px"></label></td>
		<TR>
</table>	
<label for="bio">Choose an avatar for your child: </label><br />

<table id="avatarSelect">
	<TR>
		<td><label><input type="radio" name="avatar" value="01" /><img src="/images/avatars/01.png" style="width:200px"></label></td>
		<td><label><input type="radio" name="avatar" value="02" /><img src="/images/avatars/02.png" style="width:200px"></label></td>
		<td><label><input type="radio" name="avatar" value="03" /><img src="/images/avatars/03.png" style="width:200px"></label></td>
		<td><label><input type="radio" name="avatar" value="04" /><img src="/images/avatars/04.png" style="width:200px"></label></td>
		<td><label><input type="radio" name="avatar" value="05" /><img src="/images/avatars/05.png" style="width:200px"></label></td>
	</TR>
	<TR>
		<td><label><input type="radio" name="avatar" value="06" /><img src="/images/avatars/06.png" style="width:200px"></label></td>
		<td><label><input type="radio" name="avatar" value="07" /><img src="/images/avatars/07.png" style="width:200px"></label></td>
		<td><label><input type="radio" name="avatar" value="08" /><img src="/images/avatars/08.png" style="width:200px"></label></td>
		<td><label><input type="radio" name="avatar" value="09" /><img src="/images/avatars/09.png" style="width:200px"></label></td>
		<td><label><input type="radio" name="avatar" value="10" /><img src="/images/avatars/10.png" style="width:200px"></label></td>
	</TR>
	<TR>
		<td><label><input type="radio" name="avatar" value="11" /><img src="/images/avatars/11.png" style="width:200px"></label></td>
		<td><label><input type="radio" name="avatar" value="12" /><img src="/images/avatars/12.png" style="width:200px"></label></td>
		<td><label><input type="radio" name="avatar" value="13" /><img src="/images/avatars/13.png" style="width:200px"></label></td>
		<td><label><input type="radio" name="avatar" value="14" /><img src="/images/avatars/14.png" style="width:200px"></label></td>
		<td><label><input type="radio" name="avatar" value="15" /><img src="/images/avatars/15.png" style="width:200px"></label></td>
		</TR>
</table>	

<br />

I am a responsible adult, parent or guardian giving permission for this child to receive a JokeSwaps profile page.<br />
I have discussed the site rules with the child, and they understand the rules.<br />
I will be supervising the child when using this website.<br />
I have read, understand and agree with the rules, <a href="tac.php">Terms and Conditions</a> and <a href="privacy.php">Privacy Policy</a> for this website.<br />
By clicking the 'Signup' button below you are agreeing with these statements.<br />

<div class="g-recaptcha" data-sitekey="6Le44QQTAAAAAEy-cOwETZWZ9cKBNZZAhQP4d91C"></div>

<input type="submit" value="Signup"><br />
</FORM>

<?PHP
}

$conn->close();
?>


	    </div>
	    <!--End of Body wrapper-->
	    <footer id="footerBg">
	        <form id="contactForm" class="container_16">

	            <div class="prefix_8 grid_3 alpha label">

	                <label for="name" class="grid_3">Name - </label>

	                <label for="email" class="grid_3">E-mail - </label>

	                <label for="Message" class="grid_3">Message - </label>

	            </div>


	            <div class="grid_5 omega inputs">

	                <input type="text" name="name" class="grid_5" id="contactName" />

	                <input type="email" name="email" class="grid_5" id="contactEmail" />

	                <textarea name="message" id="contactMessage" cols="30" rows="8"></textarea>
	                <a href="#"><h1 class="prefix_3 grid_2" id="contactSubmit">GO!</h1></a>

	            </div>


	        </form>


	    </footer>
		
		<div style="width:100%;">
		About: When the 7 year old twins Tammi and Mahni created their first web pages, they were quickly dissapointed to discover that only they could add jokes to their personal pages, and not to their sisters page. They needed a 'JokeSpace' or 'JokeTime' - a social media site allowing them to swap jokes, a few tips from dad and they were coding away creating a SQL database and writting up a php script to save and display the jokes. Ofcourse the dinner table discussion that night quickly turned into a discussion of the sites rules and terms and conditions, which will be also written by kids for kids. Visit back regularly to see the site progress as they choose their style for the site and hopefully one day open it for parents to register their kids.<BR />

		<a href="tac.php">Terms and Conditions</a> - <a href="privacy.php">Privacy Policy</a> - <a href="parents.php">Parents Console</a><BR />


		<a rel="license" href="http://creativecommons.org/licenses/by-sa/4.0/"><img alt="Creative Commons License" style="border-width:0" src="https://i.creativecommons.org/l/by-sa/4.0/88x31.png" /></a><br /><span xmlns:dct="http://purl.org/dc/terms/" property="dct:title">JokeSwaps</span> by <a xmlns:cc="http://creativecommons.org/ns#" href="https://github.com/karwalski/jokeswaps" property="cc:attributionName" rel="cc:attributionURL">Karwalski</a> is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by-sa/4.0/">Creative Commons Attribution-ShareAlike 4.0 International License</a>.
		</div>
		
	</body>

	</html>