<HTML>
<HEAD>
<TITLE>
Saving Your Joke!
</TITLE>
</HEAD>
<BODY>



<?PHP

require_once("funcaptcha.php");
$servername = "localhost";
$username = "root";
$password = "YmQCl60qMwe2YpUn34k7";
$dbname = "jokeswaps";



if (isset($_GET['for']))
{
$funcaptcha = new FUNCAPTCHA();
$verified = $funcaptcha->checkResult("E1A7B6DB-4779-5670-933E-464FB325E22D");

if ($verified)
{
if ($_POST['secret'] == 'bee')
{


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



$forUser =  $_GET['for'];
 $forUser = mysqli_real_escape_string($conn, $forUser);
$fromName = $_POST['name'];
 $fromName = mysqli_real_escape_string($conn, $fromName);
$joke = $_POST['joke'];
 $joke = mysqli_real_escape_string($conn, $joke);
$answer = $_POST['answer'];
 $answer = mysqli_real_escape_string($conn, $answer);
$fromIP = $_SERVER['REMOTE_ADDR'];
 $fromIP = mysqli_real_escape_string($conn, $fromIP);



// Insert new joke
$sql = "INSERT INTO jokes (forUser, fromName, joke, answer, fromIP)
VALUES ('$forUser', '$fromName', '$joke', '$answer', '$fromIP')";

if ($conn->query($sql) === TRUE) {
    echo "New joke saved successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();


echo '<script>window.location.href = "http://' . $forUser . '.jokeswaps.com/";</script>';


}
else
{
echo 'Error: You got the secret wrong! Are you sure you are meant to be here?';
}

}
else
{
echo 'Error: We think you are a robot! You didn\'t complete the verification';
}

}
else
{
echo 'Error: Wrong way, go back!<BR />You cannot access this page directly';
}



?>


</BODY>
</HTML>
