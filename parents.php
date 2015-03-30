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