<HTML>
<HEAD>
<TITLE>
</TITLE>
</HEAD>
<BODY>

<?PHP

// Temp DB editor script

echo 'This is the temp DB Editor script, take caution in using';

if (!isset($_POST["a"]) && $_POST["a"] == "runDBscript" )
{
echo '
<form action="#" method="post">
<input type="hidden" name="a" value="runDBscript"
<input type="submit" value="Run script">
</form>';

}
else
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
$sql = "CREATE TABLE MyGuests (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
firstname VARCHAR(30) NOT NULL,
lastname VARCHAR(30) NOT NULL,
email VARCHAR(50),
reg_date TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table MyGuests created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();


}

?>