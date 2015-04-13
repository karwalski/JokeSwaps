<HTML>
<HEAD>
<TITLE>
</TITLE>
</HEAD>
<BODY>

<?PHP

// Temp DB editor script

echo 'This is the temp DB Editor script, take caution in using<BR /> <BR />';

if (isset($_POST["a"]))
{




	
	
// Script running
echo 'The script is running....<BR /> <BR />';

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


/*
// sql command
$sql = "";

*/

$sql = "UPDATE jokes SET type = 'question' WHERE type IS NULL;";



if ($conn->query($sql) === TRUE) {
    echo "SQL command ran successfully";
} else {
    echo "Error running SQL command: " . $conn->error;
}

$conn->close();



}
else
{
echo '
<form action="#" method="post">
<input type="hidden" name="a" value="runDBscript">
<input type="submit" value="Run script">
</form>';

}

?>

</BODY>
</HTML>