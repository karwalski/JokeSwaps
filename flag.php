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
	
	
	
// Script to process flagged jokes


if(isset($_GET["jokeID"]) && isset($_GET["reason"]))
{
	
	$JokeID = $_GET["jokeID"];
	$reason = $_GET["reason"];
	
 $sql = "INSERT INTO flags (JokeID, reason)
 VALUES ('$JokeID', '$reason')";

 if ($conn->query($sql) === TRUE) {
	 // Insert success, return ajax response
echo 'success';
} else {
		     echo "Error: " . $sql . "<br>" . $conn->error;
		 }

	
	echo 'completed script';
	
	
?>