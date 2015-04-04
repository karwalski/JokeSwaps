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


// Signup form submit
if (isset($_GET['signup']) && $_GET['signup'] == "true")
{

$username = $_POST["username"];
 $username = mysqli_real_escape_string($conn, $username);
$email = $_POST["email"];
 $email = mysqli_real_escape_string($conn, $email);
$bio = $_POST["bio"];
 $bio = mysqli_real_escape_string($conn, $bio);

$theme = $_POST["theme"];
$avatar = $_POST["avatar"];


$hash = crypt($_POST["password"], (sprintf("$2a$%02d$", 10) . strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.')));


// Save user
$sql = "INSERT INTO users (username, password, email, theme, bio, avatar)
VALUES ('$username', '$hash', '$email', '$theme', '$bio', '$avatar')";

if ($conn->query($sql) === TRUE) {
    echo 'Account created for ' . $username . '!';
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}


}




// Login
if (isset($_GET['login']) && $_GET['login'] == "true")
{

echo 'Checkpoint 1';

$username = $_POST["username"];
 $username = mysqli_real_escape_string($conn, $username);

$sql = "SELECT * FROM users WHERE username = '$username'" ;
$result = $conn->query($sql);


for ($userInfo = array (); $row = $result->fetch_assoc(); $userInfo[] = $row);

$hash = $userInfo[0]["password"];
echo 'hash :' . $hash . '<BR />';
echo 'Checkpoint 2';
$hash2 = crypt($_POST["password"], $hash);

echo 'hash2 :' . $hash2 . '<BR />';

if ( hash_equals($hash, crypt($_POST["password"], $hash)) ) {

echo 'Checkpoint 3';

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

</HEAD>
<BODY>
<H1>This is the parents Console</H1>

<FORM METHOD="POST" ACTION="?login=true">
<STRONG>Login</STRONG><BR />
<label for="username">Childs username: </label><input type="text" name="username" id="username"><br />
<label for="password">Parents password: </label><input type="password" name="password" id="password"><br />
<input type="submit" value="Login"><br />
</FORM>


<FORM METHOD="POST" ACTION="?signup=true">
<STRONG>Signup</STRONG><BR />
<label for="username">Childs username: </label><input type="text" name="username" id="username"><br />
<label for="password">Parents password: </label><input type="password" name="password" id="password"><br />
<label for="password2">Renter Password: </label><input type="password" name="password2" id="password2"><br />


<label for="password">Parents email: </label><input type="email" name="email" id="email"><br />

<label for="theme">Page theme: </label><input type="text" name="theme" id="theme"><br />

<label for="bio">Childs bio: </label><input type="text" name="bio" id="bio"><br />
Choose an avatar for your child<br />
<br /><br /><br /><br />

I declare I am a responsible adult, parent or guardian giving permission for this child to receive a JokeSwaps profile page.<br />
I declare I have discussed the site rules with the child, and they understand the rules.<br />
I declare I will be supervising the child when using this website.<br />
I have read the rules and privacy policy for this website.<br />
<input type="submit" value="Signup"><br />
</FORM>

<br /><br /><br />
// If signed in<br />
Childs username (cannot be changed): <br />


Update settings<br />
<FORM METHOD="POST" ACTION="?update=true">
<label for="theme">Page theme: </label><input type="text" name="theme" id="theme"><br />
<label for="bio">Childs bio: </label><input type="text" name="bio" id="bio"><br />
Choose an avatar for your child<br />
<br /><br /><br /><br />
<label for="password">Parents password: </label><input type="password" name="password" id="password"><br />
<label for="password2">Renter Password: </label><input type="password" name="password2" id="password2"><br />
<input type="submit" value="Save"><br />


<?PHP

// Print jokes on users page
$sql = "SELECT * FROM jokes WHERE forUser = 'felix' ORDER BY id DESC" ;
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

$conn->close();


?>

</BODY>
</HTML>