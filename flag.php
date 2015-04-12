<HTML>
<BODY>
<H1> Flag Script </H1>

<?PHP
	


	echo 'Running flag script';

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
	
	echo 'checkpoint 1';
	
// Script to process flagged jokes


if(isset($_GET["jokeid"]) && isset($_GET["reason"]))
{

	echo 'checkpoint 2';
	
	$JokeID = mysqli_real_escape_string($conn, $_GET["jokeID"]);
	$reason = mysqli_real_escape_string($conn, $_GET["reason"]);
	
 $sql = "INSERT INTO flags (JokeID, reason)
 VALUES ('$JokeID', '$reason')";

 if ($conn->query($sql) === TRUE) {
	 // Insert success return ajax response
echo 'success';
} else {
		     echo "Error: " . $sql . "<br>" . $conn->error;
		 }

}	
	echo 'completed script';
	

$conn->close();
	
?>

</BODY>
</HTML>