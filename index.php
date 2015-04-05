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

// Check SQL user


$user = substr($_SERVER['HTTP_HOST'], 0, strpos($_SERVER['HTTP_HOST'], "."));
echo '<BR /> User: ' . $user;

 $user = mysqli_real_escape_string($conn, $user);

$sql = "SELECT * FROM users WHERE username = '$user'" ;
$result = $conn->query($sql);

for ($userInfo = array (); $row = $result->fetch_assoc(); $userInfo[] = $row);

print_r($userInfo);




?>