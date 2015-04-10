<HTML>
<HEAD>
<TITLE>
</TITLE>
</HEAD>
<BODY>

<?PHP

// Temp DB editor script

echo 'This is the temp DB Editor script, take caution in using';

if (isset($_POST["a"]))
{




	
	
// Script running
echo 'The script is running';

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




// sql to create table
$sql = "CREATE TABLE emailQueue (
EmailID INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
username VARCHAR(255),
TokenID INT,
sent TINYINT,
LastModified TIMESTAMP
)";



if ($conn->query($sql) === TRUE) {
    echo "Table emailQueue created successfully";
} else {
    echo "Error creating table: " . $conn->error;
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