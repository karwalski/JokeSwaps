<?PHP



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

// Signup form submit
if (isset($_GET['signup']) && $_GET['signup'] == "true")
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


echo 'original jsonesque: ' . $response.success;

    if($response["success"] == "false")
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

// Save user
$sql = "INSERT INTO users (username, password, email, theme, bio, avatar, secret, verified)
VALUES ('$username', '$hash', '$email', '$theme', '$bio', '$avatar', '$secret', '0')";

if ($conn->query($sql) === TRUE) {
    echo 'Account created for ' . $username . '!';
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}


}
}



// Update form submit
if (isset($_GET['update']) && $_GET['update'] == "true")
{

$username = $_GET["username"];
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
if (isset($_GET['login']) && $_GET['login'] == "true")
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
<FORM METHOD="POST" ACTION="?update=true&username=<?PHP echo $userInfo[0]["username"]; ?>">
<label for="secret">Secret word: </label><input type="text" name="secret" id="secret" value="<?PHP echo $userInfo[0]["secret"]; ?>"><br />
<label for="email">Parents email: </label><input type="email" name="email" id="email" value="<?PHP echo $userInfo[0]["email"]; ?>"><br />
<label for="theme">Page theme: </label><input type="text" name="theme" id="theme" value="<?PHP echo $userInfo[0]["theme"]; ?>"><br />
<label for="bio">Childs bio: </label><input type="text" name="bio" id="bio" value="<?PHP echo $userInfo[0]["bio"]; ?>"><br />
Choose an avatar for your child<br />
<br /><br /><br /><br />
<label for="password">Parents password: </label><input type="password" name="password" id="password" required="required"><br />
<label for="password2">Renter Password: </label><input type="password" name="password2" id="password2" required="required"><br />
<input type="submit" value="Save"><br />


<?PHP

$username = $userInfo[0]["username"];

// Print jokes on users page
$sql = "SELECT * FROM jokes WHERE forUser = '$username' ORDER BY id DESC" ;
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo '<strong>' . $row["fromName"] . ':</strong> ' . $row["joke"] . '<BR />';
        echo 'Answer: ' . $row["answer"] . '<BR />';
    }
} else {
    echo "No jokes yet";
}




}
else
{
?>

<FORM METHOD="POST" ACTION="?login=true">
<STRONG>Login</STRONG><BR />
<label for="username">Childs username: </label><input type="text" name="username" id="username" required="required" value="<?PHP if(empty($user)) {} elseif ($user == "www") {} elseif ($user == "jokeswaps") {} else { echo $user; } ?>"><br />
<label for="password">Parents password: </label><input type="password" name="password" id="password" required="required"><br />
<input type="submit" value="Login"><br />
</FORM>


<FORM METHOD="POST" ACTION="?signup=true">
<STRONG>Signup</STRONG><BR />
<label for="username">Childs username: </label><input type="text" name="username" id="username" required="required"><br />
<label for="password">Parents password: </label><input type="password" name="password" id="password" required="required"><br />
<label for="password2">Renter Password: </label><input type="password" name="password2" id="password2" required="required"><br />


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